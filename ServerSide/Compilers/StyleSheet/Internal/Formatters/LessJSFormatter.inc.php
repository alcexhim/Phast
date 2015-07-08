<?php
	namespace Phast\Compilers\StyleSheet\Internal\Formatters;
	
	class LessJSFormatter extends ClassicFormatter
	{
		public $disableSingle = true;
		public $breakSelectors = true;
		public $assignSeparator = ": ";
		public $selectorSeparator = ",";
	}
?>