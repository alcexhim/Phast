<?php
	namespace Phast;
	
	/**
	 * Various formats for displaying WebPages
	 * @author Michael Becker
	 */
	abstract class WebPageFormat extends Enumeration
	{
		/**
		 * The WebPage is rendered as HTML for interactive requests.
		 * @var int 1
		 */
		const HTML = 1;
		/**
		 * The WebPage is rendered as JSON for AJAX requests.
		 * @var int 2
		 */
		const JSON = 2;
		/**
		 * The WebPage is rendered as XML for AJAX requests.
		 * @var int 3
		 */
		const XML = 3;
		/**
		 * The WebPage is rendering JavaScript from the associated *.phpx.js file.
		 * @var int 4
		 */
		const JavaScript = 4;
	}
?>