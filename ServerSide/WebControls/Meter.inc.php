<?php
	namespace Phast\WebControls;
	
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	use Phast\HTMLControl;
	use Phast\Enumeration;
	
	class MeterDisplayStyle extends Enumeration
	{
		const None = 0;
		const Percent = 1;
		const Decimal = 2;
	}
	
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
		
		/**
		 * Determines how the value of this Meter should be displayed.
		 * @var MeterDisplayStyle
		 */
		public $DisplayStyle;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->ClassList[] = "Meter";
			$this->TagName = "div";
			
			$this->MinimumValue = 0;
			$this->MaximumValue = 100;
			$this->CurrentValue = 0;
			
			$this->DisplayStyle = MeterDisplayStyle::Percent;
		}
		
		private function GetDisplayStyleValue()
		{
			$strValue = $this->DisplayStyle;
			if (is_string($strValue)) $strValue = strtolower($strValue);
			return $strValue;
		}
		
		protected function RenderBeginTag()
		{
			$this->Controls = array();
			
			$this->Attributes[] = new WebControlAttribute("data-minimum-value", $this->MinimumValue);
			$this->Attributes[] = new WebControlAttribute("data-maximum-value", $this->MaximumValue);
			$this->Attributes[] = new WebControlAttribute("data-current-value", $this->CurrentValue);

			switch ($this->GetDisplayStyleValue())
			{
				case "decimal":
				case MeterDisplayStyle::Decimal:
				{
					$this->Attributes[] = new WebControlAttribute("data-display-style", "decimal");
					break;
				}
				case "none":
				case MeterDisplayStyle::None:
				{
					$this->Attributes[] = new WebControlAttribute("data-display-style", "none");
					break;
				}
				case "percent":
				case MeterDisplayStyle::Percent:
				{
					$this->Attributes[] = new WebControlAttribute("data-display-style", "percent");
					break;
				}
				default:
				{
				}
			}
			
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
			if (($this->MaximumValue - $this->MinimumValue) <= 0)
			{
				$decimalValue = 0;
			}
			$printedValue = round(($decimalValue * ($this->MaximumValue - $this->MinimumValue)), 0);
			$percentValue = round(($decimalValue * 100), 0) . "%";
			
			$stringValue = "";
			
			switch ($this->GetDisplayStyleValue())
			{
				case "decimal":
				case MeterDisplayStyle::Decimal:
				{
					$stringValue = $printedValue;
					break;
				}
				case "none":
				case MeterDisplayStyle::None:
				{
					$stringValue = "";
					break;
				}
				case "percent":
				case MeterDisplayStyle::Percent:
				default:
				{
					$stringValue = $percentValue;
					break;
				}
			}
			
			$divContent->InnerHTML = $stringValue;
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