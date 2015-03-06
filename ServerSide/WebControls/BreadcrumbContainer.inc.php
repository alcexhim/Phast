<?php
	namespace Phast\WebControls
	{	
		class BreadcrumbItem
		{
			public $NavigateURL;
			public $Title;
			public $Selected;
			  
			public function __construct($navigateURL, $title)
			{
				$this->NavigateURL = $navigateURL;
				$this->Title = $title;
			}
		}
		class BreadcrumbContainer extends \Phast\WebControl
		{
			public $Items;
			  
			// $bc->Items = array(
			//      new BreadcrumbItem("http://www.psychatica.com/", "Psychatica")
			//      new BreadcrumbItem("http://www.psychatica.com/community", "Community"),
			//      new BreadcrumbItem("http://www.psychatica.com/community/members", "Members", true)
			//  );
			  
			protected function RenderContent()
			{
				echo("<div class=\"BreadcrumbContainer\">");
				  
				$i = 0;
				$c = count($this->Items);
				  
				foreach ($this->Items as $item)
				{
					echo("<span class=\"BreadcrumbItem\">");
					if ($i == $c - 1)
					{
						echo("<span class=\"Selected\">" . $item->Title . "</span>");
					}
					else
					{
						echo("<a href=\"" . System::ExpandRelativePath($item->NavigateURL) . "\">" . $item->Title . "</a>");
					}
					if ($i < $c - 1)
					{
						echo("<a class=\"BreadcrumbArrow\">&gt;&gt;</a>");
					}
					echo("</span>");
					$i++;
				}
				echo("</div>");
			}
		}
	}
?>