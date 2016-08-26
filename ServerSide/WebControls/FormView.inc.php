<?php
	namespace Phast\WebControls;
	
	use Phast\HTMLControl;
	use Phast\HTMLControls\Input;
	use Phast\HTMLControls\InputType;
	
	use Phast\HTMLControls\HTMLControlSelect;
	use Phast\HTMLControls\HTMLControlSelectOption;
	
	use Phast\HTMLControls\HTMLControlTextArea;
	
	use Phast\WebControlAttribute;
	use Phast\Enumeration;
	use Phast\System;
	
	class FormViewLabelStyle extends Enumeration
	{
		/**
		 * The labels for FormView items are rendered as an HTML <label> element beside the form element.
		 * @var FormViewLabelStyle
		 */
		const Label = 1;
		/**
		 * The labels for FormView items are rendered in-place where possible.
		 * @var FormViewLabelStyle
		 */
		const Placeholder = 2;
	}
	
	class FormView extends \Phast\WebControl
	{
		/**
		 * The style of the labels applied to FormView items.
		 * @var FormViewLabelStyle
		 */
		public $LabelStyle;
		/**
		 * Array of FormViewItems contained within this FormView.
		 * @var FormViewItem[]
		 */
		public $Items;
		
		public function GetItemByID($id)
		{
			foreach ($this->Items as $item)
			{
				if ($item->ID == $id) return $item;
			}
			return null;
		}
		
		public function __construct()
		{
			parent::__construct();
			$this->ParseChildElements = true;
			$this->TagName = "div";
			$this->ClassList[] = "FormView";
		}
		
		protected function RenderContent()
		{
			foreach ($this->Items as $item)
			{
				$div = new HTMLControl("div");
				$div->ID = "FormView_" . $this->ID . "_" . $item->ID;
				$div->ClassList[] = "Field";
				if ($item->Required) $div->ClassList[] = "Required";
				
				if ($item->GenerateLabel)
				{
					$title = $item->Title;
					$i = stripos($title, "_");
					$char = null;
					if ($i !== FALSE)
					{
						$before = substr($title, 0, $i);
						$after = substr($title, $i + 1);
						$char = substr($after, 0, 1);
						$title = $before . "<u>" . $char . "</u>" . substr($after, 1);
					}
					
					$lbl = new HTMLControl("label");
					$lbl->Attributes[] = new WebControlAttribute("for", $item->GetInputElementID());
					if ($char !== null)
					{
						$lbl->Attributes[] = new WebControlAttribute("accesskey", $char);
						echo(" accesskey=\"" . $char . "\"");
					}
					
					if ($item->IconName != "")
					{
						$i = new HTMLControl("i");
						$i->ClassList[] = "fa";
						$i->ClassList[] = "fa-" . $item->IconName;
						$lbl->Controls[] = $i;
					}
					
					$spanText = new HTMLControl("span");
					$spanText->ClassList[] = "Text";
					$spanText->InnerHTML = $title;
					
					$lbl->Controls[] = $spanText;
					
					$div->Controls[] = $lbl;
				}
				
				$ctl = $item->CreateControl();
				if ($ctl != null) $div->Controls[] = $ctl;
				$div->Render();
			}
		}
	}
	
	abstract class FormViewItem
	{
		public $ID;
		public $Name;
		public $Title;
		public $DefaultValue;
		public $Value;
		public $Required;
		
		public $IconName;
		
		public $GenerateLabel;
		public $ReadOnly;
		
		public function GetInputElementID()
		{
			return $this->ID;
		}
		
		/**
		 * The client-side script called when the value of this FormViewItem changed and validated.
		 * @var string
		 */
		public $OnClientValueChanged;
		
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null)
		{
			$this->ID = $id;
			
			if ($name == null) $name = $id;
			$this->Name = $name;
			
			if ($title == null) $title = $name;
			$this->Title = $title;
			
			$this->DefaultValue = $defaultValue;
			$this->Required = false;
			
			$this->ParseChildElements = false;
			$this->GenerateLabel = true;
			$this->ReadOnly = false;
		}
		
		public function CreateControl()
		{
			return $this->CreateControlInternal();
		}
		
		protected abstract function CreateControlInternal();
	}
	class FormViewItemSeparator extends FormViewItem
	{
		public function __construct($id = null, $title = null)
		{
			parent::__construct($id, $id, $title);
			$this->GenerateLabel = false;
		}
		
		protected function CreateControlInternal()
		{
			$divSeparator = new HTMLControl("div");
			$divSeparator->ClassList[] = "Separator";
			
			$divTitle = new HTMLControl("div");
			$divTitle->ClassList[] = "Title";
			$divTitle->InnerHTML = $this->Title;
			
			$divSeparator->Controls[] = $divTitle;
			return $divSeparator;
		}
	}
	class FormViewItemLabel extends FormViewItem
	{
		/**
		 * Creates a new Label FormViewItem with the given parameters.
		 * @param string $id The control ID for the FormViewItem.
		 * @param string $name The name of the form field to associate with the FormViewItem.
		 * @param string $title The title of the FormViewItem.
		 * @param string $defaultValue The default value of the FormViewItem.
		 */
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null)
		{
			parent::__construct($id, $name, $title, $defaultValue);
		}
		
		protected function CreateControlInternal()
		{
			$elem = new HTMLControl("div");
			$elem->ID = $this->ID;
			$elem->Name = $this->Name;
			$elem->InnerHTML = $this->DefaultValue;
			if (isset($this->Value))
			{
				if (is_string($this->Value))
				{
					$elem->InnerHTML = System::ExpandRelativePath($this->Value);
				}
			}
			
			return $elem;
		}
	}
	class FormViewItemText extends FormViewItem
	{
		/**
		 * Text that is displayed in the textbox of the FormViewItem when the user has not entered a value.
		 * @var string
		 */
		public $PlaceholderText;
		
		/**
		 * Creates a new Text FormViewItem with the given parameters.
		 * @param string $id The control ID for the FormViewItem.
		 * @param string $name The name of the form field to associate with the FormViewItem.
		 * @param string $title The title of the FormViewItem.
		 * @param string $defaultValue The default value of the FormViewItem.
		 */
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null)
		{
			parent::__construct($id, $name, $title, $defaultValue);
		}
		
		protected function CreateControlInternal()
		{
			$elem = new Input();
			$elem->ID = $this->ID;
			$elem->Type = InputType::Text;
			$elem->Name = $this->Name;
			$elem->Value = System::ExpandRelativePath($this->DefaultValue);
			if (isset($this->Value)) $elem->Value = System::ExpandRelativePath($this->Value);
			
			if (isset($this->PlaceholderText))
			{
				$elem->PlaceholderText = $this->PlaceholderText;
			}
			if ($this->OnClientValueChanged != null)
			{
				$elem->Attributes[] = new WebControlAttribute("onchange", $this->OnClientValueChanged);
			}
			return $elem;
		}
	}
	class FormViewItemPassword extends FormViewItemText
	{
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null)
		{
			parent::__construct($id, $name, $title, $defaultValue);
		}
		
		protected function CreateControlInternal()
		{
			$elem = new Input();
			$elem->ID = $this->ID;
			$elem->Type = InputType::Password;
			$elem->Name = $this->Name;
			$elem->Value = System::ExpandRelativePath($this->DefaultValue);
			if (isset($this->Value)) $elem->Value = System::ExpandRelativePath($this->Value);
			if (isset($this->PlaceholderText))
			{
				$elem->PlaceholderText = $this->PlaceholderText;
			}
			return $elem;
		}
	}
	class FormViewItemMemo extends FormViewItemText
	{
		public $Rows;
		public $Columns;
		public $PlaceholderText;
		
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null)
		{
			parent::__construct($id, $name, $title, $defaultValue);
		}
		
		protected function CreateControlInternal()
		{
			$elem = new HTMLControlTextArea();
			$elem->ID = $this->ID;
			$elem->Name = $this->Name;
			if (isset($this->Rows)) $elem->Rows = $this->Rows;
			if (isset($this->Columns)) $elem->Columns = $this->Columns;
			$elem->Value = System::ExpandRelativePath($this->DefaultValue);
			if (isset($this->Value)) $elem->Value = System::ExpandRelativePath($this->Value);
			if (isset($this->PlaceholderText)) $elem->PlaceholderText = $this->PlaceholderText;
			return $elem;
		}
	}
	class FormViewItemNumber extends FormViewItemText
	{
		public $MinimumValue;
		public $MaximumValue;
		
		/**
		 * Creates a new Number FormViewItem with the given parameters.
		 * @param string $id The control ID for the FormViewItem.
		 * @param string $name The name of the form field to associate with the FormViewItem.
		 * @param string $title The title of the FormViewItem.
		 * @param string $defaultValue The default value of the FormViewItem.
		 */
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null)
		{
			parent::__construct($id, $name, $title, $defaultValue);
		}
		
		protected function CreateControlInternal()
		{
			$elem = new Input();
			$elem->ID = $this->ID;
			$elem->Type = InputType::Number;
			$elem->Name = $this->Name;
			if ($this->MaximumValue != null)
			{
				$elem->Attributes[] = new WebControlAttribute("max", $this->MaximumValue);
			}
			if ($this->MinimumValue != null)
			{
				$elem->Attributes[] = new WebControlAttribute("min", $this->MinimumValue);
			}
			$elem->Value = System::ExpandRelativePath($this->DefaultValue);
			if (isset($this->Value)) $elem->Value = System::ExpandRelativePath($this->Value);
			
			if (isset($this->PlaceholderText))
			{
				$elem->PlaceholderText = $this->PlaceholderText;
			}
			if ($this->OnClientValueChanged != null)
			{
				$elem->Attributes[] = new WebControlAttribute("onchange", $this->OnClientValueChanged);
			}
			return $elem;
		}
	}
	class FormViewItemBoolean extends FormViewItem
	{
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null)
		{
			parent::__construct($id, $name, $title, $defaultValue);
		}
		
		protected function CreateControlInternal()
		{
			$elem = new Input();
			$elem->ID = $this->ID;
			$elem->Type = InputType::CheckBox;
			$elem->Name = $this->Name;
			if ($this->DefaultValue)
			{
				$elem->Attributes[] = new WebControlAttribute("checked", "checked");
			}
			return $elem;
		}
	}
	class FormViewItemChoice extends FormViewItem
	{
		public $EnableMultipleSelection;
		public $RequireSelectionFromChoices;
		
		/**
		 * The items available for selection.
		 * @var FormViewItemChoiceValue[]
		 */
		public $Items;
		
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null, $items = null)
		{
			parent::__construct($id, $name, $title, $defaultValue);
			if (is_array($items))
			{
				$this->Items = $items;
			}
			else
			{
				$this->Items = array();
			}
			$this->ParseChildElements = true;
		}
		
		public function GetInputElementID()
		{
			return $this->ID . "_InputElement";
		}
		
		protected function CreateControlInternal()
		{
			$elem = new TextBox();
			$elem->ID = $this->ID;
			$elem->Name = $this->Name;
			$elem->EnableMultipleSelection = $this->EnableMultipleSelection;
			$elem->RequireSelectionFromChoices = $this->RequireSelectionFromChoices;
			$elem->ClearOnFocus = $elem->RequireSelectionFromChoices;
			foreach ($this->Items as $item)
			{
				$elem->Items[] = new TextBoxItem($item->Title, System::ExpandRelativePath($item->Value), $item->Selected);
			}
			return $elem;
		}
	}
	class FormViewItemChoiceValue
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
	class FormViewItemDateTime extends FormViewItem
	{
		public $Nullable;
		public $NullableOptionText;
		
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null, $nullable = null)
		{
			parent::__construct($id, $name, $title, $defaultValue);
			$this->Nullable = $nullable;
		}
		
		protected function CreateControlInternal()
		{
			$elem = new Input();
			$elem->ID = $this->ID;
			$elem->Type = InputType::Text;
			$elem->Name = $this->Name;
			return $elem;
		}
	}
?>