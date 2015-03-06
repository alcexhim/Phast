<?php
	namespace Phast\WebControls;
	
	use Phast\WebControl;
	
	/**
	 * Provides a page section that can hold any WebControl. Content is rendered to the portion of the master page with the same ID as the PlaceholderID.
	 * 
	 * @author Michael Becker
	 * @see SectionPlaceholder
	 */
	class Section extends WebControl
	{
		public $PlaceholderID;
	}
	class SectionPlaceholder extends WebControl
	{
	}
?>
