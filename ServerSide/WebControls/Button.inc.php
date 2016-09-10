<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	use Phast\HTMLControl;
	use Phast\HTMLControls\Anchor;
	use Phast\HTMLControls\Input;
	use Phast\HTMLControls\InputType;
		
	class Button extends WebControl
	{
		public $DropDownControls;
		public $DropDownDirection;
		public $DropDownRequired;
		
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
			
			$this->TagName = "div";
			$this->ClassList[] = "pwt-Button";
			
			$this->DropDownControls = array();
			$this->DropDownRequired = false;
			
			$this->IconName = null;
			$this->UseSubmitBehavior = false;
			$this->ParseChildElements = true;
		}
		
		protected function RenderBeginTag()
		{
			if ($this->UseSubmitBehavior)
			{
				$tag = new Input();
				
				foreach ($this->ClassList as $className)
				{
					if ($className == "pwt-Button") continue;
					$tag->ClassList[] = $className;
				}
				
				$tag->ClassList = $this->ClassList;
				$tag->ClassList[] = $this->CssClass;
				$tag->Attributes[] = new WebControlAttribute("id", $this->ClientID);
				$tag->Type = InputType::Submit;
				$tag->Attributes[] = new WebControlAttribute("value", $this->Text);
				
				$this->Controls[] = $tag;
			}
			else
			{
				$tag = new Anchor();
				$tag->ClassList = $this->ClassList;
				$tag->ClassList[] = $this->CssClass;
				
				if ($this->ClientID != null)
				{
					$tag->Attributes[] = new WebControlAttribute("id", $this->ClientID);
				}
				else
				{
					$tag->Attributes[] = new WebControlAttribute("id", $this->ID);
				}

				$tag->TargetFrame = $this->TargetFrame;
				$tag->TargetURL = $this->TargetURL;
				$tag->TargetScript = $this->TargetScript;
				
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

				$this->Controls[] = $tag;
			}

			if (count($this->DropDownControls) > 0)
			{
				$this->ClassList[] = "pwt-DropDownButton";
			}
			if ($this->DropDownRequired)
			{
				$this->ClassList[] = "pwt-DropDownRequired";
			}
			if ($this->DropDownDirection != null)
			{
				$this->Attributes[] = new WebControlAttribute("data-pwt-dropdown-direction", $this->DropDownDirection);
			}
			
			$aDropDown = new Anchor();
			$aDropDown->ClassList[] = "pwt-Button pwt-DropDownButton";
			$aDropDown->InnerHTML = "&nbsp;";
			$this->Controls[] = $aDropDown;
			
			$divDropDown = new HTMLControl("div");
			$divDropDown->ClassList[] = "pwt-DropDownContent Popup";
			$divDropDown->Controls = $this->DropDownControls;
			$this->Controls[] = $divDropDown;
			
			parent::RenderBeginTag();
		}
	}
?>
