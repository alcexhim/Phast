<?php
	namespace Phast\Data;
	
	use Phast\Conditionals\ConditionalStatement;
	
	class TableSelectCriteria
	{
		/**
		 * The names of the columns to return, or NULL to return all columns.
		 * @var string[]
		 */
		public $ColumnNames;
		
		/**
		 * The conditions for this select statement. 
		 * @var ConditionalStatement[]
		 */
		public $Conditions;
	}
?>