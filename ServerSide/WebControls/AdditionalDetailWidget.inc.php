<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	use Phast\WebControl;
	use Phast\Enumeration;
	
	use Phast\HTMLControl;
	
	use Phast\WebControls\Menu;
	
	abstract class AdditionalDetailWidgetDisplayStyle extends Enumeration
	{
		const Magnify = 1;
		const Ellipsis = 2;
		const Arrow = 3;
	}
	
	class AdditionalDetailWidget extends WebControl
	{
		public $DisplayStyle; /* AdditionalDetailWidgetDisplayStyle */
		public $Text;
		
		public $ShowText; /* bool */
		public $ShowURL; /* bool */
		
		public $TargetFrame;
		public $TargetURL;
		public $TargetScript;
		
		public $MenuItems;
		public $MenuItemHeaderText;
		
		public $PreviewContentURL;
		
		public $ClassTitle;
		
		public function __construct($id)
		{
			parent::__construct($id);
			$this->ClassTitle = "";
			$this->DisplayStyle = AdditionalDetailWidgetDisplayStyle::Ellipsis;
			$this->MenuItemHeaderText = "Available Actions";
			$this->MenuItems = array();
			$this->ShowText = true;
			$this->ShowURL = true;
		}
		
		private function RenderMenuItem($mi)
		{
			if (get_class($mi) == "Phast\\WebControls\\MenuItemCommand")
			{
				echo("<a href=\"");
				if ($mi->PostBackUrl == "")
				{
					echo("#");
				}
				else
				{
					echo(System::ExpandRelativePath($mi->PostBackUrl));
				}
				echo("\"");
				if ($mi->OnClientClick != "")
				{
					echo(" onclick=\"" . $mi->OnClientClick . "\"");
				}
				echo(">");
				echo($mi->Title);
				echo("</a>");
			}
			else if (get_class($mi) == "Phast\\WebControls\\MenuItemSeparator")
			{
				echo("<br />");
			}
		}
		
		protected function OnInitialize()
		{
			$this->TagName = "div";
			$this->ClassList[] = "AdditionalDetailWidget";
			if ($this->ShowText)
			{
				$this->ClassList[] = "Text";
			}
			switch ($this->DisplayStyle)
			{
				case AdditionalDetailWidgetDisplayStyle::Magnify:
				{
					$this->ClassList[] = "Magnify";
					break;
				}
				case AdditionalDetailWidgetDisplayStyle::Arrow:
				{
					$this->ClassList[] = "Arrow";
					break;
				}
				case AdditionalDetailWidgetDisplayStyle::Ellipsis:
				{
					$this->ClassList[] = "Ellipsis";
					break;
				}
			}
		}
		
		protected function BeforeContent()
		{
			if ($this->ShowURL)
			{
				echo("<a class=\"AdditionalDetailText\" href=\"");
				if ($this->TargetURL != "")
				{
					echo(System::ExpandRelativePath($this->TargetURL));
				}
				else
				{
					echo("#");
				}
				echo("\"");
				
				if ($this->TargetFrame != "")
				{
					echo(" target=\"" . $this->TargetFrame . "\"");
				}
	
				echo(">");
				echo($this->Text);
				echo("</a>");
			}
			else
			{
				echo ("<span class=\"AdditionalDetailText\">" . $this->Text . "</span>");
			}
			
			echo("<a class=\"AdditionalDetailButton\">&nbsp;</a>");

			echo("<div class=\"Content\">");

			echo("<div class=\"MenuItems");
			if (count($this->MenuItems) <= 0)
			{
				echo(" Empty");
			}
			echo("\">");
			echo("<div class=\"Header\">" . $this->MenuItemHeaderText . "</div>");
			
			$divContent = new HTMLControl("div");
			$divContent->ClassList[] = "Content";
			$menu = new Menu();
			foreach ($this->MenuItems as $mi)
			{
				$menu->Items[] = $mi;
			}
			$divContent->Controls[] = $menu;
			$divContent->Render();
			
			echo("</div>");

			echo("<div class=\"PreviewContent\">");
			echo("<div class=\"Header\">");
			if ($this->ClassTitle != "") echo("<span class=\"ClassTitle\">" . $this->ClassTitle . "</span>");
			if ($this->Text != "")
			{
				echo("<span class=\"ObjectTitle\">");
				echo("<a href=\"");
				if ($this->PostBackURL != "")
				{
					echo($this->PostBackURL);
				}
				else
				{
					echo("#");
				}
				echo("\"");

				echo(">");
				echo($this->Text);
				echo("</a>");
				echo("</span>");
			}
			echo("</div>");
			echo("<div class=\"Content\">");
		}
		protected function AfterContent()
		{
			echo("</div>");
			echo("</div>");

			echo("</div>");
		}
	}
?>