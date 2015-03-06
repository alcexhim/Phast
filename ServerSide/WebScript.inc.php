<?php
	namespace Phast;
	
    class WebScript
    {
        public $FileName;
        public $ContentType;
		public $Content;
		
		public static function FromFile($FileName, $ContentType = null)
		{
			$script = new WebScript($FileName, $ContentType);
			return $script;
		}
		public static function FromContent($Content, $ContentType = null)
		{
			$script = new WebScript(null, $contentType);
			$script->Content = $Content;
			return $script;
		}
        
        public function __construct($fileName = null, $contentType = null)
        {
            $this->FileName = $fileName;
            if ($contentType == null)
            {
                $contentType = "text/javascript";
            }
            $this->ContentType = $contentType;
        }
    }
?>