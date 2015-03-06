<?php
	namespace Phast\WebControls;
	
	use Phast\HTMLControls\HTMLControlInput;
	use Phast\HTMLControls\HTMLControlInputType;
	
	use Phast\HTMLControls\HTMLControlSelect;
	use Phast\HTMLControls\HTMLControlSelectOption;
	
	use Phast\HTMLControls\HTMLControlTextArea;
	
	use Phast\WebControlAttribute;
	use Phast\Enumeration;
	
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
				if (get_class($item) == "Phast\\WebControls\\FormViewItemSeparator")
				{
					echo("<div class=\"Separator\">");
					echo("<div class=\"Title\">");
					echo($item->Title);
					echo("</div>");
					echo("</div>");
				}
				else
				{
					echo("<div class=\"Field");
					if ($item->Required) echo(" Required");
					echo("\">");
					
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
					
					echo("<label for=\"" . $item->ID . "\"");
					if ($char !== null)
					{
						echo(" accesskey=\"" . $char . "\"");
					}
					echo(">" . $title . "</label>");
					$item->Render();
					echo("</div>");
				}
			}
		}
	}
	
	abstract class FormViewItem
	{
		public $ID;
		public $Name;
		public $Title;
		public $DefaultValue;
		public $Required;
		
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
		}
		
		public function Render()
		{
			$this->RenderContent();
		}
		
		protected abstract function RenderContent();
	}
	class FormViewItemSeparator extends FormViewItem
	{
		public function __construct($id = null, $title = null)
		{
			parent::__construct($id, $id, $title);
		}
		
		protected function RenderContent()
		{
			
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
		
		protected function RenderContent()
		{
			$elem = new HTMLControlInput();
			$elem->ID = $this->ID;
			$elem->Type = HTMLControlInputType::Text;
			$elem->Name = $this->Name;
			$elem->Value = $this->DefaultValue;
			if (isset($this->PlaceholderText))
			{
				$elem->PlaceholderText = $this->PlaceholderText;
			}
			if ($this->OnClientValueChanged != null)
			{
				$elem->Attributes[] = new WebControlAttribute("onchange", $this->OnClientValueChanged);
			}
			$elem->Render();
		}
	}
	class FormViewItemPassword extends FormViewItemText
	{
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null)
		{
			parent::__construct($id, $name, $title, $defaultValue);
		}
		
		protected function RenderContent()
		{
			$elem = new HTMLControlInput();
			$elem->ID = $this->ID;
			$elem->Type = HTMLControlInputType::Password;
			$elem->Name = $this->Name;
			$elem->Value = $this->DefaultValue;
			if (isset($this->PlaceholderText))
			{
				$elem->PlaceholderText = $this->PlaceholderText;
			}
			$elem->Render();
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
		
		protected function RenderContent()
		{
			$elem = new HTMLControlTextArea();
			$elem->ID = $this->ID;
			$elem->Name = $this->Name;
			if (isset($this->Rows)) $elem->Rows = $this->Rows;
			if (isset($this->Columns)) $elem->Columns = $this->Columns;
			$elem->Value = $this->DefaultValue;
			if (isset($this->PlaceholderText)) $elem->PlaceholderText = $this->PlaceholderText;
			$elem->Render();
		}
	}
	class FormViewItemBoolean extends FormViewItem
	{
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null)
		{
			parent::__construct($id, $name, $title, $defaultValue);
		}
		
		protected function RenderContent()
		{
			$elem = new HTMLControlInput();
			$elem->ID = $this->ID;
			$elem->Type = HTMLControlInputType::CheckBox;
			$elem->Name = $this->Name;
			if ($this->DefaultValue)
			{
				$elem->Attributes[] = new WebControlAttribute("checked", "checked");
			}
			$elem->Render();
		}
	}
	class FormViewItemChoice extends FormViewItem
	{
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
		}
		
		protected function RenderContent()
		{
			$elem = new HTMLControlSelect();
			$elem->ID = $this->ID;
			$elem->Name = $this->Name;
			foreach ($this->Items as $item)
			{
				$elem->Items[] = new HTMLControlSelectOption($item->Title, $item->Value, $item->Selected);
			}
			$elem->Render();
		}
	}
	class FormViewItemChoiceValue
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
	class FormViewItemDateTime extends FormViewItem
	{
		public $Nullable;
		public $NullableOptionText;
		
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null, $nullable = null)
		{
			parent::__construct($id, $name, $title, $defaultValue);
			$this->Nullable = $nullable;
		}
		
		protected function RenderContent()
		{
			$elem = new HTMLControlInput();
			$elem->ID = $this->ID;
			$elem->Type = HTMLControlInputType::Text;
			$elem->Name = $this->Name;
			$elem->Render();
		}
	}
?>