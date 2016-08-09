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
		
		public function __construct($id = null, $title = null, $imageURL = null, $targetURL = null, $targetScript = null, $visible = true)
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
		
		public function __construct()
		{
			parent::__construct();
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
				
			$ulTabs = new HTMLControl("ul");
			$ulTabs->ClassList[] = "Tabs";
			
			$j = 0;
			foreach ($this->TabPages as $tabPage)
			{
				$liTab = new HTMLControl("li");
				if ((is_bool($tabPage->Visible) && $tabPage->Visible) || (is_string($tabPage->Visible) && $tabPage->Visible != "false"))
				{
					$liTab->ClassList[] = "Visible";
				}
				if (($this->SelectedTabID != null && $tabPage->ID == $this->SelectedTabID) || ($this->SelectedTab != null && ($tabPage->ID == $this->SelectedTab->ID)))
				{
					$liTab->ClassList[] = "Selected";
				}
				
				$aTab = new Anchor();
				$aTab->Attributes[] = new WebControlAttribute("data-id", $tabPage->ID);
				$aTab->TargetURL = $tabPage->TargetURL;
				$aTab->InnerHTML = $tabPage->Title;
				$liTab->Controls[] = $aTab;
				
				$ulTabs->Controls[] = $liTab;
				$j++;
			}
			$this->Controls[] = $ulTabs;
			
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
			
			parent::RenderBeginTag();
		}
	}
?>