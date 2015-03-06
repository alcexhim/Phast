<?php
	namespace Phast\HTMLControls;
	
	use Phast\Enumeration;
	use Phast\HTMLControl;
	use Phast\WebControl;
	
	use Phast\WebControlAttribute;

	/**
	 * Provides an enumeration of predefined values for type of input.
	 * @author Michael Becker
	 */
	abstract class HTMLControlInputType extends Enumeration
	{
		/**
		 * No type is specified
		 * @var int 0
		 */
		const None = 0;
		/**
		 * Text
		 * @var int 1
		 */
		const Text = 1;
		/**
		 * Password
		 * @var int 2
		 */
		const Password = 2;
		/**
		 * CheckBox
		 * @var int 3
		 */
		const CheckBox = 3;
		/**
		 * RadioButton
		 * @var int 4
		 */
		const RadioButton = 4;
		/**
		 * Hidden
		 * @var int 9
		 */
		const Hidden = 9;
	}
	
	class HTMLControlInput extends HTMLControl
	{
		public function __construct()
		{
			parent::__construct();
			$this->TagName = "input";
			$this->HasContent = false;
		}
		
		public $Name;
		public $Type;
		public $Value;
		public $PlaceholderText;
		
		protected function RenderBeginTag()
		{
			switch ($this->Type)
			{
				case HTMLControlInputType::Text:
				{
					$this->Attributes[] = new WebControlAttribute("type", "text");
					break;
				}
				case HTMLControlInputType::Password:
				{
					$this->Attributes[] = new WebControlAttribute("type", "password");
					break;
				}
				case HTMLControlInputType::CheckBox:
				{
					$this->Attributes[] = new WebControlAttribute("type", "checkbox");
					break;
				}
				case HTMLControlInputType::RadioButton:
				{
					$this->Attributes[] = new WebControlAttribute("type", "radio");
					break;
				}
				case HTMLControlInputType::Hidden:
				{
					$this->Attributes[] = new WebControlAttribute("type", "hidden");
					break;
				}
			}
			if (isset($this->Name)) $this->Attributes[] = new WebControlAttribute("name", $this->Name);
			if (isset($this->Value)) $this->Attributes[] = new WebControlAttribute("value", $this->Value);
			if (isset($this->PlaceholderText)) $this->Attributes[] = new WebControlAttribute("placeholder", $this->PlaceholderText);
			parent::RenderBeginTag();
		}
	}
?>