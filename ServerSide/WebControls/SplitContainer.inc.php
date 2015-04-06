<?php
	namespace Phast\WebControls;
	
	use Phast\Orientation;
	use Phast\WebStyleSheetRule;
	
	class SplitContainer extends \Phast\WebControl
	{
		public $Orientation;
		public $PrimaryPanel;
		public $SecondaryPanel;
		public $SplitterPosition;
		public $SplitterWidth;
		
		public function __construct($id)
		{
			parent::__construct($id);
			$this->Orientation = Orientation::Vertical;
			$this->PrimaryPanel = new SplitContainerPanel($this, "Primary");
			$this->SecondaryPanel = new SplitContainerPanel($this, "Secondary");
			$this->SplitterWidth = "4px";
			$this->TagName = "div";
		}
		
		protected function RenderBeginTag()
		{
			$this->ClassList[] = "SplitContainer";
			switch ($this->Orientation)
			{
				case Orientation::Horizontal:
				{
					$this->ClassList[] = "Horizontal";
					break;
				}
				case Orientation::Vertical:
				{
					$this->ClassList[] = "Vertical";
					break;
				}
			}
			parent::RenderBeginTag();
		}
		protected function RenderEndTag()
		{
			parent::RenderEndTag();
			echo("<script type=\"text/javascript\">var " . $this->ID . " = new SplitContainer('" . $this->ID . "');</script>");
		}
	}
	class SplitContainerPanel extends \Phast\WebControl
	{
		public $ID;
		public $ParentContainer;
		public $Visible;
		
		public function __construct($parent, $id)
		{
			$this->ParentContainer = $parent;
			$this->ID = $id;
			$this->TagName = "div";
			$this->Visible = true;
		}
		
		protected function RenderBeginTag()
		{
			$this->ClassList[] = "SplitContainerPanel";
			$this->ClassList[] = $this->ID;
			
			$this->ClientID = "SplitContainer_" . $this->ParentContainer->ID . "_" . $this->ID . "\"";
			if ($this->ID == "Primary" && $this->ParentContainer->SplitterPosition != null)
			{
				$this->StyleRules[] = new WebStyleSheetRule("width", $this->ParentContainer->SplitterPosition);
			}
			
			parent::RenderBeginTag();
		}
		protected function RenderEndTag()
		{
			parent::RenderEndTag();
			
			if ($this->ID == "Primary")
			{
				echo("<div class=\"Splitter\" id=\"SplitContainer_" . $this->ParentContainer->ID . "_Splitter\"");
				if ($this->ParentContainer->SplitterWidth != null)
				{
					echo (" style=\"width: " . $this->ParentContainer->SplitterWidth . "\"");
				}
				echo("><span class=\"SplitterGrip\">&nbsp;</span></div>");
			}
		}
	}
?>