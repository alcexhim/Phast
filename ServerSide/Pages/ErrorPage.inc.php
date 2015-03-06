<?php
	namespace Phast\Pages;
	require_once("MessagePage.inc.php");
	
	class ErrorPage extends MessagePage
	{
		protected function BeforeContent()
		{
			parent::BeforeContent();
		?>
			<div class="Icon ErrorIcon">&nbsp;</div>
			<div class="Title">Application error</div>
		<?php
		}
		
		protected function AfterContent()
		{
		?>
		<?php
			parent::AfterContent();
		}
	}
?>