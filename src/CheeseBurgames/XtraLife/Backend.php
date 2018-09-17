<?php
namespace CheeseBurgames\XtraLife {

	$requiredConstants = array(
			'XTRALIFE_BACKEND_APIKEY',
			'XTRALIFE_BACKEND_APISECRET',
			'XTRALIFE_BACKEND_ENDPOINT',
			'XTRALIFE_PUBLIC_DOMAIN'
	);
	foreach ($requiredConstants as $c) {
		if (!defined($c)) {
			throw new \Exception("Constant '{$c}' is not defined.");
		}
	}
	
	if (!defined('XTRALIFE_BACKEND_LOAD_BALANCER_COUNT')) {
		defined('XTRALIFE_BACKEND_LOAD_BALANCER_COUNT', 2);
	}
	
	class Backend {
	
		use Base;
	
		private static $gamerId = NULL;
		private static $gamerSecret = NULL;
	
		public static function setGamerCredentials($gamerId, $gamerSecret) {
			self::$gamerId = $gamerId;
			self::$gamerSecret = $gamerSecret;
		}
	
		public static function logout() {
			self::post('gamer', 'logout', false, NULL);
			self::setGamerCredentials(NULL, NULL);
		}
	
		private static function _buildHeaders($body = NULL) {
			$headers = array(
					"Content-Type: application/json",
					sprintf("x-apikey: %s", XTRALIFE_BACKEND_APIKEY),
					sprintf("x-apisecret: %s", XTRALIFE_BACKEND_APISECRET)
			);
	
			if (self::$gamerId !== NULL && self::$gamerSecret !== NULL) {
				$basicAuth = sprintf("%s:%s", self::$gamerId, self::$gamerSecret);
				$basicAuth = base64_encode($basicAuth);
				$headers[] = sprintf("Authorization: Basic %s", $basicAuth);
			}
	
			if ($body !== NULL) {
				$headers["Content-Length"] = strlen($body);
			}
			return $headers;
		}
	
		private static function _prepareURL($route, $path, $usePublicDomain) {
			$path = str_replace(':domain', $usePublicDomain ? XTRALIFE_PUBLIC_DOMAIN : 'private', $path);
	
			$maxLoadBalancerNumber = XTRALIFE_BACKEND_LOAD_BALANCER_COUNT;
			$loadBalancerNumber = mt_rand(1, $maxLoadBalancerNumber);
	
			$url = XTRALIFE_BACKEND_ENDPOINT;
			$url = sprintf($url, $loadBalancerNumber);
			if (!preg_match('#/$#', $url)) {
				$url .= '/';
			}
			$url .= "{$route}/";
			if (!empty($path)) {
				$url .= $path;
			}
	
			return $url;
		}
	
	}

}