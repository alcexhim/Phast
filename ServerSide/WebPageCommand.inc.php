<?php
	namespace Phast;
	
	class WebPageCommand
	{
		public $Title;
		public $NavigateURL;
		public $OnClientClick;
		
		public function __construct($title, $navigateURL, $onClientClick)
		{
			$this->Title = $title;
			$this->NavigateURL = $navigateURL;
			$this->OnClientClick = $onClientClick;
		}
	}
?>