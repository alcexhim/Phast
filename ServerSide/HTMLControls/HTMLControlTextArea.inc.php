<?php
	namespace Phast\HTMLControls;
	
	use Phast\HTMLControl;
	use Phast\WebControl;
	
	use Phast\WebControlAttribute;
	
	class HTMLControlTextArea extends HTMLControl
	{
		public function __construct()
		{
			parent::__construct();
			$this->TagName = "textarea";
			$this->HasContent = true;
		}
		
		public $Name;
		public $Value;
		public $PlaceholderText;
		
		public $Rows;
		public $Columns;
		
		protected function RenderBeginTag()
		{
			if (isset($this->ID)) $this->Attributes[] = new WebControlAttribute("id", $this->ID);
			if (isset($this->Name)) $this->Attributes[] = new WebControlAttribute("name", $this->Name);
			if (isset($this->PlaceholderText)) $this->Attributes[] = new WebControlAttribute("placeholder", $this->PlaceholderText);
			if (isset($this->Rows)) $this->Attributes[] = new WebControlAttribute("rows", $this->Rows);
			if (isset($this->Columns)) $this->Attributes[] = new WebControlAttribute("cols", $this->Columns);
			parent::RenderBeginTag();
		}
		protected function RenderContent()
		{
			if (isset($this->Value))
			{
				echo($this->Value);
			}
		}
	}
?>