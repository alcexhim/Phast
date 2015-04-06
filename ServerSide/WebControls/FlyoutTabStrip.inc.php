<?php
	namespace Phast\WebControls;
	
	use Phast\Enumeration;
	use Phast\WebControl;
	
	class FlyoutTabStripPosition extends Enumeration
	{
		const Top = 1;
		const Bottom = 2;
		const Left = 3;
		const Right = 4;
	}
	
	class FlyoutTabStripItem
	{
		public $ID;
		public $Title;
		public $ImageURL;
		public $Content;
		public $RenderContent;
		
		public function __construct($id, $title, $imageUrl = null, $contentOrFunction = null)
		{
			$this->ID = $id;
			$this->Title = $title;
			$this->ImageURL = $imageURL;
			if ($contentOrFunction != null)
			{
				if (is_callable($contentOrFunction))
				{
					$this->Content = null;
					$this->RenderContent = $contentOrFunction;
				}
				else
				{
					$this->Content = $contentOrFunction;
					$this->RenderContent = null;
				}
			}
		}
	}
	class FlyoutTabStrip extends WebControl
	{
		public $Items;
		public $Position;
		
		public function __construct($id)
		{
			parent::__construct($id);
			$this->Position = FlyoutTabStripPosition::Right;
		}
	
		protected function RenderContent()
		{
		?>
			<div class="FlyoutTabStrip <?php switch ($this->Position)
			{
				case FlyoutTabStripPosition::Top:
				{
					echo("Top");
					break;
				}
				case FlyoutTabStripPosition::Bottom:
				{
					echo("Bottom");
					break;
				}
				case FlyoutTabStripPosition::Left:
				{
					echo("Left");
					break;
				}
				case FlyoutTabStripPosition::Right:
				{
					echo("Right");
					break;
				}
			}?>">
				<div class="FlyoutTabs">
				<?php
					foreach ($this->Items as $item)
					{
					?>
					<div id="FlyoutTabContainer_<?php echo($this->ID); ?>_<?php echo($item->ID); ?>_Tab" onclick="<?php echo($this->ID); ?>.ToggleItem('<?php echo($item->ID); ?>');" title="<?php echo($item->Title); ?>" class="FlyoutTab"><img src="<?php echo(\System::ExpandRelativePath($item->ImageURL)); ?>" alt="<?php echo($item->Title); ?>" title="<?php echo($item->Title); ?>" /></div>
					<?php
					}
				?>
				</div>
				<div class="FlyoutTabContents">
				<?php
					foreach ($this->Items as $item)
					{
						?><div id="FlyoutTabContainer_<?php echo($this->ID); ?>_<?php echo($item->ID); ?>_Content" class="FlyoutTabContent"><?php
						if ($item->RenderContent != null && is_callable($item->RenderContent))
						{
							call_user_func($item->RenderContent);
						}
						else if ($item->Content != null)
						{
							echo($item->Content);
						}
						?></div>
				<?php
					}
				?>
				</div>
			</div>
			<script type="text/javascript">
				var <?php echo($this->ID); ?> = new FlyoutTabContainer("<?php echo($this->ID); ?>");
			</script>
		<?php
		}
	}
?>