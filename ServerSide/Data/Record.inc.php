<?php
	namespace Phast\Data;
	/**
	 * Represents a record on a Table.
	 * @author Michael Becker
	 * @see Table
	 * @see RecordColumn
	 */
	class Record
	{
		/**
		 * The column values for this record.
		 * @var RecordColumn[]
		 */
		public $Columns;
		
		/**
		 * Retrieves the RecordColumn with the specified name for this record.
		 * @param string $name
		 * @return RecordColumn|NULL The RecordColumn with the specified name, or NULL if no RecordColumn with the specified name was found.
		 * @see RecordColumn
		 */
		public function GetColumnByName($name)
		{
			foreach ($this->Columns as $column)
			{
				if ($column->Name == $name) return $column;
			}
			return null;
		}
		
		/**
		 * Creates a Record object with the specified column values, but does not insert the record into a table. 
		 * @param RecordColumn[] $columns The column values to associate with this Record.
		 */
		public function __construct($columns = null)
		{
			if ($columns == null) $columns = array();
			$this->Columns = $columns;
		}
	}
?>