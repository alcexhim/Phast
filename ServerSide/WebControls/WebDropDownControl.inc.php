<?php
	namespace Phast\WebControls;
	use System;
	use Phast\WebControl;
	
	class WebDropDownControl extends WebControl
	{
		public $Text;
		public $Width;
		public $Items;
		public $SelectedItem;
		public $RequireSelection;
		
		protected function RenderContent()
		{
?>
<div class="DropDown<?php if ($this->RequireSelection) echo(" SelectionRequired"); ?>" id="DropDown_<?php echo($this->ID); ?>">
	<?php
	if ($this->RequireSelection)
	{
	?>
	<span class="Text" id="DropDown_<?php echo($this->ID); ?>_Text"<?php if ($this->Width != null) echo(" style=\"width: " . $this->Width . "px;\""); ?>><?php if ($this->SelectedItem != null) { echo ($this->SelectedItem->Title); } else { echo($this->Text); } ?></span>
	<?php
	}
	else
	{
	?>
	<input type="text" class="Text" id="DropDown_<?php echo($this->ID); ?>_Input"<?php if ($this->Width != null) echo(" style=\"width: " . $this->Width . "px;\""); if ($this->SelectedItem != null) { echo(" value=\"" . $this->SelectedItem->Title . "\""); } ?> />
	<?php
	}
	?>
	<a class="Button" id="DropDown_<?php echo($this->ID); ?>_Button">▼</a>
	<div class="Menu Popup" id="DropDown_<?php echo($this->ID); ?>_ItemList">
		<div class="MenuSearch">
			<input type="text" placeholder="Type to search" id="DropDown_<?php echo($this->ID); ?>_ItemList_Search" />
		</div>
		<div class="MenuItems" id="DropDown_<?php echo($this->ID); ?>_ItemList_Items">
		<?php
		foreach ($this->Items as $item)
		{
			?><a href="#"><?php echo($item->Title); ?></a><?php
		}
		?>
		</div>
	</div>
</div>
<script type="text/javascript">var <?php echo($this->ID); ?> = new DropDown("<?php echo($this->ID); ?>");</script>
<?php
		}
	}
?>