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
		 */
		const HTML = 1;
		/**
		 * The WebPage is rendered as JSON for AJAX requests.
		 */
		const JSON = 2;
		/**
		 * The WebPage is rendered as XML for AJAX requests.
		 */
		const XML = 3;
	}
?>