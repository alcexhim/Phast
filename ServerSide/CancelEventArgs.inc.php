<?php
	namespace Phast;
	
	/**
	 * Provides data for a cancelable event.
	 * @author Michael Becker
	 */
	class CancelEventArgs extends EventArgs
	{
		/**
		 * True if the event should be canceled; false otherwise.
		 * @var boolean
		 */
		public $Cancel;
	} 
?>