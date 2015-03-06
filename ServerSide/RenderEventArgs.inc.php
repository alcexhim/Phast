<?php
	namespace Phast;
	
	class RenderingEventArgs extends CancelEventArgs
	{
		/**
		 * Describes whether this is a partial or complete render.
		 * @var RenderMode
		 */
		public $RenderMode;
		
		/**
		 * Creates a new instance of RenderingEventArgs with the specified renderMode.
		 * @param RenderMode $renderMode Describes whether this is a partial or complete render. Default RenderMode::Any.
		 */
		public function __construct($renderMode = null)
		{
			if ($renderMode == null) $renderMode = RenderMode::Any;
			$this->RenderMode = $renderMode;
		}
	}
	class RenderedEventArgs extends EventArgs
	{
		/**
		 * Describes whether this is a partial or complete render.
		 * @var RenderMode
		 */
		public $RenderMode;
		
		/**
		 * Creates a new instance of RenderedEventArgs with the specified renderMode.
		 * @param RenderMode $renderMode Describes whether this is a partial or complete render. Default RenderMode::Any.
		 */
		public function __construct($renderMode = null)
		{
			if ($renderMode == null) $renderMode = RenderMode::Any;
			$this->RenderMode = $renderMode;
		}
	}
?>