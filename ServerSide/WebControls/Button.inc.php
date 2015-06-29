<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	use Phast\HTMLControl;
	use Phast\Phast;
	
	class Button extends WebControl
	{
		public $IconName;
		
		public $TargetFrame;
		public $TargetURL;
		public $TargetScript;
		
		public $Text;
		
		/**
		 * Determines whether this Button is rendered as an HTML Input control with type Submit.
		 * @var boolean
		 */
		public $UseSubmitBehavior;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->IconName = null;
			$this->UseSubmitBehavior = false;
		}
		
		protected function RenderContent()
		{
			if ($this->UseSubmitBehavior)
			{
				$tag = new HTMLControl();
				$tag->TagName = "input";
				$tag->HasContent = false;
				
				$tag->ClassList = $this->ClassList;
				$tag->ClassList[] = $this->CssClass;
				$tag->Attributes[] = new WebControlAttribute("id", $this->ClientID);
				$tag->Attributes[] = new WebControlAttribute("type", "submit");
				$tag->Attributes[] = new WebControlAttribute("value", $this->Text);
				$tag->Render();
			}
			else
			{
				$tag = new HTMLControl();
				$tag->TagName = "a";
				
				$tag->ClassList = $this->ClassList;
				array_unshift($tag->ClassList, "pwt-Button");
				$tag->ClassList[] = $this->CssClass;
				if ($this->ClientID != null)
				{
					$tag->Attributes[] = new WebControlAttribute("id", $this->ClientID);
				}
				else
				{
					$tag->Attributes[] = new WebControlAttribute("id", $this->ID);
				}
				
				if ($this->TargetFrame != null)
				{
					$tag->Attributes[] = new WebControlAttribute("target", $this->TargetFrame);
				}
				if ($this->TargetURL != null)
				{
					$tag->Attributes[] = new WebControlAttribute("href", System::ExpandRelativePath($this->TargetURL));
				}
				if ($this->TargetScript != null)
				{
					$tag->Attributes[] = new WebControlAttribute("href", "#");
					$tag->Attributes[] = new WebControlAttribute("onclick", $this->TargetScript);
				}
				
				if ($this->IconName != null)
				{
					$i = new HTMLControl("i");
					$i->ClassList[] = "fa";
					$i->ClassList[] = "fa-" . $this->IconName;
					$tag->Controls[] = $i;
				}
				
				$spanText = new HTMLControl("span");
				$spanText->ClassList[] = "Text";
				$spanText->InnerHTML = $this->Text;
				
				$tag->Controls[] = $spanText;
				
				$tag->Render();
			}
		}
	}
?>
