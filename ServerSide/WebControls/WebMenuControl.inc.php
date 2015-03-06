<?php
	namespace Phast\WebControls;
	use System;
	use Phast\WebControl;
	
	class WebMenuControl extends WebControl
	{
		public $MenuItems;
		
		protected function RenderContent()
		{
			echo("<div class=\"Menu Popup\" id=\"Popup_" . $this->ID . "_menu\">");
			foreach ($this->MenuItems as $menuItem)
			{
				if (get_class($menuItem) == "WebMenuItemCommand")
				{
					echo("<a class=\"MenuItem\" href=\"" . System::ExpandRelativePath($menuItem->NavigateUrl) . "\" onclick=\"");
					if ($menuItem->OnClientClick != null)
					{
						echo($menuItem->OnClientClick . "; ");
					}
					echo($this->ID . ".PopupMenu.Hide(); return false;\">" . $menuItem->Title . "</a>");
				}
				else if (get_class($menuItem) == "WebMenuItemHeader")
				{
					echo("<span class=\"MenuItem\"><span class=\"MenuItemTitle\">" . $menuItem->Title . "</span> <span class=\"MenuItemSubtitle\">" . $menuItem->Subtitle . "</span></span>");
				}
			}
			echo("</div>");
			echo("<script type=\"text/javascript\">var " . $this->ID . " = new Menu('" . $this->ID . "');</script>");
		}
	}
	
	class WebMenuItem
	{
	}
	class WebMenuItemHeader extends WebMenuItem
	{
		public $Title;
		public $Subtitle;
		public function __construct($title, $subtitle = null)
		{
			$this->Title = $title;
			$this->Subtitle = $subtitle;
		}
	}
	class WebMenuItemCommand extends WebMenuItem
	{
		public $Title;
		public $NavigateUrl;
		public $OnClientClick;
		
		public function __construct($title, $navigateUrl = "#", $onClientClick = null)
		{
			$this->Title = $title;
			$this->NavigateUrl = $navigateUrl;
			$this->OnClientClick = $onClientClick;
		}
	}
	class WebMenuItemMenu extends WebMenuItem
	{
		public $Title;
		public $MenuItems;
		
		public function __construct($title, $menuItems = array())
		{
			$this->Title = $title;
			$this->MenuItems = $menuItems;
		}
	}
?>