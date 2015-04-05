<?php
	namespace Phast;
	
	class StringMethods
	{
		public static function StartsWith($value, $search)
		{
			$valueLength = strlen($value);
			$searchLength = strlen($search);
			if ($valueLength < $searchLength) return false;
			
			return (substr($value, 0, $searchLength) == $search);
		}
		public static function EndsWith($value, $search)
		{
			$valueLength = strlen($value);
			$searchLength = strlen($search);
			if ($valueLength < $searchLength) return false;
			
			return (substr($value, $valueLength - $searchLength, $searchLength) == $search);
		}
	}
?>