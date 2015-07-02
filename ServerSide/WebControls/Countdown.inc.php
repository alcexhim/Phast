<?php
	namespace Phast\WebControls;
	
	use Phast\HTMLControl;
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	class Countdown extends WebControl
	{
		public function __construct()
		{
			parent::__construct();
			$this->TagName = "div";
			$this->ClassList[] = "Countdown";
		}
		
		protected function RenderBeginTag()
		{
			$year = 2015;
			$month = 4;
			$day = 25;
			$hour = 0;
			$minute = 0;
			$second = 0;
			
			$this->Attributes[] = new WebControlAttribute("data-target-year", $year);
			$this->Attributes[] = new WebControlAttribute("data-target-month", $month);
			$this->Attributes[] = new WebControlAttribute("data-target-day", $day);
			$this->Attributes[] = new WebControlAttribute("data-target-hour", $hour);
			$this->Attributes[] = new WebControlAttribute("data-target-minute", $minute);
			$this->Attributes[] = new WebControlAttribute("data-target-second", $second);
			
			$this->HasContent = true;
			
			// quickly generate 6 child controls, one each for Year, Month, Day, Hour, Minute, Second
			for ($i = 0; $i < 6; $i++)
			{
				$div = new HTMLControl("div");
				$div->ClassList[] = "Segment";
				
				$divContent = new HTMLControl("div");
				$divContent->ClassList[] = "Content";
				$divContent->InnerHTML = "0";
				$div->Controls[] = $divContent;
				
				$divTitle = new HTMLControl("div");
				$divTitle->ClassList[] = "Title";
				switch ($i)
				{
					case 0:
					{
						$divTitle->InnerHTML = "Years";
						break;
					}
					case 1:
					{
						$divTitle->InnerHTML = "Months";
						break;
					}
					case 2:
					{
						$divTitle->InnerHTML = "Days";
						break;
					}
					case 3:
					{
						$divTitle->InnerHTML = "Hours";
						break;
					}
					case 4:
					{
						$divTitle->InnerHTML = "Minutes";
						break;
					}
					case 5:
					{
						$divTitle->InnerHTML = "Seconds";
						break;
					}
				}
				$div->Controls[] = $divTitle;
				
				$this->Controls[] = $div;
			}
			parent::RenderBeginTag();
		}
	}
?>