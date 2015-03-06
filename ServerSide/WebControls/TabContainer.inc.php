<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	\Enum::Create("Phast\\WebControls\\TabContainerTabPosition", "Top", "Bottom", "Left", "Right");
	
	class TabPage
	{
		public $ID;
		public $Title;
		public $Content;
		
		public $Visible;
		
		public $ImageURL;
		public $TargetURL;
		public $TargetScript;
		public $ContentFunction;
		
		public function __construct($id, $title, $imageURL = null, $targetURL = null, $targetScript = null, $contentFunction = null, $visible = true)
		{
			$this->ID = $id;
			$this->Title = $title;
			$this->ImageURL = $imageURL;
			$this->TargetURL = $targetURL;
			$this->TargetScript = $targetScript;
			$this->ContentFunction = $contentFunction;
			$this->Visible = $visible;
		}
	}
	
	class TabContainer extends WebControl
	{
		public $SelectedTab;
		public $TabPages;
		
		public $OnClientTabChanged;
		
		public function __construct($id)
		{
			parent::__construct($id);
			$this->TagName = "div";
		}
		
		public function GetTabByID($id)
		{
			foreach ($this->TabPages as $tabPage)
			{
				if ($tabPage->ID == $id) return $tabPage;
			}
			return null;
		}
		
		protected function OnInitialize()
		{
			$oldtab = $this->SelectedTab;
			$this->SelectedTab = $this->GetTabByID($this->GetClientProperty("SelectedTabID"));
			if ($this->SelectedTab == null) $this->SelectedTab = $oldtab;
		}
		
		protected function RenderBeginTag()
		{
			if ($this->OnClientTabChanged != null)
			{
				$this->Attributes[] = new WebControlAttribute("data-onclienttabchanged", $this->OnClientTabChanged);
			}
			$this->ClassList[] = "TabContainer";
			
			parent::RenderBeginTag();
		}
		
		protected function RenderContent()
		{
			echo("<div class=\"Tabs\">");
			$j = 0;
			foreach ($this->TabPages as $tabPage)
			{
				echo("<a data-id=\"" . $tabPage->ID . "\" class=\"Tab");
				if ($tabPage->Visible)
				{
					echo (" Visible");
				}
				if ($this->SelectedTab != null && ($tabPage->ID == $this->SelectedTab->ID))
				{
					echo (" Selected");
				}
				echo("\" href=\"");
				if ($tabPage->TargetURL != null)
				{
					echo(System::ExpandRelativePath($tabPage->TargetURL));
				}
				else
				{
					echo("#");
				}
				echo("\">");
				echo($tabPage->Title);
				echo("</a>");
				$j++;
			}
			echo("</div>");
			echo("<div class=\"TabPages\">");
			foreach ($this->TabPages as $tabPage)
			{
				echo("<div class=\"TabPage");
				if ($this->SelectedTab != null && ($tabPage->ID == $this->SelectedTab->ID))
				{
					echo (" Selected");
				}
				echo("\">");
				if (is_callable($tabPage->ContentFunction))
				{
					call_user_func($tabPage->ContentFunction);
				}
				else
				{
					echo($tabPage->Content);
				}
				echo("</div>");
			}
			echo("</div>");
		}
	}
?>