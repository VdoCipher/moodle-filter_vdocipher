<?php
class filter_vdocipher extends moodle_text_filter {
	private static $csk;
	public function filter($text, array $options = array()){
		self::$csk = get_config('filter_vdocipher', 'csk');
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
			$OTP = $this->vdo_send("otp", $params, false);
			if (is_null(json_decode($OTP))) {
				return "Video playback can not be authenticated.";
			}
			$OTP = json_decode($OTP)->otp;
			$output = "<span id='vdo$OTP'></span>";
			$output .= "<script src='https://de122v0opjemw.cloudfront.net/utils/playerInit.php?otp=$OTP&height=$height&width=$width'></script>";
			return $output;
		}, $text );
	}
	private function vdo_send($action, $params, $posts = false){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_FAILONERROR, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);    
		//curl_setopt($curl, CURLOPT_PROXY, "xx.xx.xx.xx:xxxx");

		$getData = http_build_query($params);
		curl_setopt($curl, CURLOPT_POST, true); 
		$postData = "clientSecretKey=".self::$csk;
		if ($posts) {
			$postData .= "&". $posts;
		}
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
