<?php
	namespace Phast;
	
	class WebPageMessageSeverity extends Enumeration
	{
		const None = 0;
		const Information = 1;
		const Warning = 2;
		const Error = 3;
	}
	
	class WebPageMessage
	{
		public $Message;
		public $Severity;
		
		public function __construct($message, $severity = WebPageMessageSeverity::None)
		{
			$this->Message = $message;
			$this->Severity = $severity;
		}
	}
?>