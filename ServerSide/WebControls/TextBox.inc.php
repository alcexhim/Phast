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
	use Phast\HTMLControls\Input;
	use Phast\HTMLControls\InputType;
	
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
		public $MultiSelect;
		
		public $RequireSelectionFromChoices;
		public $EnableMultipleSelection;
		public $ClearOnFocus;
		public $OpenWhenFocused;
		
		public $ShowColumnHeaders;
		
		public $SuggestionURL;
		
		public $Text;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->ShowColumnHeaders = true;
			$this->Items = array();
			
			$this->TagName = "div";
			$this->ClassList[] = "TextBox";
			
			$this->OpenWhenFocused = true;
			$this->MultiSelect = false;
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
			if ($this->MultiSelect)
			{
				$this->ClassList[] = "MultiSelect";
			}
			if ($this->SuggestionURL != null)
			{
				$this->Attributes[] = new WebControlAttribute("data-suggestion-url", System::ExpandRelativePath($this->SuggestionURL));
			}
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
				
				$spanText = new HTMLControl("span");
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
			
			$inputText = new Input();
			$inputText->ID = $this->ID . "_InputElement";
			$inputText->Type = InputType::Text;
			$inputText->Attributes[] = new WebControlAttribute("autocomplete", "off"); 
			$inputText->Name = $this->Name;
			$inputText->PlaceholderText = $this->PlaceholderText;
			$inputText->Value = $this->Text;
			$inputText->Width = $this->Width;
			
			if ($this->InnerStyle != null)
			{
				$inputText->Attributes[] = new WebControlAttribute("style", $this->InnerStyle);
			}
			$divTextboxContent->Controls[] = $inputText;
			
			$this->Controls[] = $divTextboxContent;
			
			$divSuggestionList = new HTMLControl("div");
			$divSuggestionList->ClassList[] = "SuggestionList";
			
			$ulSuggestionList = new HTMLControl("ul");
			$ulSuggestionList->ClassList[] = "Menu";
			$ulSuggestionList->ClassList[] = "Popup";
			foreach ($this->Items as $item)
			{
				$li = new HTMLControl("li");
				$li->ClassList[] = "MenuItem";
				$li->ClassList[] = "Command";
				$li->ClassList[] = "Visible";
				$aSuggestionListItem = new Anchor();
				
				$iCheckmark = new HTMLControl("i");
				$iCheckmark->ClassList[] = "fa";
				$iCheckmark->ClassList[] = "fa-check";
				$iCheckmark->ClassList[] = "Checkmark";
				$aSuggestionListItem->Controls[] = $iCheckmark;
				
				$spanText = new HTMLControl("span");
				$spanText->Content = $item->Title;
				$aSuggestionListItem->Controls[] = $spanText;
				
				$aSuggestionListItem->Attributes[] = new WebControlAttribute("data-value", $item->Value);
				$li->Controls[] = $aSuggestionListItem;
				$ulSuggestionList->Controls[] = $li;
			}
			$divSuggestionList->Controls[] = $ulSuggestionList;
			
			$divSuggestionListThrobber = new HTMLControl("div");
			$divSuggestionListThrobber->ClassList[] = "Throbber";
			$divSuggestionList->Controls[] = $divSuggestionListThrobber;
			
			$this->Controls[] = $divSuggestionList;
			
			$selectSuggestionList = new HTMLControl("select");
			$selectSuggestionList->ClassList[] = "SuggestionList";
			$selectSuggestionList->Attributes[] = new WebControlAttribute("multiple", "multiple");
			
			foreach ($this->Items as $item)
			{
				$option = new HTMLControl("option");
				$option->Attributes[] = new WebControlAttribute("value", $item->Value);
				$option->Content = $item->Title;
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