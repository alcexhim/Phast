<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	use Phast\WebControl;
	use Phast\HTMLControl;
	use Phast\WebStyleSheetRule;
	
	class ProgressBar extends WebControl
	{
		public $MaximumValue;
		public $MinimumValue;
		public $CurrentValue;
		
		public $Animated;
		public $Striped;
		
		public $Text;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->MinimumValue = 0;
			$this->MaximumValue = 100;
			$this->CurrentValue = 0;

			$this->Animated = false;
			$this->Striped = false;
			
			$this->TagName = "div";
			
			$this->ClassList[] = "ProgressBar";
		}
		
		protected function RenderBeginTag()
		{
			if ((is_bool($this->Striped) && $this->Striped) || (is_string($this->Striped) && $this->Striped == "true"))
			{
				$this->ClassList[] = "Striped";
			}
			if ((is_bool($this->Animated) && $this->Animated) || (is_string($this->Animated) && $this->Animated == "true"))
			{
				$this->ClassList[] = "Animated";
			}
			
			$divProgressValueFill = new HTMLControl("div");
			$divProgressValueFill->ClassList[] = "ProgressValueFill";
			$divProgressValueFill->StyleRules[] = new WebStyleSheetRule("width", ((($this->MinimumValue + $this->CurrentValue) / ($this->MaximumValue - $this->MinimumValue)) * 100) . "%");
			$divProgressValueFill->Content = "&nbsp;";
			
			$divProgressValueLabel = new HTMLControl("div");
			$divProgressValueLabel->ClassList[] = "ProgressValueLabel";
			if ($this->Text == "")
			{
				$divProgressValueLabel->Content = "&nbsp;";
			}
			else
			{
				$divProgressValueLabel->Content = $this->Text;
			}
			
			$this->Controls = array($divProgressValueFill, $divProgressValueLabel);
			
			parent::RenderBeginTag();
		}
	}
?>