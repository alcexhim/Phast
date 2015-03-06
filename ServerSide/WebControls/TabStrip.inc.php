<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	use Phast\WebControl;
	
	class TabStripTab
	{
		public $ID;
		public $Title;
		public $NavigateURL;
		public $OnClientClick;
		public $Selected;
		
		public function __construct($id, $title, $navigateURL = null, $onClientClick = null, $selected = false)
		{
			$this->ID = $id;
			$this->Title = $title;
			$this->NavigateURL = $navigateURL;
			$this->OnClientClick = $onClientClick;
			$this->Selected = $selected;
		}
	}
	class TabStrip extends WebControl
	{
		public $TabPosition;
		
		protected function RenderContent()
		{
?>
			<div class="TabStrip<?php
			switch ($this->TabPosition)
			{
				case TabContainerTabPosition::Top:
				{
					echo(" Top");
					break;
				}
				case TabContainerTabPosition::Bottom:
				{
					echo(" Bottom");
					break;
				}
				case TabContainerTabPosition::Left:
				{
					echo(" Left");
					break;
				}
				case TabContainerTabPosition::Right:
				{
					echo(" Right");
					break;
				}
			}?>" id="TabStrip_<?php echo($this->ID); ?>">
				<div class="Tabs" id="TabStrip_<?php echo($this->ID); ?>_Tabs">
<?php
				foreach ($this->Tabs as $tab)
				{
					echo("<a id=\"TabStrip_" . $this->ID . "_Tabs_" . $tab->ID . "_Tab\" class=\"Tab");
					if ($tab->Selected)
					{
						echo (" Selected");
					}
					echo("\"");
					if ($tab->OnClientClick != null)
					{
						echo(" onclick=\"" . $tab->OnClientClick . "\"");
					}
					if ($tab->NavigateURL != null)
					{
						echo(" href=\"" . System::ExpandRelativePath($tab->NavigateURL) . "\"");
					}
					else
					{
						echo(" href=\"#\"");
					}
					echo(">");
					echo($tab->Title);
					echo("</a>");
				}
?>
				</div>
			</div>
<?php
		}
	}
?>