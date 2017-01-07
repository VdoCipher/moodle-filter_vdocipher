<?php
// This file is part of VdoCipher plugin for Moodle ( moodle-filter_vdocipher ) - https://www.vdocipher.com/
//
// moodle-filter_vdocipher is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// moodle-filter_vdocipher is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with moodle-filter_vdocipher.  If not, see <http://www.gnu.org/licenses/>.
 
/**
 * @package   filter_vdocipher
 * @copyright 2017, VdoCipher Media Solutions <info@vdocipher.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Main filter class
 */
class filter_vdocipher extends moodle_text_filter {
	private static $csk;
	private static $watermark;
	public function filter($text, array $options = array()){
		self::$csk = get_config('filter_vdocipher', 'csk');
		self::$watermark = get_config('filter_vdocipher', 'watermark');
		if (strpos($text, '[vdo ') === false) {
			return $text;
		}
		return preg_replace_callback( '/\[vdo\s+([A-Za-z0-9\=\s\"\']+)\]/' , function($matches) {
			if (is_null(self::$csk) || self::$csk === "") {
				return "Plugin not set properly. Please enter API key.";
			}
			$attrs = array();
			$output = preg_replace_callback( '/\b([a-zA-Z0-9]+)\=[\"\']*([A-Za-z0-9]+)[\"\']*\b/' , function($matches) use(&$attrs){
					$attrs[$matches[1]] = $matches[2];
				} , $matches[1]);
			$params = array(
				'video'=>$attrs['id'],
			);
			$height = (isset($attrs['height'])) ? $attrs['height'] : 480;
			$width = (isset($attrs['width'])) ? $attrs['width'] : 720;
			if (substr($height, -2) !== 'px') $height .= 'px';
			if (substr($width, -2) !== 'px') $width .= 'px';

			$anno = [];
			if (!function_exists("eval_date")) {
				function eval_date($matches){
					return current_time($matches[1]);
				}
			}
			if (!empty(self::$watermark)) {
				global $USER;
				$vdo_annotate_code = self::$watermark;
				if ( !is_null($USER) ) {
					$fullname = $USER->firstname . ' ' . $USER->middlename . ' ' . $USER->lastname;
					$vdo_annotate_code = str_replace('{name}', $fullname . ' ', $vdo_annotate_code);
					$vdo_annotate_code = str_replace('{email}', $USER->email , $vdo_annotate_code);
					$vdo_annotate_code = str_replace('{username}', $USER->username , $vdo_annotate_code);
					$vdo_annotate_code = str_replace('{id}', $USER->id , $vdo_annotate_code);
				}
				$vdo_annotate_code = str_replace('{ip}', $_SERVER['REMOTE_ADDR'] , $vdo_annotate_code);
				$vdo_annotate_code = preg_replace_callback('/\{date\.([^\}]+)\}/', "eval_date" , $vdo_annotate_code);
				if(!isset($attrs['no_annotate']))
					$anno = array("annotate" => $vdo_annotate_code);
			}
			$OTP = $this->vdo_send("otp", $params, $anno);
			if (is_null(json_decode($OTP))) {
				return "Video playback can not be authenticated.";
			}
			$OTP = json_decode($OTP)->otp;
			$output =  <<<EOF
<div id="vdo$OTP" style="height:$height;width:$width;max-width:100%;"></div>
	<script>
	(function(v,i,d,e,o){v[o]=v[o]||{}; v[o].add = v[o].add || function V(a){ (v[o].d=v[o].d||[]).push(a);};
	if(!v[o].l) { v[o].l=1*new Date(); a=i.createElement(d), m=i.getElementsByTagName(d)[0];
	a.async=1; a.src=e; m.parentNode.insertBefore(a,m);}
	})(window,document,'script','//de122v0opjemw.cloudfront.net/vdo.js','vdo');
	vdo.add({
		o: "$OTP",
	});
</script>
EOF;
			return $output;
		}, $text );
	}
	private function vdo_send($action, $params, $posts = []){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_FAILONERROR, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);    
		//curl_setopt($curl, CURLOPT_PROXY, "xx.xx.xx.xx:xxxx");

		$getData = http_build_query($params);
		curl_setopt($curl, CURLOPT_POST, true); 
		$posts["clientSecretKey"] = self::$csk;
		$postData = http_build_query($posts, null, '&');
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		$url = "https://api.vdocipher.com/v2/$action/?$getData";
		curl_setopt($curl, CURLOPT_URL,$url);
		$html = curl_exec($curl);
		if (!$html) {
			echo curl_error($curl);
		}
		curl_close($curl);
		return $html;
	}
}
?>
