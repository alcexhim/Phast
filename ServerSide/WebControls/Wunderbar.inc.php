<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	
	class Wunderbar extends \Phast\WebControl
	{
		public $Items;
		public $SelectedItem;
		
		public function __construct($id)
		{
			$this->ID = $id;
			$this->Items = array();
		}
		
		public function GetItemByID($id)
		{
			foreach ($this->Items as $item)
			{
				if ($item->ID == $id) return $item;
			}
			return null;
		}
		
		protected function RenderContent()
		{
			echo("<div class=\"Wunderbar\">");
			echo("<div class=\"WunderbarContents\">");
			foreach ($this->Items as $pane)
			{
				echo("<div class=\"WunderbarContent\">");
				if (is_callable($pane->Content))
				{
					call_user_func($pane->Content);
				}
				else
				{
					echo($pane->Content);
				}
				echo("</div>");
			}
			echo("</div>");
			echo("<div class=\"WunderbarButtons\">");
			foreach ($this->Items as $pane)
			{
				echo("<a");
				if ($pane == $this->SelectedItem)
				{
					echo(" class=\"Selected\"");
				}
				echo(" href=\"");
				if ($pane->ButtonTargetURL != null)
				{
					echo(System::ExpandRelativePath($pane->ButtonTargetURL));
				}
				else
				{
					echo("#");
				}
				echo("\"");
				if ($pane->ButtonTargetFrame != null)
				{
					echo(" target=\"" . $pane->ButtonTargetFrame . "\"");
				}
				if (!$pane->InhibitDefaultBehavior)
				{
					echo(" onclick=\"" . $this->ID . ".SetSelectedPane('" . $pane->ID . "');");
					if ($pane->ButtonOnClientClick != null)
					{
						echo(" onclick=\"" . $pane->ButtonOnClientClick . "\"");
					}
					echo("\"");
				}
				else
				{
					if ($pane->ButtonOnClientClick != null)
					{
						echo(" onclick=\"" . $pane->ButtonOnClientClick . "\"");
					}
				}
				echo(">");
				
				if ($pane->ImageURL != null) echo("<img class=\"Icon\" src=\"" . System::ExpandRelativePath($pane->ImageURL) . "\" />");
				echo("<span class=\"Text\">" . $pane->Title . "</span>");
				echo("</a>");
			}
			echo("</div>");
			echo("</div>");
		}
	}
	class WunderbarPanel
	{
		public $ID;
		public $Title;
		public $Content;
		public $ImageURL;
		
		public $ButtonTargetURL;
		public $ButtonTargetFrame;
		public $ButtonOnClientClick;
		
		public $InhibitDefaultBehavior;
		
		public function __construct($id, $title, $contentOrContentFunction, $imageURL = null, $buttonTargetURL = null, $buttonOnClientClick = null, $inhibitDefaultBehavior = false)
		{
			$this->ID = $id;
			$this->Title = $title;
			$this->Content = $contentOrContentFunction;
			$this->ImageURL = $imageURL;
			
			$this->ButtonTargetURL = $buttonTargetURL;
			$this->ButtonOnClientClick = $buttonOnClientClick;
			
			$this->InhibitDefaultBehavior = $inhibitDefaultBehavior;
		}
	}
?>