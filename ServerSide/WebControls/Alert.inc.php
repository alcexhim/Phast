<?php
	namespace Phast\WebControls;
	
	use Phast\HTMLControl;
	use Phast\WebControl;
	use Phast\Enumeration;
		
	abstract class AlertType extends Enumeration
	{
		const None = 0;
		const Error = 1;
		const Warning = 2;
		const Information = 3;
	}
	class Alert extends WebControl
	{
		/**
		 * The type of Alert to render.
		 * @var AlertType
		 */
		public $AlertType;
		
		/**
		 * The title of the Alert.
		 * @var string
		 */
		public $Title;
		
		public function __construct()
		{
			parent::__construct();
			$this->ClassList[] = "Alert";
			$this->TagName = "div";
		}
		
		protected function RenderBeginTag()
		{
			switch ($this->AlertType)
			{
				case "Error":
				case AlertType::Error:
				{
					$this->ClassList[] = "Error";
					break;
				}
				case "Warning":
				case AlertType::Warning:
				{
					$this->ClassList[] = "Warning";
					break;
				}
				case "Information":
				case AlertType::Information:
				{
					$this->ClassList[] = "Information";
					break;
				}
			}
			
			$ctls = $this->Controls;
			$this->Controls = array();
			
			$divTitle = new HTMLControl("div");
			$divTitle->ClassList[] = "Title";
			$divTitle->Content = $this->Title;
			$this->Controls[] = $divTitle;
			
			$divContent = new HTMLControl("div");
			$divContent->ClassList[] = "Content";
			foreach ($ctls as $ctl)
			{
				$divContent->Controls[] = $ctl;
			}
			$this->Controls[] = $divContent;
			
			parent::RenderBeginTag();
		}
	}
?>