<?php
	namespace Phast;
	
	class EventArgs
	{
		private static $_empty;
		
		public static function GetEmptyInstance()
		{
			if (EventArgs::$_empty == null)
			{
				EventArgs::$_empty = new EventArgs();
			}
			return EventArgs::$_empty;
		}
	}
?>