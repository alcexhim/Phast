<?php
	namespace Phast;
	
    class WebStyleSheet
    {
        public $ContentType;
        public $FileName;
        
        public function __construct($FileName, $ContentType = "text/css")
        {
            $this->FileName = $FileName;
            $this->ContentType = $ContentType;
        }
    }
	class WebStyleSheetRule
	{
		public $Name;
		public $Value;
		
		public function __construct($name, $value)
		{
			$this->Name = $name;
			$this->Value = $value;
		}
	}
?>