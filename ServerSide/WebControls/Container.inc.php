<?php

	namespace Phast\WebControls;
	
	use Phast\WebControl;
	
	/**
	 * Provides a simple container for child controls that does not render any extra markup to the HTML page.
	 * @author Michael Becker
	 *
	 */
	class Container extends WebControl
	{
		public function __construct()
		{
			parent::__construct();
			$this->ParseChildElements = false;
		}
	}
?>