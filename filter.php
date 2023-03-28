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
    public function filter($text, array $options = array())
    {
        self::$csk = get_config('filter_vdocipher', 'csk');
        self::$playerVersion = get_config('filter_vdocipher', 'playerVersion');
        self::$playerTheme = get_config('filter_vdocipher', 'playerTheme');
        self::$width = get_config('filter_vdocipher', 'width');
        self::$height = get_config('filter_vdocipher', 'height');
        self::$watermark = get_config('filter_vdocipher', 'watermark');
        // by default, use player version 2
        if (!self::$playerVersion) {
            self::$playerVersion = '2.x';
        }
        // if using player version 1 but theme is not available then use the default theme
        if (self::$playerVersion === '1.x' && strlen(self::$playerTheme) !== 32) {
            self::$playerTheme = '9ae8bbe8dd964ddc9bdb932cca1cb59a';
        }
        if (is_null(self::$csk) || empty(self::$csk)) {
            return 'Plugin not configured. API Key missing.';
        } else if (strlen(self::$csk) !== 64) {
            return 'Invalid API Key.';
        }
        if (strpos($text, '[vdo ') === false) {
            // the text does not contain shortcode. skip this
            return $text;
        }
        return preg_replace_callback('/\[vdo\s+([A-Za-z0-9\=\s\"\']+)\]/', function ($matches) {
            $attrs = array();
            $regex = '/\b([a-zA-Z0-9]+)\=[\"\']*([A-Za-z0-9]+)[\"\']*\b/';
            $output = preg_replace_callback($regex, function ($matches) use (&$attrs) {
                $attrs[$matches[1]] = $matches[2];
            }, $matches[1]);
            // now, we have the shortcode attributes saved in $attrs

            if (!isset($attrs['id'])) {
                return "Required argument id for embedded video not found.";
            } elseif (strlen($attrs['id']) !== 32) {
                return "Invalid Video Id.";
            } else {
                $videoId = $attrs['id'];
            }

            $setting_width = self::$width ?: '1280';
            $setting_height = self::$height ?: 'auto';
            $width = isset($attrs['width']) ? $attrs['width'] : $setting_width;
            $height = isset($attrs['height']) ? $attrs['height'] : $setting_height;
            if (substr($width, -2) !== 'px') {
                $width .= 'px';
            }
            if ((substr($height, -2) !== 'px') && ($height !== 'auto')) {
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
            if (isset($otp_response->error)) {
                return $otp_response->error;
            }
            $otp = $otp_response->otp;
            $playbackInfo = $otp_response->playbackInfo;
            if (is_null($otp)) {
                return "Video playback can not be authenticated.";
            }
            if (self::$playerVersion === '1.x') {
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
            } else {
                $uniq = 'u' . rand();
                $url = "https://player.vdocipher.com/v2/?otp=$otp&playbackInfo=$playbackInfo";
                $playerId = self::$playerTheme;
                if (strlen($playerId) === 16) {
                    $url .= "&player=$playerId";
                }
                if (isset($attrs['autoplay']) && $attrs['autoplay']) {
                    $url .= "&autoplay=true";
                }
                if (isset($attrs['loop']) && $attrs['loop']) {
                    $url .= "&loop=true";
                }
                if (isset($attrs['controls']) && in_array($attrs['controls'], ['off', 'native'])) {
                    $url .= "&controls=" . $attrs['controls'];
                }
                if (isset($attrs['cc_language']) && $attrs['cc_language']) {
                    $url .= "&ccLanguage=" . $attrs['cc_language'];
                }
                if (isset($attrs['litemode']) && $attrs['litemode'] === 'true') {
                    $url .= "&litemode=true";
                }
                $output = <<<END
<script src="https://player.vdocipher.com/v2/api.js"></script>
<iframe
  src="$url"
  id="$uniq"
  style="height:$height;width:$width;max-width:100%;border:0;display: block;"
  allow="encrypted-media"
  allowfullscreen
></iframe>
<script>
(function() {
  const iframe = document.querySelector('#$uniq');
  const player = VdoPlayer.getInstance(iframe);
  const isAutoHeight = () => iframe.style.height === 'auto' && iframe.style.width.endsWith('px');
  const setAspectRatio = (ratio) => {
      iframe.style.maxHeight = '100vh';
      if (CSS.supports('aspect-ratio', 1)) {
          iframe.style.aspectRatio = ratio;
      } else {
          const offsetWidth = iframe.offsetWidth;
          iframe.style.height = Math.round(offsetWidth / ratio) + 'px';
      }
  }
  if (isAutoHeight()) {
    if (iframe.src.includes('litemode'))  {
       setAspectRatio(16/9);
    }
    player.video.addEventListener('loadstart', async () => {
      const aspectRatio = (await player.api.getMetaData()).aspectRatio;
      setAspectRatio(aspectRatio);
    });
  }
})();
</script>
END;
            }
            return $output;
        }, $text);
    }

    private function vdo_otp($video, $otpPostParams = [])
    {
        $client_key = self::$csk;
        $url = "https://dev.vdocipher.com/api/videos/$video/otp";
        $postParams = json_encode($otpPostParams);
        $headers = [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Apisecret {$client_key}"
        ];
        $options = [
            'CURLOPT_HTTPHEADER' => $headers,
            'CURLOPT_FAILONERROR' => true,
            'CURLOPT_RETURNTRANSFER' => true,
        ];
        $curl = new \curl();
        $response = $curl->post($url, $postParams, $options);
        if ($curl->error) {
            return (object)['error' => $curl->error];
        }
        return json_decode($response);
    }
}
