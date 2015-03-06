<?php
	namespace Phast\WebControls;
	
	use Phast\WebControl;
	use Phast\System;
	
	class Ribbon extends WebControl
	{
		public $Title;
		public $ImageURL;
		
		public $Collapsed;
		
		public $ApplicationMenu;
		public $Commands;
		public $HelpButton;
		public $Tabs;
		
		public $SelectedTab;
		
		public $UserName;
		
		public function __construct($id, $title = null)
		{
			parent::__construct($id);
			$this->Title = $title;
			$this->Collapsed = false;
			$this->ApplicationMenu = new RibbonApplicationMenuSettings();
			$this->HelpButton = new RibbonHelpButtonSettings();
			$this->Commands = array();
			$this->Tabs = array();
			
			$this->TagName = "div";
		}
		
		public function GetCommandByID($id)
		{
			foreach ($this->Commands as $command)
			{
				if ($command->ID == $id) return $command;
			}
			return null;
		}
		public function GetTabByID($id)
		{
			foreach ($this->Tabs as $tab)
			{
				if ($tab->ID === $id) return $tab;
			}
			return null;
		}
		
		protected function OnInitialize()
		{
			$this->Collapsed = ($this->GetClientProperty("Collapsed", "false") == "true");
			$SelectedTabID = ($this->GetClientProperty("ActiveTabID", null));
			if ($SelectedTabID != null)
			{
				$this->SelectedTab = $this->GetTabByID($SelectedTabID);
			}
		}
		
		private function RenderRibbonTab($tab)
		{
			echo("<a class=\"RibbonTab");
			if ($this->SelectedTab->ID === $tab->ID)
			{
				echo(" Selected");
			}
			echo("\" data-tab-id=\"" . $tab->ID . "\" data-tooltip-title=\"" . $tab->ToolTipTitle . "\" data-tooltip-content=\"" . $tab->ToolTipText . "\" href=\"#\"");
			if (!$tab->Visible)
			{
				echo(" style=\"display: none;\"");
			}
			echo(">");
			if ($tab->ImageURL != null)
			{
				echo("<img src=\"" . System::ExpandRelativePath($tab->ImageURL) . "\" />");
			}
			echo($tab->Title);

			echo("</a>");
		}
		private function RenderRibbonItem($item)
		{
			if (get_class($item) == "Phast\\WebControls\\RibbonCommandReferenceItem")
			{
				$this->RenderRibbonCommand($this->GetCommandByID($item->TargetID));
			}
			else if (get_class($item) == "Phast\\WebControls\\RibbonSeparatorItem")
			{
				echo("<span class=\"Separator\">&nbsp;</span>");
			}
		}
		private function RenderRibbonCommand($command)
		{
			if ($command == null) return;

			echo("<div data-tooltip-title=\"" . $command->ToolTipTitle . "\" data-tooltip-content=\"" . $command->ToolTipText . "\" class=\"RibbonCommand Ribbon_" . $this->ID . "_Commands_" . $command->ID);
			if ($command->Selected)
			{
				echo(" Selected");
			}
			if (!$command->Enabled)
			{
				echo(" Disabled");
			}
			
			if (get_class($command) == "Phast\\WebControls\\RibbonDropDownCommand")
			{
				echo(" RibbonDropDownCommand");
				echo("\">");
				
				$titleText = $command->Title;
				$accessKey = null;
				$iof = stripos($titleText, "&");
				if ($iof !== false)
				{
					$titleTextBefore = substr($titleText, 0, $iof);
					$titleTextAfter = substr($titleText, $iof . 2);
					$accessKey = substr($titleText, $iof . 1, 1);
					$titleText = $titleTextBefore . "<u>" . $accessKey . "</u>" . $titleTextAfter;
				}

				echo("<script type=\"text/javascript\">var " . $command->ID . " = new RibbonDropDownCommand('Ribbon_" . $this->ID . "_Commands_" . $command->ID . "');</script>");
				echo("<a ");

				if ($accessKey != null)
				{
					echo(" data-accesskey=\"" . $accessKey . "\"");
				}
				if ($command->TargetURL != null)
				{
					echo(" href=\"" . System::ExpandRelativePath($command->TargetURL) . "\"");
				}
				else
				{
					echo(" href=\"#\"");
				}
				if ($command->TargetFrame != null)
				{
					echo(" target=\"" . $command->TargetFrame . "\"");
				}

				$onclickstr = "var ribbon = Ribbon.FromID('" . $this->ID . "'); ribbon.SetApplicationMenuVisible(false);";

				$onclickstr .= "if (ribbon.IsCollapsed() && ribbon.IsOpened())";
				$onclickstr .= "{ ribbon.SetOpened(false); };";
				$onclickstr .= $command->ID . ".ToggleSelected();";
				/*
				if ($command->TargetScript != null)
				{
					$onclickstr .= $command->TargetScript;
				}
				*/
				echo(" onclick=\"" . $onclickstr . "\"");
				echo(">");

				if ($command->ImageURL != null)
				{
					echo("<img class=\"Icon\" src=\"" . System::ExpandRelativePath($command->ImageURL) . "\" />");
				}

				echo("<span class=\"Text\">");
				echo($titleText);
				echo("</span>");

				echo("<span class=\"SpacerText\">");
				echo($titleText);
				echo("</span>");

				echo("<img class=\"DropDownImage\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAECAYAAABGM/VAAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3gMNCw0c/NC4EQAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAR0lEQVQI12P0Saj6z4AGmBZMbEARWDCxgYHhzrMvDG8+/vrvk1D1/83HX//vPPvCwHjn2RcGBgYGBgFutv8fvv5iZGBgYAAAlbYbf1Hz7NoAAAAASUVORK5CYII=\" />");

				echo("</a>");
				
				echo("<div class=\"RibbonDropDownItems Ribbon_" . $this->ID . "_Commands_" . $command->ID . "_DropDownItems\">");
				echo("<div class=\"ImageMarginBackground\">&nbsp;</div>");
				foreach ($command->Items as $item)
				{
					$this->RenderRibbonItem($item);
				}
				echo("</div>");
			}
			else if (get_class($command) == "Phast\\WebControls\\RibbonButtonCommand")
			{
				echo(" RibbonButtonCommand");
				echo("\">");
				
				$titleText = $command->Title;
				$accessKey = null;
				$iof = stripos($titleText, "&");
				if ($iof !== false)
				{
					$titleTextBefore = substr($titleText, 0, $iof);
					$titleTextAfter = substr($titleText, $iof . 2);
					$accessKey = substr($titleText, $iof . 1, 1);
					$titleText = $titleTextBefore . "<u>" . $accessKey . "</u>" . $titleTextAfter;
				}

				echo("<script type=\"text/javascript\">var " . $command->ID . " = new RibbonButtonCommand('Ribbon_" . $this->ID . "_Commands_" . $command->ID . "');</script>");
				echo("<a ");

				if ($accessKey != null)
				{
					echo(" data-accesskey=\"" . $accessKey . "\"");
				}
				if ($command->TargetURL != null)
				{
					echo(" href=\"" . System::ExpandRelativePath($command->TargetURL) . "\"");
				}
				else
				{
					echo(" href=\"#\"");
				}
				if ($command->TargetFrame != null)
				{
					echo(" target=\"" . $command->TargetFrame . "\"");
				}

				$onclickstr = "var ribbon = Ribbon.FromID('" . $this->ID . "'); ribbon.SetApplicationMenuVisible(false);";

				$onclickstr .= "if (ribbon.IsCollapsed() && ribbon.IsOpened())";
				$onclickstr .= "{ ribbon.SetOpened(false); };";
				if ($command->TargetScript != null)
				{
					$onclickstr .= $command->TargetScript;
				}
				echo(" onclick=\"" . $onclickstr . "\"");
				echo(">");

				if ($command->ImageURL != null)
				{
					echo("<img class=\"Icon\" src=\"" . System::ExpandRelativePath($command->ImageURL) . "\" />");
				}

				echo("<span class=\"Text\">");
				echo($titleText);
				echo("</span>");

				echo("<span class=\"SpacerText\">");
				echo($titleText);
				echo("</span>");

				echo("</a>");
			}
			echo("</div>");
		}
		
		private function RenderRibbonTabGroup($group)
		{
			echo("<div class=\"RibbonTabGroup\"");
			if (!$group->Visible) echo(" style=\"display: none;\"");
			echo(">");
			echo("<div class=\"RibbonTabGroupBackground\">&nbsp;</div>");
			echo("<div class=\"RibbonTabGroupContent\">");
			foreach ($group->Items as $item)
			{
				$this->RenderRibbonItem($item);
			}
			echo("</div>");
			echo("<div class=\"RibbonTabGroupTitle\">" . $group->Title . "</div>");
			echo("</div>");
			echo("<span class=\"Separator\"");
			if (!$group->Visible) echo(" style=\"display: none;\"");
			echo(">&nbsp;</span>");
		}
		
		protected function RenderContent()
		{
			if ($this->Title != null || $this->ImageURL != null)
			{
				echo("<div class=\"RibbonTitleBar\">");
				if ($this->ImageURL != null)
				{
					echo("<img src=\"" . System::ExpandRelativePath($this->ImageURL) . "\" /> ");
				}
				if ($this->Title != null)
				{
					echo("<span class=\"Text\">" . $this->Title . "</span>");
				}
				echo("</div>");
			}
			echo ("<div class=\"Ribbon");
			if ($this->Collapsed)
			{
				echo(" Collapsed");
			}
			echo("\" data-id=\"" . $this->ID . "\">");
			
			echo("<a id=\"Ribbon_" . $this->ID . "_ApplicationButton\" class=\"ApplicationButton\" data-tooltip-title=\"" . $this->ApplicationMenu->ToolTipTitle . "\" data-tooltip-content=\"" . $this->ApplicationMenu->ToolTipText . "\">");
			echo("<img class=\"Icon\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAALCAYAAABhwJ3wAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3gIcEwcFpaQT2QAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAACW0lEQVQ4y3WTS4sVVxSFv33vqUduJ62ooQUVGhRaElQcBASTQaATW3CoE9H8CSeC0JHuQWfgLwgZCf4B8RVQBCGIBJJBHghOkoCgA1/d9PXWedRyUFW3b2vnTHadfdbZq9baZ9vj/16wf+8O0a6//n1tR2a3j/dbrX+er9rZlZuU23YDILVwCdQEBCBUN9HN7dshHxtg7mx8qctttWZnpuWHQ3NTdYPvoGJir4awjU5AShHJyJwjtbgYE/Pf37dNDIJ7y19LzhGDCD5NnLWFJ4gmc66qPDEmJKiqGl95utydxS9lEz8MEGLAqIkpEkMaWxfWXxJHaw2o1geWOh8CMSSEAIcPAR8CIUTmF3+xSTUAd5ePCzlSECHUYwWjtTc8+vH8Fn43quzV6zd6O/IYoiwLfv3jqX1xaI9GowoBNsHTfZdlwdy3l80NZpqkIPk1FIY8/nlRo9GI9eGQqcGAsiw5eGLJnOqa4CtAFHmGJFTXeF9x7LtrTW0DU1Px4dVzKvIMsdnSfv4JCZhbWLK/b11SmQqyPOPgwpJZNsClOuG9ByDViVo1TS7w4Kcz4j1Fla8Y1CX0etDvjYkM6H20DSrj81Mr9uf1i/rs1Ir18ymsmMal2BIZpJhIEikmfPAsXLhtnV+dottXTirFRD93uDLbIBL0AMqdpGqVw6evWDY1Tb+cBoGTRIyxbZqwXjNLMQRu/PBNU942BjDGgCT6RU6/LNuD7nX1ENArirZ1rQ8y3IPfnthXRw+oe44d0ae7dv3vwP7+5Jllg49xRY4h1DSxLdrIa8aiozPeARJkctn1QJKkAAAAAElFTkSuQmCC\" />");
			echo("</a>");

			echo("<div class=\"ApplicationMenu\" id=\"Ribbon_" . $this->ID . "_ApplicationMenu\">");
			echo("<div class=\"ApplicationMenuHeader\">&nbsp;</div>");
			echo("<div class=\"ApplicationMenuItems\">");
			foreach ($this->ApplicationMenu->Commands as $item)
			{
				$this->RenderRibbonCommand($item);
			}
			echo("</div>");
			echo("</div>");

			echo("<div class=\"RibbonTabContainer\" id=\"Ribbon_" . $this->ID . "_TabContainer\">");
			foreach ($this->Tabs as $tab)
			{
				$this->RenderRibbonTab($tab);
			}
			/*
			foreach (RibbonTabContext ctx in $this->Contexts)
			{
				echo("<div class=\"RibbonTabContext\" style=\"");

				HSLColor borderColor = new HSLColor(ctx.BackgroundColor);
				HSLColor startColor = new HSLColor(borderColor);
				startColor.Hue .= 10;
				startColor.Saturation -= 54;
				startColor.Luminosity .= 10;
				HSLColor endColor = new HSLColor(borderColor);
				endColor.Saturation -= 54;
				endColor.Luminosity .= 20;

				string css = "border-color: " . GradientGenerator.GetColorCSS(borderColor) . ";";
				css .= GradientGenerator.GenerateCSS(new GradientColorStop(0.0, startColor), new GradientColorStop(1.0, endColor));
				echo(css);

				if (ctx.Active)
				{
					echo(" display: block;\"");
				}
				echo("\">");
				echo("<div class=\"Title\">");
				echo(ctx.Title);
				echo("</div>");
				foreach (RibbonTab tab in ctx.Tabs)
				{
					$this->RenderRibbonTab(tab, writer);
				}
				echo("</div>");
			}
			*/
			echo("</div>");

			echo("<div class=\"RibbonTabContentContainer\" id=\"Ribbon_" . $this->ID . "_TabContentContainer\">");
			foreach ($this->Tabs as $tab)
			{
				echo("<div class=\"RibbonTabContent");
				if ($tab->ID === $this->SelectedTab->ID)
				{
					echo(" Selected");
				}
				echo("\" data-tab-id=\"" . $tab->ID . "\"");
				echo(">");
				foreach ($tab->Groups as $tgroup)
				{
					$this->RenderRibbonTabGroup($tgroup);
				}
				echo("</div>");
			}
			echo("</div>");

			if ($this->UserName != null)
			{
				echo("<span class=\"UserName\">" . $this->UserName . "</span>");
			}

			echo("<a class=\"HelpButton\" id=\"Ribbon_" . $this->ID . "_HelpButton\" data-tooltip-title=\"" . $this->HelpButton->ToolTipTitle . "\" data-tooltip-content=\"" . $this->HelpButton->ToolTipText . "\"");
			if ($this->HelpButton->TargetURL != null)
			{
				echo(" href=\"" . $this->HelpButton->TargetURL . "\"");
				if ($this->HelpButton->TargetFrame != null)
				{
					echo(" target=\"" . $this->HelpButton->TargetFrame . "\"");
				}
			}
			else
			{
				echo(" href=\"#\"");
			}
			if ($this->HelpButton->TargetScript != null)
			{
				echo(" onclick=\"" . $this->HelpButton->TargetScript . "\"");
			}
			if ($this->HelpButton->Visible)
			{
				echo(" style=\"display: inline-block\"");
			}
			echo(">");

			echo("<img src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAOCAYAAAAfSC3RAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3gMDEQIZ+kwBxAAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAACPklEQVQoz0WST0gUYRjGf7Nqu4kxg2n+Kd0BIwapGNgQpQ5DpzoEe8ujN7vlsUu0l87VTbpkkGDQYQ0KDyojRGypMEjlSJJjbamZ9H2xa6PQvh0G9T0/v+eB53mNta0qhyfC9p+/Cyvl/dzmz39sV39zuq2ZgXOZxVbr+CXEOJQaB2Blt8bLxR25fN7CtU3MdCLQexBEmpmgzOBAp9GYSR+Bld0awbcdudGXJQjBDyGIEtC1wXM0tm0yPr3ENffMcPOJzGPWNivcfxGJikXGfBHvjsjYlIiKRUREIiUyMioyNqUkUiJ3n76V1Y0Kxvsv2wu7+w05YpPCRJLwYBgm30GxBPl+8FzIFzQjeQBFU6ae1NyH/Zxrm/ghEEOk4NEkFCbADzV+SWOmwcpA0Y/wnCzzH9dJLa7+wkxDEIICogjGpkDF4DlQuGUyt6IJggA/UJgmzM4vU39Qr4qTRIWGGFwHntw2mZzTjIwGKBTW0XCk2prq0Hvg2hoVa1AKYoVnJ4Khh1FiCri2hdaai90d1Of7ThFEGs+Foq9QKIiTSdRzBSRGAHnPJggVV/q6SHW1NxqvFxSuYyatxYnYc+DezewhNDJo4/VbvAlXudCTNYy1rSplvSszn6oMeVn80jrFkiIIE8B1LPL9Fp5rMf5qiasDWdpPtiRgyjD4ulOVZ7PLXM914zkWpmkmL6c1QahYLq/j9tp0trQaNZGjXwWgJoTff8h06TPR6gb22Q5ajjXg9Lbj9mSNVF0DNREA/gPgtSGQXugIvQAAAABJRU5ErkJggg==\" alt=\"Help\" />");
			echo("</a>");

			echo("</div>");
			echo("</div>");
			echo("<div id=\"Ribbon_" . $this->ID . "_Spacer\" class=\"RibbonSpacer");
			if ($this->Collapsed) echo(" Collapsed");
			echo("\">");
		}
		protected function RenderBeginTag()
		{
			parent::RenderBeginTag();
			echo("<div class=\"RibbonContainer\">");
		}
		protected function RenderEndTag()
		{
			parent::RenderEndTag();
			echo("</div>");
			
			echo("<div class=\"RibbonTooltip\" id=\"Tooltip\"><div class=\"Title\" id=\"Tooltip_Title\"></div><div class=\"Content\" id=\"Tooltip_Content\"></div><div class=\"ContextHelp\" id=\"Tooltip_ContextHelp\"><span class=\"Separator\">&nbsp;</span><div class=\"Content\">Press F1 for more help.</div></div></div>");
		}
	}
	class RibbonTab
	{
		public $ID;
		public $Title;
		public $ImageURL;
		public $ToolTipTitle;
		public $ToolTipText;
		
		public $Groups;
		
		public $Visible;
		
		public function __construct($id, $title = null, $groups = null, $imageURL = null, $tooltipTitle = null, $tooltipText = null, $visible = true)
		{
			if ($groups == null) $groups = array();
			
			$this->ID = $id;
			$this->Title = $title;
			$this->Groups = $groups;
			$this->ImageURL = $imageURL;
			$this->ToolTipTitle = $tooltipTitle;
			$this->ToolTipText = $tooltipText;
			$this->Visible = $visible;
		}
		
		public function GetTabGroupByID($id)
		{
			foreach ($this->Groups as $group)
			{
				if ($group->ID == $id) return $group;
			}
			return null;
		}
	}
	class RibbonTabGroup
	{
		public $ID;
		public $Title;
		public $Items;
		public $Visible;
		
		public function __construct($id, $title, $items = null)
		{
			if ($items == null) $items = array();
			
			$this->ID = $id;
			$this->Title = $title;
			$this->Items = $items;
			$this->Visible = true;
		}
	}
	
	abstract class RibbonCommand
	{
		public $ID;
		public $Enabled;
		
		public function __construct($id)
		{
			$this->ID = $id;
			$this->Enabled = true;
		}
	}
	class RibbonButtonCommand extends RibbonCommand
	{
		public $Title;
		public $ImageURL;
		
		public $TargetURL;
		public $TargetScript;
		public $TargetFrame;
		
		public $ToolTipTitle;
		public $ToolTipText;
		
		public $Selected;
		
		public function __construct($id, $title, $targetURL = null, $targetScript = null, $imageURL = null, $toolTipTitle = null, $toolTipText = null)
		{
			parent::__construct($id);
			$this->Title = $title;
			
			$this->TargetURL = $targetURL;
			$this->TargetScript = $targetScript;
			
			$this->ImageURL = $imageURL;
			$this->ToolTipTitle = $toolTipTitle;
			$this->ToolTipText = $toolTipText;
		}
	}
	class RibbonDropDownCommand extends RibbonButtonCommand
	{
		public $Items;
		
		public function __construct($id, $title, $targetURL = null, $targetScript = null, $imageURL = null, $toolTipTitle = null, $toolTipText = null, $commands = null)
		{
			parent::__construct($id, $title, $targetURL, $targetScript, $imageURL, $toolTipTitle, $toolTipText);
			
			if ($commands == null) $commands = array();
			$this->Items = $commands;
		}
	}
	
	abstract class RibbonItem
	{
	}
	class RibbonCommandReferenceItem extends RibbonItem
	{
		public $TargetID;
		public function __construct($targetID)
		{
			$this->TargetID = $targetID;
		}
	}
	class RibbonSeparatorItem extends RibbonItem
	{
	}
	
	class RibbonApplicationMenuSettings
	{
		public $Title;
		public $Commands;
		
		public $ToolTipTitle;
		public $ToolTipText;
		
		public function __construct()
		{
			$this->Commands = array();
		}
	}
	class RibbonHelpButtonSettings
	{
		public $ToolTipTitle;
		public $ToolTipText;
		
		public $TargetURL;
		public $TargetFrame;
		public $TargetScript;
		
		public $Visible;
	}
?>