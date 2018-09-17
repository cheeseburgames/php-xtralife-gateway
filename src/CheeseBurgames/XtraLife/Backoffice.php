<?php
namespace CheeseBurgames\XtraLife {

	$requiredConstants = array(
			'XTRALIFE_BACKOFFICE_USER',
			'XTRALIFE_BACKOFFICE_PASS',
			'XTRALIFE_BACKOFFICE_ENDPOINT',
			'XTRALIFE_BACKOFFICE_GAME',
			'XTRALIFE_PUBLIC_DOMAIN'
	);
	foreach ($requiredConstants as $c) {
		if (!defined($c)) {
			throw new \Exception("Constant '{$c}' is not defined.");
		}
	}
	
	class Backoffice {
	
		use Base;
	
		private static function _buildHeaders($body = NULL) {
			$authData = base64_encode(sprintf("%s:%s", XTRALIFE_BACKOFFICE_USER, XTRALIFE_BACKOFFICE_PASS));
			$headers = array(
					"Content-Type: application/json",
					"Authorization: Basic {$authData}"
			);
			if ($body !== NULL) {
				$headers["Content-Length"] = strlen($body);
			}
			return $headers;
		}
	
		private static function _prepareURL($route, $path, $usePublicDomain) {
			$path = str_replace(':domain', $usePublicDomain ? XTRALIFE_PUBLIC_DOMAIN : 'private', $path);
	
			$url = XTRALIFE_BACKOFFICE_ENDPOINT;
			if (!preg_match('#/$#', $url)) {
				$url .= '/';
			}
			$url .= "{$route}/" . XTRALIFE_BACKOFFICE_GAME;
			if (!empty($path)) {
				if (!preg_match('#^/#', $path)) {
					$url .= '/';
				}
				$url .= $path;
			}
	
			return $url;
		}
	
	}
	
}