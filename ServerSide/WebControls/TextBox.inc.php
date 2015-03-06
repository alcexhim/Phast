<?php
	namespace Phast\WebControls;
	
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	use Phast\WebScript;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Phast\System;
	
	class TextBox extends WebControl
	{
		public $Name;
		public $PlaceholderText;
		
		public $Columns;
		public $Items;
		
		public $InnerStyle;
		
		public $RequireSelectionFromChoices;
		public $EnableMultipleSelection;
		
		public $ShowColumnHeaders;
		
		public $SuggestionURL;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->ShowColumnHeaders = true;
			$this->Items = array();
			
			$this->TagName = "div";
			$this->ClassList[] = "TextBox";
		}
		
		protected function OnInitialize()
		{
			$parent = $this->FindParentPage();
			if ($parent != null) $parent->Scripts[] = new WebScript("$(PhastStaticPath)/Scripts/Controls/TextBox.js");
		}
		
		protected function RenderBeginTag()
		{
			if ($this->RequireSelectionFromChoices)
			{
				$this->ClassList[] = "RequireSelection";
			}
			$this->Attributes[] = new WebControlAttribute("data-suggestion-url", System::ExpandRelativePath($this->SuggestionURL));
			parent::RenderBeginTag();
		}
		
		protected function RenderContent()
		{
			echo("<div class=\"TextboxContent\">");
			echo("<span class=\"TextboxSelectedItems\">");
			
			$i = 0;
			foreach ($this->Items as $item)
			{
				if (!$item->Selected) continue;
				
				echo("<span class=\"SelectedItem\">");
				echo("<span class=\"Text\">");
				echo($item->Title);
				echo("</span>");
				echo("<a class=\"CloseButton\" href=\"#\">&nbsp;</a>");
				echo("</span>");
				
				$i++;
			}
			
			echo("</span>");
			echo("<input type=\"text\" autocomplete=\"off\" name=\"" . $this->Name . "\" placeholder=\"" . $this->PlaceholderText . "\"");
			if ($this->Width != null)
			{
				echo(" style=\"width: " . $this->Width . ";\"");
			}
			if ($this->InnerStyle != null)
			{
				echo (" style=\"" . $this->InnerStyle . "\"");
			}
			
			echo(" />");
			echo("</div>");
			
			echo("<div class=\"SuggestionList Popup\">");
			$lv = new ListView("Textbox_" . $this->ID . "_ListView");
			$lv->ShowColumnHeaders = $this->ShowColumnHeaders;
			$lv->Columns = $this->Columns;
			$lv->Items = $this->Items;
			$lv->Render();
			echo("</div>");
		}
	}
?>