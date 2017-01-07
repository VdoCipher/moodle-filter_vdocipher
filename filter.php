<?php
class filter_vdocipher extends moodle_text_filter {
	private static $csk;
	private static $watermark;
	public function filter($text, array $options = array()){
		self::$csk = get_config('filter_vdocipher', 'csk');
		self::$watermark = get_config('filter_vdocipher', 'watermark');
		return preg_replace_callback( '/\[vdo\s+([A-Za-z0-9\=\s]+)\]/' , function($matches) use ($csk){
			if (is_null(self::$csk) || self::$csk === "") {
				return "Plugin not set properly. Please enter API key.";
			}
			$this->access_key = $this->params['access_key'];
			$attrs = array();
			$output = preg_replace_callback( '/\b([a-zA-Z0-9]+)\=([A-Za-z0-9]+)\b/' , function($matches) use(&$attrs){
					$attrs[$matches[1]] = $matches[2];
				} , $matches[1]);
			$params = array(
				'video'=>$attrs['id'],
			);
			$height = $attrs['height'];
			$width = $attrs['width'];

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
<div id="vdo$OTP" style="height:720px;width:1280px;max-width:100%;"></div>
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
