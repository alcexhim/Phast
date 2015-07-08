<?php
	namespace Phast\Compilers\StyleSheet\Internal\Formatters;
	
	class CompressedFormatter extends ClassicFormatter
	{
		public $disableSingle = true;
		public $open = "{";
		public $selectorSeparator = ",";
		public $assignSeparator = ":";
		public $break = "";
		public $compressColors = true;
	
		public function indentStr($n = 0)
		{
			return "";
		}
	}
?>