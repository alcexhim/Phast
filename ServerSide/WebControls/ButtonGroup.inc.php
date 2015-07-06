<?php
	namespace Phast\WebControls;
	
	use Phast\Enumeration;
	use Phast\System;
	use Phast\WebControl;
	use Phast\HorizontalAlignment;
	
	class ButtonGroupImagePosition extends Enumeration
	{
		const AboveText = 1;
		const BelowText = 2;
	}
	class ButtonGroupOrientation extends Enumeration
	{
		const Horizontal = 1;
		const Vertical = 2;
	}
	
	class ButtonGroupButtonAspectRatioPreservationMode extends Enumeration
	{
		const None = 0;
		const FitWidth = 1;
		const FitHeight = 2;
		const FitBoth = 3;
	}
	
	class ButtonGroup extends WebControl
	{
		public $Orientation;
		public $ButtonSize;
		public $ButtonWidth;
		public $ButtonHeight;
		public $Items;
		
		public function __construct($id)
		{
			parent::__construct($id);
			$this->Orientation = ButtonGroupOrientation::Horizontal;
			$this->ParseChildElements = true;
		}
		
		protected function RenderContent()
		{
			?>
			<div class="ButtonGroup<?php switch ($this->Orientation)
			{
				case "Vertical":
				case ButtonGroupOrientation::Vertical:
				{
					echo(" ButtonGroupVertical");
					break;
				}
				case "Horizontal":
				case ButtonGroupOrientation::Horizontal:
				{
					echo(" ButtonGroupHorizontal");
					break;
				}
			} ?>" style="<?php switch($this->HorizontalAlignment)
			{
				case "Left":
				case HorizontalAlignment::Left:
				{
					echo("text-align: left;");
					break;
				}
				case "Center":
				case HorizontalAlignment::Center:
				{
					echo("text-align: center;");
					break;
				}
				case "Right":
				case HorizontalAlignment::Right:
				{
					echo("text-align: right;");
					break;
				}
			}?>">
			<?php
			$buttonWidth = 160;
			$buttonHeight = 160;
			$buttonActualWidth = 128;
			$buttonActualHeight = 128;
			
			if (is_numeric($this->ButtonSize))
			{
				$buttonWidth = $this->ButtonSize + 32;
				$buttonHeight = $this->ButtonSize + 32;
				$buttonActualWidth = $this->ButtonSize;
				$buttonActualHeight = $this->ButtonSize;
			}
			else
			{
				if (is_numeric($this->ButtonWidth))
				{
					$buttonWidth = $this->ButtonWidth + 32;
					$buttonActualWidth = $this->ButtonWidth;
				}
				if (is_numeric($this->ButtonHeight))
				{
					$buttonHeight = $this->ButtonHeight + 32;
					$buttonActualHeight = $this->ButtonHeight;
				}
			}
			
			if (is_array($this->Items))
			{
				foreach ($this->Items as $item)
				{
					?>
					<a class="ButtonGroupButton"<?php if ($item->NavigationURL != null) { echo (" href=\"" . System::ExpandRelativePath($item->NavigationURL) . "\""); } if ($item->OnClientClick != null) { echo (" onclick=\"" . $item->OnClientClick . "\""); } echo (" style=\"width: " . $buttonWidth . "px; height: " . $buttonHeight . "px; visibility: " . ($item->Visible ? "visible" : "hidden") . ";\""); ?>>
						<?php
						if ($item->ImagePosition == ButtonGroupImagePosition::AboveText)
						{
						?>
						<img src="<?php echo(System::ExpandRelativePath($item->ImageURL)); ?>" title="<?php echo($item->Title); ?>" style="<?php
						switch ($item->AspectRatioPreservationMode)
						{
							case ButtonGroupButtonAspectRatioPreservationMode::FitWidth:
							{
								echo("width: " . $buttonActualWidth . "px;");
								break;
							}
							case ButtonGroupButtonAspectRatioPreservationMode::FitHeight:
							{
								echo("height: " . $buttonActualHeight . "px;");
								break;
							}
							case ButtonGroupButtonAspectRatioPreservationMode::FitBoth:
							{
								echo("width: " . $buttonActualWidth . "px;");
								echo("height: " . $buttonActualHeight . "px;");
								break;
							}
						}?>" />
						<?php
						}
						?>
						<span class="ButtonGroupButtonText"><?php echo($item->Title); ?></span>
						<?php
						if ($item->ImagePosition == ButtonGroupImagePosition::BelowText)
						{
						?>
						<img src="<?php echo(System::ExpandRelativePath($item->ImageURL)); ?>" title="<?php echo($item->Title); ?>" style="<?php
						switch ($item->AspectRatioPreservationMode)
						{
							case ButtonGroupButtonAspectRatioPreservationMode::FitWidth:
							{
								echo("width: " . $buttonActualWidth . "px;");
								break;
							}
							case ButtonGroupButtonAspectRatioPreservationMode::FitHeight:
							{
								echo("height: " . $buttonActualHeight . "px;");
								break;
							}
							case ButtonGroupButtonAspectRatioPreservationMode::FitBoth:
							{
								echo("width: " . $buttonActualWidth . "px;");
								echo("height: " . $buttonActualHeight . "px;");
								break;
							}
						}
						?>" />
					<?php
					}
					?>
					</a>
					<?php
				}
			}
			?></div><?php
		}
	}
	class ButtonGroupItem
	{
	}
	class ButtonGroupButton extends ButtonGroupItem
	{
		public $Name;
		public $Title;
		public $Description;
		public $ImageURL;
		public $NavigationURL;
		public $OnClientClick;
		public $ImagePosition;
		public $AspectRatioPreservationMode;
		public $Visible;
		
		public function __construct($name, $title, $description = null, $imageURL = null, $navigationURL = null, $onClientClick = null)
		{
			$this->Name = $name;
			$this->Title = $title;
			$this->Description = $description;
			$this->ImageURL = $imageURL;
			$this->NavigationURL = $navigationURL;
			$this->OnClientClick = $onClientClick;
			$this->ImagePosition = ButtonGroupImagePosition::AboveText;
			$this->AspectRatioPreservationMode = ButtonGroupButtonAspectRatioPreservationMode::FitHeight;
			$this->Visible = true;
		}
	}
	class ButtonGroupSeparator extends ButtonGroupItem
	{
	}
	class ButtonGroupLineBreak extends ButtonGroupItem
	{
	}
?>