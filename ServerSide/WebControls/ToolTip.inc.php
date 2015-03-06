<?php
	namespace Phast\WebControls;
	use System;
	use Phast\WebControl;
	
	class ToolTip extends WebControl
	{
		public $Title;
		public $Content;
		public $Width;
		
		public function __construct($id, $title, $content)
		{
			parent::__construct($id);
			$this->Title = $title;
			$this->Content = $content;
		}
		
		protected function BeforeContent()
		{
?>
<div class="ToolTip" id="ToolTip_<?php echo($this->ID); ?>"<?php if ($this->Width != null) { echo (" style=\"width: " . (is_numeric($this->Width) ? ($this->Width . "px") : $this->Width) . ";\""); } ?>>
	<div class="Title" id="ToolTip_<?php echo($this->ID); ?>_Title"><?php echo($this->Title); ?></div>
	<div class="Content" id="ToolTip_<?php echo($this->ID); ?>_Content"><?php echo($this->Content); ?></div>
</div>
<div class="ToolTipActivator" id="ToolTip_<?php echo($this->ID); ?>_Activator">
<?php
		}
		protected function AfterContent()
		{
?>
</div>
<script type="text/javascript">var <?php echo($this->ID); ?> = new ToolTip("<?php echo($this->ID); ?>");</script>
<?php
		}
	}
?>