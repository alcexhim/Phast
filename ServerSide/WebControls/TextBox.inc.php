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
	
	use Phast\HTMLControl;
	use Phast\HTMLControls\Anchor;
	use Phast\HTMLControls\HTMLControlInput;
	use Phast\HTMLControls\HTMLControlInputType;
					
	class TextBoxItem
	{
		public $Title;
		public $Value;
		public $Selected;
		
		public function __construct($title = null, $value = null, $selected = false)
		{
			$this->Title = $title;
			$this->Value = $value;
			$this->Selected = $selected;
		}
	}
	
	class TextBox extends WebControl
	{
		public $Name;
		public $PlaceholderText;
		
		/**
		 * The items available for selection from this TextBox.
		 * @var TextBoxItem[]
		 */
		public $Items;
		
		public $InnerStyle;
		
		public $RequireSelectionFromChoices;
		public $EnableMultipleSelection;
		public $ClearOnFocus;
		public $OpenWhenFocused;
		
		public $ShowColumnHeaders;
		
		public $SuggestionURL;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->ShowColumnHeaders = true;
			$this->Items = array();
			
			$this->TagName = "div";
			$this->ClassList[] = "TextBox";
			
			$this->OpenWhenFocused = true;
		}
		
		protected function OnInitialize()
		{
			$parent = $this->FindParentPage();
			if ($parent != null) $parent->Scripts[] = new WebScript("$(PhastStaticPath)/Scripts/Controls/TextBox.js");
		}
		
		protected function RenderBeginTag()
		{
			$this->Controls = array();
			
			if ($this->RequireSelectionFromChoices)
			{
				$this->ClassList[] = "RequireSelection";
			}
			if ($this->ClearOnFocus)
			{
				$this->ClassList[] = "ClearOnFocus";
			}
			$this->Attributes[] = new WebControlAttribute("data-suggestion-url", System::ExpandRelativePath($this->SuggestionURL));
			if ($this->OpenWhenFocused)
			{
				$this->Attributes[] = new WebControlAttribute("data-auto-open", "true");
			}
			
			$divTextboxContent = new HTMLControl("div");
			$divTextboxContent->ClassList[] = "TextboxContent";
			
			$spanTextboxSelectedItems = new HTMLControl("span");
			$spanTextboxSelectedItems->ClassList[] = "TextboxSelectedItems";
			
			$i = 0;
			foreach ($this->Items as $item)
			{
				if (!$item->Selected) continue;
				
				$spanSelectedItem = new HTMLControl("span");
				$spanSelectedItem->ClassList[] = "SelectedItem";
				
				$spanText = new HTMLControl("Text");
				$spanText->ClassList[] = "Text";
				$spanText->InnerHTML = $item->Title;
				$spanSelectedItem->Controls[] = $spanText;
				
				$aCloseButton = new Anchor();
				$aCloseButton->ClassList[] = "CloseButton";
				$spanSelectedItem->Controls[] = $aCloseButton;
				
				$spanTextboxSelectedItems->Controls[] = $spanSelectedItem;
				
				$i++;
			}
			$divTextboxContent->Controls[] = $spanTextboxSelectedItems;
			
			$inputText = new HTMLControlInput();
			$inputText->ID = $this->ID . "_InputElement";
			$inputText->Type = HTMLControlInputType::Text;
			$inputText->Attributes[] = new WebControlAttribute("autocomplete", "off"); 
			$inputText->Name = $this->Name;
			$inputText->PlaceholderText = $this->PlaceholderText;
			$inputText->Width = $this->Width;
			
			if ($this->InnerStyle != null)
			{
				$inputText->Attributes[] = new WebControlAttribute("style", $this->InnerStyle);
			}
			$divTextboxContent->Controls[] = $inputText;
			
			$this->Controls[] = $divTextboxContent;
			
			$ulSuggestionList = new HTMLControl("ul");
			$ulSuggestionList->ClassList[] = "SuggestionList";
			$ulSuggestionList->ClassList[] = "Menu";
			$ulSuggestionList->ClassList[] = "Popup";
			foreach ($this->Items as $item)
			{
				$li = new HTMLControl("li");
				$li->ClassList[] = "MenuItem";
				$li->ClassList[] = "Visible";
				$aSuggestionListItem = new Anchor();
				
				$iCheckmark = new HTMLControl("i");
				$iCheckmark->ClassList[] = "fa";
				$iCheckmark->ClassList[] = "fa-check";
				$iCheckmark->ClassList[] = "Checkmark";
				$aSuggestionListItem->Controls[] = $iCheckmark;
				
				$spanText = new HTMLControl("span");
				$spanText->InnerHTML = $item->Title;
				$aSuggestionListItem->Controls[] = $spanText;
				
				$aSuggestionListItem->Attributes[] = new WebControlAttribute("data-value", $item->Value);
				$li->Controls[] = $aSuggestionListItem;
				$ulSuggestionList->Controls[] = $li;
			}
			$this->Controls[] = $ulSuggestionList;
			
			$selectSuggestionList = new HTMLControl("select");
			$selectSuggestionList->ClassList[] = "SuggestionList";
			$selectSuggestionList->Attributes[] = new WebControlAttribute("multiple", "multiple");
			
			foreach ($this->Items as $item)
			{
				$option = new HTMLControl("option");
				$option->Attributes[] = new WebControlAttribute("value", $item->Value);
				$option->InnerHTML = $item->Title;
				if ($item->Selected)
				{
					$option->Attributes[] = new WebControlAttribute("selected", "selected");
				}
				$selectSuggestionList->Controls[] = $option;
			}
			$this->Controls[] = $selectSuggestionList;
			
			parent::RenderBeginTag();
		}
	}
?>