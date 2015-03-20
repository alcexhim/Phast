<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	use Phast\WebControl;
	
	class ProgressBar extends WebControl
	{
		public $MaximumValue;
		public $MinimumValue;
		public $CurrentValue;
		
		public $Text;
		
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->MinimumValue = 0;
			$this->MaximumValue = 100;
			$this->Value = 0;
		}
		
		protected function RenderContent()
		{
			echo("<div class=\"ProgressBar\" id=\"ProgressBar_" . $this->ID . "\">");
			echo("<div class=\"ProgressValueFill\" id=\"ProgressBar_" . $this->ID . "_ValueFill\" style=\"width: " . (($this->Value / ($this->MaximumValue - $this->MinimumValue)) * 100) . "%\">&nbsp;</div>");
			echo("<div class=\"ProgressValueLabel\" id=\"ProgressBar_" . $this->ID . "_ValueLabel\">&nbsp;</div>");
			echo("</div>");
		}
	}
?>