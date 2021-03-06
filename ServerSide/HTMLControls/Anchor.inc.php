<?php
	namespace Phast\HTMLControls;
	
	use Phast\System;
	
	use Phast\HTMLControl;
	use Phast\WebControl;
	
	use Phast\WebControlAttribute;
	
	/**
	 * Provides an HTMLControl for the <A> HTML tag.
	 * @author Michael Becker
	 */
	class Anchor extends HTMLControl
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->TagName = "a";
		}
		
		/**
		 * The URL to navigate to when this anchor is activated.
		 * @var string
		 */
		public $TargetURL;
		/**
		 * The script to execute when this anchor is activated.
		 * @var string
		 */
		public $TargetScript;
		/**
		 * The frame in which to open the associated TargetURL.
		 * @var string
		 */
		public $TargetFrame;
		
		protected function RenderBeginTag()
		{
			if ($this->TargetURL != null)
			{
				$this->Attributes[] = new WebControlAttribute("href", System::ExpandRelativePath($this->TargetURL));
				if ($this->TargetFrame != null)
				{
					$this->Attributes[] = new WebControlAttribute("target", $this->TargetFrame);
				}
			}
			else
			{
				$this->Attributes[] = new WebControlAttribute("href", "#");
			}
			if ($this->TargetScript != null)
			{
				$this->Attributes[] = new WebControlAttribute("onclick", $this->TargetScript);
			}
			parent::RenderBeginTag();
		}
	}
?>