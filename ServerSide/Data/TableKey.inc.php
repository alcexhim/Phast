<?php
	namespace Phast\Data;
	
	use Phast\System;
	class TableKey
	{
		public $Columns;
		
		public function __construct($columns)
		{
			if (is_array($columns))
			{
				$this->Columns = $columns;
			}
			else
			{
				$this->Columns = array();
			}
		}
	}
	class TableKeyColumn
	{
		public $Name;
		
		public function __construct($name)
		{
			$this->Name = $name;
		}
	}
?>