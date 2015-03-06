<?php
	namespace Phast;
	
	class WebApplicationTask
	{
		public $ID;
		
		public $Title;
		public $TaskType;
		public $Description;
		
		public $TargetURL;
		public $TargetScript;
		public $TargetFrame;
		
		public $Tasks;
		
		public $CheckAvailabilityFunction;
		
		public function IsAvailable()
		{
			return call_user_func($this->CheckAvailabilityFunction);
		}
		
		public function __construct($id, $title = null, $taskType = null, $description = null, $targetURL = null, $targetScript = null, $targetFrame = null, $tasks = null)
		{
			if ($title == null) $title = $id;
			if ($tasks == null) $tasks = array();
			
			$this->ID = $id;
			$this->Title = $title;
			$this->TaskType = $taskType;
			$this->Description = $description;
			$this->TargetURL = $targetURL;
			$this->TargetScript = $targetScript;
			$this->TargetFrame = $targetFrame;
			$this->Tasks = $tasks;
			
			$this->CheckAvailabilityFunction = function()
			{
				return true;
			};
		}
	}
?>