<?php
if(!class_exists('SARequest')){
	class SARequest {
		public function __construct() {
		}
		/**
		 * 파라미터를 얻는다.
		 *
		 * @param string $name        	
		 * @return object
		 */
		public static function getParameter($name,$defaultValue='') {
			if (! isset ( $_REQUEST [$name] )){
				return $defaultValue;
			}
			
			return $_REQUEST [$name];
		}
		
		public static function getParameterNames() {
			return array_keys ( $_REQUEST );
		}
		
		public static function getUploadedFile($name) {
			if (! isset ( $_FILES [$name] ))
				return _null ();
			
			if (is_array ( $_FILES [$name] ['name'] )) {
				$result = array ();
				foreach ( $_FILES [$name] ['name'] as $key => $name ) {
					$file = new UploadedFile ( $name, $_FILES [$name] ['tmp_name'] [$key], $_FILES [$name] ['size'] [$key], $_FILES [$name] ['type'] [$key], $_FILES [$name] ['error'] [$key] );
					$result [] = & $file;
				}
				return $result;
			} else {
				$file = new SAUploadedFile ( $_FILES [$name] ['name'], $_FILES [$name] ['tmp_name'], $_FILES [$name] ['size'], $_FILES [$name] ['type'], $_FILES [$name] ['error'] );
				return $file;
			}
		}
		
		public static function getUploadedFiles() {
			$result = array ();
			foreach ( ( array ) array_keys ( $_FILES ) as $name ) {
				$result [$name] = self::getUploadedFile ( $name );
			}
			return $result;
		}
		
		public static function getLocaleFromHeaders() {
			$languages = preg_replace ( '/(;q=.+)/i', '', trim ( $_SERVER ['HTTP_ACCEPT_LANGUAGE'] ) );
			
			list ( $variant, $lang ) = explode ( ',', $languages );
			list ( $lang2, $country ) = explode ( '-', $variant );
			
			return array (
					$lang,
					$country,
					$variant 
			);
		}
		
		public static function getContextPath() {
			$path = (isset ( $_SERVER ['PATH_INFO'] )) ? $_SERVER ['PATH_INFO'] : @getenv ( 'PATH_INFO' );
			if ($path != '' and $path != "/" . SELF) {
				return $path;
			}
			
			$path = (isset ( $_SERVER ['ORIG_PATH_INFO'] )) ? $_SERVER ['ORIG_PATH_INFO'] : @getenv ( 'ORIG_PATH_INFO' );
			if ($path != '' and $path != "/" . SELF) {
				return $path;
			}
			
			return $path;
		}
		
		public static function getContextRoot() {
			$requestUri = self::getRequestURI ();
			$requestUri = str_replace ( self::getContextPath (), '', $requestUri );
			if (strpos ( $requestUri, '?' ))
				$requestUri = substr ( $requestUri, 0, strpos ( $requestUri, '?' ) );
			
			if (strpos ( $requestUri, '.' )) {
				$requestUri = substr ( $requestUri, 0, strpos ( $requestUri, '.' ) );
				$requestUri = substr ( $requestUri, 0, strrpos ( $requestUri, '/' ) );
			}
			
			return $requestUri;
		}
		
		public static function getDomainRoot() {
			$protocol = self::isSecure () ? 'https' : 'http';
			$hostname = self::getServerName ();
			$port = self::getServerPort () == 80 ? "" : ":" . self::getServerPort ();
			return $protocol . '://' . $hostname . $port;
		}
		
		public static function getCurrentURL() {
			return self::getDomainRoot().self::getRequestURI();
		}
		
		public static function isSecure() {
			return isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on';
		}
		
		public static function getServerName() {
			return $_SERVER['SERVER_NAME'];
		}
		
		public static function getServerPort() {
			return $_SERVER['SERVER_PORT'];
		}
		
		public static function getRemoteIp(){
			return $_SERVER['REMOTE_ADDR'];
		}
		
		/**
		 * 세션에 저장된 객체를 얻는다.
		 *
		 * @param string $name        	
		 * @return object
		 */
		public static function getAttribute($name) {
			if (! isset ( $_SESSION [$name] ))
				return null;
			
			return $_SESSION [$name];
		}
		
		/**
		 * 세션에 객체를 저장한다.
		 *
		 * @param string $key        	
		 * @param string $value        	
		 */
		public static function setAttribute($key, $value) {
			$_SESSION [$key] = $value;
		}
		
		public static function setErrorMessage($name,$message){
			self::setAttribute('error_'.$name, $message);
		}
		
		public static function setSessionMessage($name,$message){
			self::setAttribute('session_message_'.$name, $message);
		}
	
		public static function deleteAttribute($name){
			unset($_SESSION[$name]);
		}
		
		public static function getSessionMessage($name){
			$output = self::getAttribute('session_message_'.$name);
			return $output;
		}
		
		public static function outputSessionMessage($name,$html='div',$html_option=array()){
			$message = self::getSessionMessage($name);
			self::deleteAttribute('session_message_'.$name);
			
			$html_option['class'] = $name.' session_message';
	
			$result = '';
			
			if(!empty($message)){
				$result .= sa_html($html,$html_option);
				$result .= $message;
				$result .= _sa_html($html);
				
				return $result;
			}
		}
		
		/**
		 * 쿼리 스트링을 얻는다.
		 *
		 * @return string
		 */
		public static function getQueryString() {
			return $_SERVER ['QUERY_STRING'];
		}
		
		/**
		 *
		 * @return string
		 */
		public static function getRemotePort() {
			return $_SERVER ['REMOTE_PORT'];
		}
		
		/**
		 *
		 * @return string
		 */
		public static function getUserAgent() {
			return $_SERVER ['HTTP_USER_AGENT'];
		}
		
		/**
		 *
		 * @return string
		 */
		public static function getDocumentRoot() {
			return $_SERVER ['DOCUMENT_ROOT'];
		}
		
		/**
		 *
		 * @return string
		 */
		public static function getMethod() {
			return $_SERVER ['REQUEST_METHOD'];
		}
		
		/**
		 * 요청 URI를 얻는다.
		 *
		 * @return string
		 */
		public static function getRequestURI() {
			return $_SERVER ['REQUEST_URI'];
		}
		
		/**
		 * 쿠키를 얻는다
		 *
		 * @return $_COOKIE
		 */
		public static function getCookies() {
			return $_COOKIE;
		}
		
		/**
		 * 쿠키를 얻는다.
		 *
		 * @param string $name        	
		 * @return $_COOKIE[$name];
		 */
		public static function getCookie($name) {
			return $_COOKIE [$name];
		}
		
		/**
		 * 쿠키를 설정한다.
		 *
		 * @param string $name        	
		 * @param string $value        	
		 * @param time $expire        	
		 * @param string $path        	
		 * @param string $domain        	
		 * @param boolean $secure        	
		 * @param boolean $httponly        	
		 */
		public static function setCookie($name, $value, $expire = 0, $path = '', $domain = '/', $secure = false, $httponly = false) {
			setcookie ( $name, $value, $expire, $path, $domain, $secure, $httponly );
		}
		
		/**
		 *
		 * @param string $name        	
		 */
		public static function deleteCookie($name) {
			if (isset ( $_COOKIE [$name] )) {
				unset ( $_COOKIE [$name] );
				setcookie ( $name, '', time () - 3600 );
			}
		}
	}
}