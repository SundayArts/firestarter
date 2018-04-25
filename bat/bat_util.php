<?php
class bat_util{

	//curlでpostする
	function curl_post($url) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);

		return $response;
	}
	
	//パーセント計算
	function num2per($number, $total, $precision = 0) {
		if ($number < 0) {
			return 0;
		}
		try {
			$percent = ($number / $total) * 100; 
			return round($percent, $precision);
		} catch (Exception $e) {
		return 0;
		}
	}

}