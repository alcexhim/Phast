<?php
	namespace Phast;
	
	class StringMethods
	{
		public static function StartsWith($haystack, $needle)
		{
			return $needle === "" || strpos($haystack, $needle) === 0;
		}
		public static function EndsWith($haystack, $needle)
		{
			return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
		}
	}
?>