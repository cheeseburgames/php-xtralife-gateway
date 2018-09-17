<?php
namespace CheeseBurgames\Xtralife {
	
	trait Base {
		
		public static $requestTimeout = 5;
		
		public static function query($route, $path = '', $usePublicDomain = true) {
			return self::_request('GET', $route, $path, $usePublicDomain, NULL);
		}
		
		public static function queryJSON($route, $path = '', $usePublicDomain = true) {
			$response = self::query($route, $path, $usePublicDomain);
			return ($response === false) ? false : json_decode($response, true, 512, JSON_BIGINT_AS_STRING);
		}
		
		public static function post($route, $path, $usePublicDomain, $body) {
			return self::_request('POST', $route, $path, $usePublicDomain, $body);
		}
		
		public static function postJSON($route, $path, $usePublicDomain, $body, $jsonOptions = JSON_NUMERIC_CHECK) {
			return self::post($route, $path, $usePublicDomain, json_encode($body, $jsonOptions));
		}
		
		public static function put($route, $path, $usePublicDomain, $body) {
			return self::_request('PUT', $route, $path, $usePublicDomain, $body);
		}
		
		public static function putJSON($route, $path, $usePublicDomain, $body, $jsonOptions = JSON_NUMERIC_CHECK) {
			return self::put($route, $path, $usePublicDomain, json_encode($body, $jsonOptions));
		}
		
		public static function delete($route, $path, $usePublicDomain) {
			return self::_request('DELETE', $route, $path, $usePublicDomain, NULL);
		}
		
		private static function _request($method, $route, $path, $usePublicDomain, $body) {
			$url = self::_prepareURL($route, $path, $usePublicDomain);
			
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, self::$requestTimeout);
			curl_setopt($ch, CURLOPT_HTTPHEADER, self::_buildHeaders($body));
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			if ($body !== NULL) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
			}
			
			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			return ($httpCode == 200) ? $response : false;
		}
		
	}
	
}
