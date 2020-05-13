<?php

namespace Model\Core;

class Core {

	public static function mustache($mustache = [], $mask = ''){
		$view = str_replace(array_keys($mustache), array_values($mustache), $mask);
		return $view;
	}
	public static function strip_tags($strig = ''){
		return strip_tags($strig);
	}
	public static function ucwords($string = ''){
		return ucwords($string);
	}
	public static function ucfirst($string = ''){
		return ucfirst($string);
	}
	public static function strtotime($date = 'today'){
		$date = ($date == 'today') ? date('d-m-Y') : $date;
		return strtotime($date);
	}
	public static function strtolower($string = ''){
		return strtolower($string);
	}
	public static function base64_encode($string = ''){
		return base64_encode($string);
	}
	public static function base64_decode($string = ''){
		return base64_decode($string);
	}
	public static function date($mask = ''){
		$mask = ($mask == '') ? 'd/m/Y' : $mask;
		return date($mask);
	}
	public static function datemask($date = 'today', $mask = 'd/m/Y'){
		$date = ($date == 'today') ? date('d-m-Y') : $date;
		return self::date($mask, self::strtotime($date));
	}
	public static function number_format($number = 0, $decimals = 2, $dec_point = ',', $thousands_sep = '.'){
		return number_format($number, $decimals, $dec_point, $thousands_sep);
	}
	public static function ip(){
		return $_SERVER['REMOTE_ADDR'] ?? '';
	}


	/**
	 * Verifica se il valore è
	 * un'e-mail
	 *
	 * @param mixed $email
	 * @return boolean
	 */
	public static function is_email($email = ''){
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) return true;
	}
}