<?php
	namespace Phast\WebControls;
	use System;
	use Phast\WebControl;
	
	class WebMenuLinkControl extends WebControl
	{
		public $ID;
		public $Title;
		public $NavigateUrl;
		public $OnClientClick;
		public $MenuItems;
		
		public function __construct($id = "", $title = null, $navigateUrl = "#", $onClientClick = null, $menuItems = array())
		{
			if ($title == null) $title = $name;
			$this->ID = $id;
			$this->Title = $title;
			$this->NavigateUrl = $navigateUrl;
			$this->OnClientClick = $onClientClick;
			$this->MenuItems = $menuItems;
		}
		
		protected function RenderContent()
		{
			echo("<div class=\"Menu Popup\" id=\"Popup_" . $this->ID . "_menu\">");
			
			foreach ($this->MenuItems as $menuItem)
			{
				if (get_class($menuItem) == "Phast\\WebControls\\WebMenuItemCommand")
				{
					echo("<a class=\"MenuItem\" href=\"" . System::ExpandRelativePath($menuItem->NavigateUrl) . "\" onclick=\"");
					if ($menuItem->OnClientClick != null)
					{
						echo($menuItem->OnClientClick . "; ");
					}
					echo($this->ID . ".PopupMenu.Hide(); return false;\">" . $menuItem->Title . "</a>");
				}
				else if (get_class($menuItem) == "Phast\\WebControls\\WebMenuItemHeader")
				{
					echo("<span class=\"MenuItem\"><span class=\"MenuItemTitle\">" . $menuItem->Title . "</span> <span class=\"MenuItemSubtitle\">" . $menuItem->Subtitle . "</span></span>");
				}
				else
				{
					echo("<!-- WebMenuLinkControl: Undefined menuItem class '" . get_class($menuItem) . "' -->");
				}
			}
			echo("</div>");
			
			echo("<span id=\"MenuLink_" . $this->ID . "\" class=\"MenuLink\">");
			if (count($this->MenuItems) > 0)
			{
				echo("<a href=\"#\" onclick=\"" . $this->ID . ".PopupMenu.Show(); event.preventDefault(); event.stopPropagation(); return false;\"><span class=\"Arrow Down\">&nbsp;</span></a>");
			}
			echo("<a href=\"" . System::ExpandRelativePath($this->NavigateUrl) . "\"" . ($this->OnClientClick != null ? (" onclick=\"" . $this->OnClientClick . "\"") : "") . ">" . $this->Title . "</a></span>");
			
			echo("<script type=\"text/javascript\">var " . $this->ID . " = new WebMenuLinkControl('" . $this->ID . "');</script>");
		}
	}
?>