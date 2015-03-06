<?php
	namespace Phast\HTMLControls;
	
	use Phast\HTMLControl;
	use Phast\WebControl;
	
	use Phast\WebControlAttribute;
	
	class HTMLControlSelectOption
	{
		public $Title;
		public $Value;
		public $Selected;
		
		public function __construct($title, $value = null, $selected = false)
		{
			$this->Title = $title;
			$this->Value = $value;
			$this->Selected = $selected;
		}
	}
	class HTMLControlSelect extends HTMLControl
	{
		public $Items;
		
		public function __construct()
		{
			parent::__construct();
			$this->TagName = "select";
			$this->Items = array();
		}
		
		protected function RenderContent()
		{
			foreach ($this->Items as $item)
			{
				$tag = new HTMLControl();
				$tag->TagName = "option";
				if ($item->Value != null)
				{
					$tag->Attributes[] = new WebControlAttribute("value", $item->Value);
				}
				$tag->InnerHTML = $item->Title;
				if ($item->Selected)
				{
					$tag->Attributes[] = new WebControlAttribute("selected", "selected");
				}
				$tag->Render();
			}
		}
	}
?>