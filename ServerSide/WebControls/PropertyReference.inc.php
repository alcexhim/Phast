<?php
	namespace Phast\WebControls;
	
	use Phast\WebControl;
	use Phast\System;
		
	class PropertyReference extends WebControl
	{
		public $DefaultValue;
		
		protected function RenderContent()
		{
			echo(System::GetConfigurationValue($this->ID, $this->DefaultValue));
		}	
	}
?>