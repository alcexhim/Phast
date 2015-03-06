<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	use Phast\WebControl;
	
	class ActionList extends WebControl
	{
		public $Items;
		
		protected function RenderContent()
		{
			echo("<div class=\"ActionList\">");
			foreach ($this->Items as $item)
			{
				if (get_class($item) == "Phast\\WebControls\\ActionListCommand")
				{
					echo("<a");
					if ($item->TargetURL != null)
					{
						echo(" href=\"" . $item->TargetURL . "\"");
					}
					else
					{
						echo(" href=\"#\"");
					}
					if ($item->TargetScript != null)
					{
						echo(" onclick=\"" . $item->TargetScript . "\"");
					}
					echo(">");
					if ($item->ImageURL != null)
					{
						echo("<img src=\"" . System::ExpandRelativePath($item->ImageURL) . "\" />");
					}
					echo("<span class=\"Title\">" . $item->Title . "</span>");
					echo("<span class=\"Description\">" . $item->Description . "</span>");
					echo("</a>");
				}
				else if (get_class($item) == "Phast\\WebControls\\ActionListSeparator")
				{
					echo("<hr />");
				}
			}
			echo("</div>");
		}
	}
	
	class ActionListItem
	{
		public $ID;
	}
	
	class ActionListCommand extends ActionListItem
	{
		public $Title;
		public $Description;
		public $ImageURL;
		public $TargetURL;
		public $TargetScript;
		
		public function __construct($id, $title, $description = null, $imageURL = null, $targetURL = null, $targetScript = null)
		{
			$this->Title = $title;
			$this->Description = $description;
			$this->ImageURL = $imageURL;
			$this->TargetURL = $targetURL;
			$this->TargetScript = $targetScript;
		}
	}
	
	class ActionListSeparator extends ActionListItem
	{
	}
?>