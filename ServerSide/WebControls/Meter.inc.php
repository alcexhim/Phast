<?php
	namespace Phast\WebControls;
	
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	use Phast\HTMLControl;
			
	class Meter extends WebControl
	{
		/**
		 * The current value displayed in this Meter.
		 * @var double
		 */
		public $CurrentValue;
		/**
		 * The lowest possible value that can be displayed in this Meter.
		 * @var double
		 */
		public $MinimumValue;
		/**
		 * The highest possible value that can be displayed in this Meter.
		 * @var double
		 */
		public $MaximumValue;
		
		/**
		 * The color displayed in the background of the meter fill area.
		 * @var string
		 */
		public $BackgroundColor;
		/**
		 * The color displayed in the foreground of the meter fill area.
		 * @var string
		 */
		public $ForegroundColor;
		
		/**
		 * The title of this Meter.
		 * @var string
		 */
		public $Title;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->ClassList[] = "Meter";
			$this->TagName = "div";
			
			$this->MinimumValue = 0;
			$this->MaximumValue = 100;
			$this->CurrentValue = 0;
		}
		
		protected function RenderBeginTag()
		{
			$this->Controls = array();
			
			$this->Attributes[] = new WebControlAttribute("data-minimum-value", $this->MinimumValue);
			$this->Attributes[] = new WebControlAttribute("data-maximum-value", $this->MaximumValue);
			$this->Attributes[] = new WebControlAttribute("data-current-value", $this->CurrentValue);
			
			if ($this->BackgroundColor != null)
			{
				$this->Attributes[] = new WebControlAttribute("data-background-color", $this->BackgroundColor);
			}
			if ($this->ForegroundColor != null)
			{
				$this->Attributes[] = new WebControlAttribute("data-foreground-color", $this->ForegroundColor);
			}
			
			$divContentWrapper = new HTMLControl("div");
			$divContentWrapper->ClassList[] = "ContentWrapper";
			
			$divContent = new HTMLControl("div");
			$divContent->ClassList[] = "Content";
			
			$decimalValue = (($this->MinimumValue + $this->CurrentValue) / ($this->MaximumValue - $this->MinimumValue));
			$percentValue = round(($decimalValue * 100), 0) . "%";
			
			$divContent->InnerHTML = $percentValue;
			$divContentWrapper->Controls[] = $divContent;
			
			$canvas = new HTMLControl("canvas");
			$divContentWrapper->Controls[] = $canvas;
			
			$this->Controls[] = $divContentWrapper;
			
			$lblTitle = new HTMLControl("label");
			$lblTitle->Attributes[] = new WebControlAttribute("for", $this->ClientID);
			$lblTitle->InnerHTML = $this->Title;
			$this->Controls[] = $lblTitle;
			
			parent::RenderBeginTag();
		}
	}
?>