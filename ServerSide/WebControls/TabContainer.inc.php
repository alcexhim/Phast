<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	use Phast\HTMLControl;
	use Phast\HTMLControls\Anchor;
	use Phast\Enumeration;
	
	/**
	 * Enumeration specifying the position of tabs on a tab container.
	 * @author Michael Becker
	 */
	abstract class TabContainerTabPosition extends Enumeration
	{
		/**
		 * The tabs are aligned at the top of the tab container, before the tab page content.
		 * @var TabContainerTabPosition
		 */
		const Top = 1;
		/**
		 * The tabs are aligned at the bottom of the tab container, after the tab page content.
		 * @var TabContainerTabPosition
		 */
		const Bottom = 2;
		/**
		 * The tabs are aligned to the left of the tab container, before the tab page content.
		 * @var TabContainerTabPosition
		 */
		const Left = 3;
		/**
		 * The tabs are aligned to the right of the tab container, after the tab page content.
		 * @var TabContainerTabPosition
		 */
		const Right = 4;
	}
	
	class TabPage
	{
		public $ID;
		public $Title;
		
		public $Controls;
		
		public $Visible;
		
		public $ImageURL;
		public $TargetURL;
		public $TargetScript;
		
		public function GetControlByID($id)
		{
			foreach ($this->Controls as $ctl)
			{
				if ($ctl->ID == $id) return $ctl;
				
				$ctll = $ctl->GetControlByID($id);
				if ($ctll != null) return $ctll;
			}
			return null;
		}
		
		public function __construct($id, $title, $imageURL = null, $targetURL = null, $targetScript = null, $visible = true)
		{
			$this->ID = $id;
			$this->Title = $title;
			$this->ImageURL = $imageURL;
			$this->TargetURL = $targetURL;
			$this->TargetScript = $targetScript;
			$this->Visible = $visible;
		}
	}
	
	class TabContainer extends WebControl
	{
		public $SelectedTab;
		public $SelectedTabID;
		
		public $TabPages;
		/**
		 * Determines the position of the tabs relative to the tab pages.
		 * @var TabContainerTabPosition
		 */
		public $TabPosition;
		
		public $OnClientTabChanged;
		
		public function __construct($id)
		{
			parent::__construct($id);
			$this->TagName = "div";
			$this->ParseChildElements = true;
			$this->TabPosition = TabContainerTabPosition::Top;
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
			
			if (is_string($this->TabPosition))
			{
				switch (strtolower($this->TabPosition))
				{
					case "left":
					{
						$this->ClassList[] = "TabPositionLeft";
						break;
					}
					case "top":
					{
						$this->ClassList[] = "TabPositionTop";
						break;
					}
					case "right":
					{
						$this->ClassList[] = "TabPositionRight";
						break;
					}
					case "bottom":
					{
						$this->ClassList[] = "TabPositionBottom";
						break;
					}
				}
			}
			else
			{
				switch ($this->TabPosition)
				{
					case TabContainerTabPosition::Left:
					{
						$this->ClassList[] = "TabPositionLeft";
						break;
					}
					case TabContainerTabPosition::Top:
					{
						$this->ClassList[] = "TabPositionTop";
						break;
					}
					case TabContainerTabPosition::Right:
					{
						$this->ClassList[] = "TabPositionRight";
						break;
					}
					case TabContainerTabPosition::Bottom:
					{
						$this->ClassList[] = "TabPositionBottom";
						break;
					}
				}
			}
			
			$this->Controls = array();
				
			$divTabs = new HTMLControl("div");
			$divTabs->ClassList = array("Tabs", "TopLeft");
			
			$j = 0;
			foreach ($this->TabPages as $tabPage)
			{
				$aTab = new Anchor();
				$aTab->Attributes[] = new WebControlAttribute("data-id", $tabPage->ID);
				$aTab->ClassList[] = "Tab";
				if ($tabPage->Visible)
				{
					$aTab->ClassList[] = "Visible";
				}
				if (($this->SelectedTabID != null && $tabPage->ID == $this->SelectedTabID) || ($this->SelectedTab != null && ($tabPage->ID == $this->SelectedTab->ID)))
				{
					$aTab->ClassList[] = "Selected";
				}
				$aTab->TargetURL = $tabPage->TargetURL;
				$aTab->InnerHTML = $tabPage->Title;
				$divTabs->Controls[] = $aTab;
				$j++;
			}
			$this->Controls[] = $divTabs;
			
			$divTabPages = new HTMLControl("div");
			$divTabPages->ClassList[] = "TabPages";
			foreach ($this->TabPages as $tabPage)
			{
				$divTabPage = new HTMLControl("div");
				$divTabPage->ClassList[] = "TabPage";
				if (($this->SelectedTabID != null && $tabPage->ID == $this->SelectedTabID) || ($this->SelectedTab != null && ($tabPage->ID == $this->SelectedTab->ID)))
				{
					$divTabPage->ClassList[] = "Selected";
				}
				
				if (isset($tabPage->Content))
				{
					$divTabPage->Content = $tabPage->Content;
				}
				foreach ($tabPage->Controls as $ctl)
				{
					$divTabPage->Controls[] = $ctl;
				}
				$divTabPages->Controls[] = $divTabPage;
			}
			$this->Controls[] = $divTabPages;
			
			$divTabs = new HTMLControl("div");
			$divTabs->ClassList = array("Tabs", "BottomRight");
			
			$j = 0;
			foreach ($this->TabPages as $tabPage)
			{
				$aTab = new Anchor();
				$aTab->Attributes[] = new WebControlAttribute("data-id", $tabPage->ID);
				$aTab->ClassList[] = "Tab";
				if ($tabPage->Visible)
				{
					$aTab->ClassList[] = "Visible";
				}
				if (($this->SelectedTabID != null && $tabPage->ID == $this->SelectedTabID) || ($this->SelectedTab != null && ($tabPage->ID == $this->SelectedTab->ID)))
				{
					$aTab->ClassList[] = "Selected";
				}
				$aTab->TargetURL = $tabPage->TargetURL;
				$aTab->InnerHTML = $tabPage->Title;
				$divTabs->Controls[] = $aTab;
				$j++;
			}
			$this->Controls[] = $divTabs;
			
			parent::RenderBeginTag();
		}
	}
?>