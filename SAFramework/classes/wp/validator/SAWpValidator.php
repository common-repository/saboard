<?php
class SAWpValidator extends SAValidator {
	public function isSafeJoinMail($name) {
		$check = true;
		
		$mail = $this->validateItem [$name];
		
		$message = '';
		
		if (strlen ( trim ( $mail ) ) == 0) {
			$message = '이메일은 필수입력입니다.';
			$check = false;
		}
		
		if (! is_email ( $mail )) {
			$message = '이메일을 올바르게 입력하세요.';
			$check = false;
		}
		
		if (email_exists ( $mail )) {
			$message = '이미 존재하는 이메일입니다..';
			$check = false;
		}
		
		if (! $check) {
			$this->pushError ( $name, $message );
		}
	}
	
	public function isSafeUserName($name) {
		$check = true;
		
		$user_name = $this->validateItem [$name];
		
		$message = '';
		$error_code = 0;
		
		if (username_exists ( $user_name )) {
			$message = '이미 존재하는 아이디 입니다.';
			$error_code = 1;
			
			$check = false;
		}
		
		if (! validate_username ( $user_name )) {
			$message = '잘못된 아이디 입니다.';
			$error_code = 2;
			
			$check = false;
		}
		
		if (isset ( $user_name ) && strlen ( trim ( $user_name ) ) == 0) {
			$message = '아이디는 필수입력입니다.';
			$error_code = 3;
			
			$check = false;
		}
		
		if (! $check) {
			$this->pushError ( $name, $message ,$error_code);
		}
	}
	public function isSafePassword($name) {
		$this->minLength ( $name, 5 );
	}
}
