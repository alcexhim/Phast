<?php
	namespace Phast;
	
	abstract class WebControlClientIDMode extends Enumeration
	{
		/**
		 * ClientIDs are not assigned or the implementation is unspecified.
		 * @var WebControlClientIDMode
		 */
		const None = 0;
		/**
		 * ClientIDs are automatically assigned by PHAST.
		 * @var WebControlClientIDMode
		 */
		const Automatic = 1;
		/**
		 * ClientIDs are manually specified by the Web site author.
		 * @var WebControlClientIDMode
		 */
		const Manual = 2;
	}
?>
