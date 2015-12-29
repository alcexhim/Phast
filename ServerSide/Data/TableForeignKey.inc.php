<?php
	namespace Phast\Data;
	
	use Phast\Enumeration;
	
	abstract class TableForeignKeyReferenceOption extends Enumeration
	{
		const NoAction = 0;
		const Restrict = 1;
		const Cascade = 2;
		const SetNull = 3;
	}
	
	/**
	 * Defines a relationship to a column in another table.
	 * @author Michael Becker
	 */
	class TableForeignKey
	{
		/**
		 * The ID of the foreign key. Can be null. 
		 * @var string
		 */
		public $ID;
		/**
		 * The name of the column in the source table.
		 * @var string
		 */
		public $ColumnName;
		/**
		 * The reference(s) to the foreign column(s) in the foreign table(s).
		 * @var TableForeignKeyColumn
		 */
		public $ForeignColumnReference;
		
		/**
		 * The action to take when a value in this column is deleted.
		 * @var TableForeignKeyReferenceOption
		 */
		public $DeleteAction;
		/**
		 * The action to take when a value in this column is updated.
		 * @var TableForeignKeyReferenceOption
		 */
		public $UpdateAction;
		/**
		 * 
		 * @param unknown $columnName
		 * @param TableForeignKeyColumn[] $foreignColumnReference The column(s) included in this foreign key.
		 * @param string $deleteAction
		 * @param string $updateAction
		 * @param string $id
		 */
		public function __construct($columnName, $foreignColumnReference, $deleteAction = null, $updateAction = null, $id = null)
		{
			$this->ID = $id;
			$this->ColumnName = $columnName;
			$this->ForeignColumnReference = $foreignColumnReference;
			
			if ($deleteAction == null) $deleteAction = TableForeignKeyReferenceOption::Restrict;
			$this->DeleteAction = $deleteAction;
			if ($updateAction == null) $updateAction = TableForeignKeyReferenceOption::Restrict;
			$this->UpdateAction = $updateAction;
		}
	}
	class TableForeignKeyColumn
	{
		/**
		 * The table that contains the foreign column.
		 * @var Table
		 */
		public $Table;
		/**
		 * A reference to the foreign column(s), or the name of the foreign column(s).
		 * @var string|Column|string[]|Column[]
		 */
		public $Column;
		
		/**
		 * Creates a TableForeignKeyColumn with the given parameters.
		 * @param Table $table The table that contains the foreign column.
		 * @param string|Column $column A reference to the foreign column, or the name of the foreign column.
		 */
		public function __construct($table, $column)
		{
			$this->Table = $table;
			if (is_string($column))
			{
				$column = $table->GetColumnByName($column);
			}
			$this->Column = $column;
		}
	}
?>