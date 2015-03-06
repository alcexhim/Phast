<?php
	namespace Phast\WebControls;
	
	class ImageGallery extends \Phast\WebControl
	{
		protected function RenderContent()
		{
			?>
			<div class="ImageGallery" id="ImageGallery_<?php echo($this->ID); ?>">
				<div class="ImageBrowser">
					<a class="NavigationButton Left" href="#" onclick="<?php echo($this->ID); ?>.GoToPreviousImage();">&nbsp;</a>
					<div class="CurrentImage">&nbsp;</div>
					<a class="NavigationButton Right" href="#" onclick="<?php echo($this->ID); ?>.GoToNextImage();">&nbsp;</a>
				</div>
				<div class="Thumbnails">
					<a href="#"><img src="" /></a>
					<a href="#"><img src="" /></a>
					<a href="#"><img src="" /></a>
					<a href="#"><img src="" /></a>
				</div>
			</div>
			<script type="text/javascript">var <?php echo($this->ID); ?> = new ImageGallery("<?php echo($this->ID); ?>");</script>
			<?php
		}
	}
?>