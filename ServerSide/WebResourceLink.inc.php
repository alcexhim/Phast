<?php
	namespace Phast;
	
	class WebResourceLink
	{
		public $ContentType;
		public $URL;
		public $Relationship;
		
		public function __construct($url, $relationship, $contentType = null)
		{
			$this->URL = $url;
			$this->Relationship = $relationship;
			$this->ContentType = $contentType;
		}
	}
?>