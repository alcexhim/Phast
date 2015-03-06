<?php
	namespace Phast;
	
	/**
	 * Provides an enumeration of predefined values for type of input.
	 * @author Michael Becker
	 */
	abstract class RenderMode extends Enumeration
	{
		/**
		 * The page is being rendered, either partially or completely.
		 * @var int 0
		 */
		const Any = 0;
		/**
		 * The page is being partially rendered (e.g. as part of an AJAX request)
		 * @var int 1
		 */
		const Partial = 1;
		/**
		 * The page is being completely rendered.
		 * @var int 2
		 */
		const Complete = 2;
	}
?>