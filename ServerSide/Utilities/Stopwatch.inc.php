<?php
	namespace Phast\Utilities;

	class Stopwatch
	{
		/**
		 * @var $startTime float The start time of the StopWatch
		 */
		private $startTime;
		private $endTime;
		
		/**
		 * Start the timer
		 * @return void
		 */
		public function start()
		{
			$this->startTime = microtime(true);
			$this->endTime = null;
		}
		public function stop()
		{
			$this->endTime = microtime(true);
		}
		public function reset()
		{
			$this->startTime = null;
			$this->endTime = null;
		}
		
		/**
		 * Get the elapsed time in seconds
		 * 
		 * @param $timerName string The name of the timer to start
		 * @return float|bool The elapsed time since start() was called, or false if the timer is stopped;
		 */
		public function getElapsedTime()
		{
			if ($this->startTime === null) return false;
			if ($this->endTime === null) return false;
			
			return ($this->endTime - $this->startTime);
		}
		
		public function __construct()
		{
			$this->startTime = null;
			$this->endTime = null;
		}
	}
	
?>