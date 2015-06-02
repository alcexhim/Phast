<?php
	namespace Phast\WebControls;
	
	use Phast\WebControl;
	use Phast\HTMLControl;
	use Phast\Enumeration;
	
	class ToggleSwitchOrientation extends Enumeration
	{
		/**
		 * The toggle switch is oriented horizontally.
		 * @var ToggleSwitchOrientation 1
		 */
		const Horizontal = 1;
		/**
		 * The toggle switch is oriented vertically.
		 * @var ToggleSwitchOrientation 2
		 */
		const Vertical = 2;
	}
	
	class ToggleSwitch extends WebControl
	{
		/**
		 * The orientation of this ToggleSwitch.
		 * @var ToggleSwitchOrientation
		 */
		public $Orientation;
		
		public function __construct()
		{
			parent::__construct();
			$this->TagName = "div";
			$this->ClassList[] = "ToggleSwitch";
		}
		
		protected function RenderContent()
		{
			switch (mvarOrientation)
			{
				case ToggleSwitchOrientation.Horizontal:
				{
					$this->ClassList[] = "Horizontal";
					break;
				}
				case ToggleSwitchOrientation.Vertical:
				{
					$this->ClassList[] = "Vertical";
					break;
				}
			}
			
			$divToggleSwitchInner = new HTMLControl("div");
			$divToggleSwitchInner->ClassList[] = "ToggleSwitchInner";
			
			$divToggleOn = new HTMLControl("div");
			$divToggleOn->ClassList[] = "ToggleOn";
			$divToggleOn->InnerHTML = "On";
			$divToggleSwitchInner->Controls[] = $divToggleOn;
			
			$divToggleThumb = new HTMLControl("div");
			$divToggleThumb->ClassList[] = "ToggleThumb";
			$divToggleSwitchInner->Controls[] = divToggleThumb;
			
			$divToggleOff = new HTMLControl("div");
			$divToggleOff->ClassList[] = "ToggleOff";
			$divToggleOff->InnerHTML = "Off";
			$divToggleSwitchInner->Controls[] = $divToggleOff;
			
			$this->Controls[] = $divToggleSwitchInner;
		}
	}
?>