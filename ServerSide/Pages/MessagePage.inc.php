<?php
	namespace Phast\Pages;
	
	class MessagePage extends \Phast\WebPage
	{
		public $Message;
		
		protected function BeforeContent()
		{
			?><div class="Message"><?php
		}
		protected function RenderContent()
		{
			?><div class="Content"><?php echo($this->Message); ?></div><?php
		}
		protected function AfterContent()
		{
			?></div><?php
		}
	}
?>