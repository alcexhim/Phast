<?php
	namespace Phast\WebControls;
	use System;
	use Phast\WebControl;
	
	class WebActionListControl extends WebControl
	{
		public $Items;
		
		private function RenderItemRecursive($item)
		{
			if (get_class($item) == "WebActionListGroup")
			{
				echo("<div class=\"ActionListGroup\">");
				echo("<div class=\"ActionListGroupTitle\">" . $item->Title . "</div>");
				echo("<div class=\"ActionListGroupContent\">");
				foreach ($item->Items as $item1)
				{
					$this->RenderItemRecursive($item1);
				}
				if ($item->MoreCommand != null)
				{
					echo("<a class=\"More\" href=\"" . System::ExpandRelativePath($item->MoreCommand->NavigateUrl) . "\">" . $item->MoreCommand->Title . "</a>");
				}
				echo("</div>");
				echo("</div>");
			}
			else if (get_class($item) == "WebActionListCommand")
			{
				echo("<div class=\"ActionListCommand\">");
				
				if (count($item->MenuItems) > 0)
				{
					echo("<a class=\"DropDownButton\" href=\"#\"><span class=\"Arrow Down\">&nbsp;</span></a>");
				}
				echo("<a class=\"ActionListCommandLink\" href=\"" . System::ExpandRelativePath($item->NavigateUrl) . "\"");
				if ($item->OnClientClick != null)
				{
					echo(" onclick=\"" . $item->OnClientClick . "\"");
				}
				echo(">" . $item->Title . "</a>");
				
				echo("</div>");
			}
			else if (get_class($item) == "WebActionListSeparator")
			{
				echo("<hr />");
			}
		}
		protected function RenderContent()
		{
			echo("<div class=\"ActionList\">");
			foreach ($this->Items as $item)
			{
				$this->RenderItemRecursive($item);
			}
			echo("</div>");
		}
	}
	
	abstract class WebActionListItem
	{
	}
	
	class WebActionListGroup extends WebActionListItem
	{
		public $Title;
		public $Items;
		public $MoreCommand;
		
		public function __construct($title)
		{
			$this->Title = $title;
		}
	}
	class WebActionListCommand extends WebActionListItem
	{
		public $Title;
		public $NavigateUrl;
		public $OnClientClick;
		public $MenuItems;
		
		public function __construct($title, $navigateUrl = "#", $onClientClick = null, $menuItems = array())
		{
			$this->Title = $title;
			$this->NavigateUrl = $navigateUrl;
			$this->OnClientClick = $onClientClick;
			$this->MenuItems = $menuItems;
		}
	}
?>