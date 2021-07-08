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
 * @copyright 2019, VdoCipher Media Solutions <info@vdocipher.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Main filter class
 */
class filter_vdocipher extends moodle_text_filter
{
    private static $csk;
    private static $watermark;
    private static $playerVersion;
    private static $playerTheme;
    private static $width;
    private static $height;
    private static $speedOptions;
    public function filter($text, array $options = array())
    {
        self::$csk = get_config('filter_vdocipher', 'csk');
        self::$playerVersion = get_config('filter_vdocipher', 'playerVersion');
        self::$playerTheme = get_config('filter_vdocipher', 'playerTheme');
        self::$width = get_config('filter_vdocipher', 'width');
        self::$height = get_config('filter_vdocipher', 'height');
        if (!self::$playerVersion) {
            self::$playerVersion = '1.x';
        }
        if (!self::$playerTheme) {
            self::$playerTheme = '9ae8bbe8dd964ddc9bdb932cca1cb59a';
        }
        self::$watermark = get_config('filter_vdocipher', 'watermark');
        self::$speedOptions = get_config('filter_vdocipher', 'speedOptions');
        if (strpos($text, '[vdo ') === false) {
            return $text;
        }
        return preg_replace_callback('/\[vdo\s+([A-Za-z0-9\=\s\"\']+)\]/', function ($matches) {
            if (is_null(self::$csk) || self::$csk === "") {
                return "Plugin not set properly. Please enter API key.";
            }
            $attrs = array();
            $regex = '/\b([a-zA-Z0-9]+)\=[\"\']*([A-Za-z0-9]+)[\"\']*\b/';
            $output = preg_replace_callback($regex, function ($matches) use (&$attrs) {
                $attrs[$matches[1]] = $matches[2];
            }, $matches[1]);

            $videoId = $attrs['id'];

            if (!self::$width) {
                $setting_width = '720';
            } else {
                $setting_width = self::$width;
            }
            if (!self::$height) {
                $setting_height = auto;
            } else {
                $setting_height = self::$height;
            }

            $width = (isset($attrs['width'])) ? $attrs['width'] : $setting_width;
            $height = (isset($attrs['height'])) ? $attrs['height'] : $setting_height;
            if (substr($width, -2) !== 'px') {
                $width .= 'px';
            }
            if ((substr($height, -2) !== 'px') && ($height !== 'auto') ) {
                $height .= 'px';
            }

            $otp_post_array = [];
            $otp_post_array["ttl"] = 300;

            if (!function_exists("eval_date")) {
                function eval_date($matches)
                {
                    return date($matches[1]);
                }
            }
            if (!empty(self::$watermark)) {
                global $USER;
                $annotatecode = self::$watermark;
                if (isset($USER) && !is_null($USER)) {
                    $fullname = $USER->firstname . ' ' . $USER->middlename . ' ' . $USER->lastname;
                    $annotatecode = str_replace('{name}', $fullname . ' ', $annotatecode);
                    $annotatecode = str_replace('{email}', $USER->email, $annotatecode);
                    $annotatecode = str_replace('{username}', $USER->username, $annotatecode);
                    $annotatecode = str_replace('{id}', $USER->id, $annotatecode);
                }
                $annotatecode = str_replace('{ip}', $_SERVER['REMOTE_ADDR'], $annotatecode);
                $annotatecode = preg_replace_callback('/\{date\.([^\}]+)\}/', "eval_date", $annotatecode);
                if (!isset($attrs['no_annotate'])) {
                    $otp_post_array["annotate"] = $annotatecode;
                }
            }

            $otp_response = $this->vdo_otp($videoId, $otp_post_array);
            $otp = $otp_response->otp;
            $playbackInfo = $otp_response->playbackInfo;
            if (is_null($otp)) {
                return "Video playback can not be authenticated.";
            }
            if (self::$playerVersion === '0.5') {
                $output = <<<EOF
<div id="vdo$otp" style="height:$height;width:$width;max-width:100%;"></div>
    <script>
    (function(v,i,d,e,o){v[o]=v[o]||{}; v[o].add = v[o].add || function V(a){ (v[o].d=v[o].d||[]).push(a);};
    if(!v[o].l) { v[o].l=1*new Date(); a=i.createElement(d), m=i.getElementsByTagName(d)[0];
    a.async=1; a.src=e; m.parentNode.insertBefore(a,m);}
    })(window,document,'script','//de122v0opjemw.cloudfront.net/vdo.js','vdo');
    vdo.add({
        o: "$otp",
    });
</script>
EOF;
            } else {
                $playerVersion = self::$playerVersion;
                $playerTheme = self::$playerTheme;
                $output = <<<EOF
<div id="vdo$otp" style="height:$height;width:$width;max-width:100%;"></div>
    <script>
  (function(v,i,d,e,o){v[o]=v[o]||{}; v[o].add = v[o].add || function V(a){ (v[o].d=v[o].d||[]).push(a);};
if(!v[o].l) { v[o].l=1*new Date(); a=i.createElement(d), m=i.getElementsByTagName(d)[0];
a.async=1; a.src=e; m.parentNode.insertBefore(a,m);}
})(window,document,"script","https://d1z78r8i505acl.cloudfront.net/playerAssets/$playerVersion/vdo.js","vdo");
vdo.add({
  otp: "$otp",
  playbackInfo: "$playbackInfo",
  theme: "$playerTheme",
  container: document.querySelector( "#vdo$otp" ),
});
	</script>
EOF;
            }
            if (self::validSpeed()) {
                $speedOptions = self::$speedOptions;
                $output .= <<<EOF
<script>
    function onVdoCipherAPIReady() {
      var allVideos = vdo.getObjects();
      var video_ = allVideos[allVideos.length - 1];
      video_.addEventListener('load', function () {
        video_.availablePlaybackRates = [$speedOptions]
      });
    }
</script>
EOF;

            }
            return $output;
        }, $text);
    }

    private static function validSpeed() {
        if (!self::$speedOptions) return false;
        $speeds = explode(',', self::$speedOptions);
        foreach ($speeds as $speed) {
            if (!is_numeric($speed)) return false;
            $floatSpeed = floatval($speed);
            if ($floatSpeed < 0.2 || $floatSpeed > 2.5) return false;
        }
        return true;
    }

    private function vdo_otp($video, $otp_post_array = [])
    {
        $client_key = self::$csk;


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        $headers = [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Apisecret {$client_key}"
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $otp_post_json = json_encode($otp_post_array);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $otp_post_json);
        $url = "https://dev.vdocipher.com/api/videos/$video/otp";
        curl_setopt($curl, CURLOPT_URL, $url);
        $html = curl_exec($curl);
        if (!$html) {
            echo curl_error($curl);
        }
        curl_close($curl);

        return json_decode($html);
    }
}
