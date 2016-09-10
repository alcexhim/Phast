<?php
	namespace Phast\Data;
	
	use Phast\System;
	use PDO;
	
	use Phast\Conditionals\ConditionalComparison;
	use Phast\StringMethods;
	
	/**
	 * Represents a table on the database.
	 * @author Michael Becker
	 * @see Column
	 * @see Record
	 * @see TableForeignKey
	 */
	class Table
	{
		/**
		 * The name of the table.
		 * @var string
		 */
		public $Name;
		/**
		 * The prefix used before the name of each column in the table.
		 * @var string
		 */
		public $ColumnPrefix;
		/**
		 * The columns on the table.
		 * @var Column[]
		 */
		public $Columns;
		/**
		 * The records in the table.
		 * @var Record[]
		 */
		public $Records;
		
		/**
		 * The key that is the primary key of the table.
		 * @var TableKey
		 */
		public $PrimaryKey;
		/**
		 * The key(s) that are the unique keys of the table.
		 * @var TableForeignKey[]
		 */
		public $UniqueKeys;
		/**
		 * Any additional key(s) on the table that are not primary or unique keys.
		 * @var TableForeignKey[]
		 */
		public $ForeignKeys;
		
		/**
		 * Creates a Table object with the given parameters (but does not create the table on the database).
		 * @param string $name The name of the table.
		 * @param string $columnPrefix The prefix used before the name of each column in the table.
		 * @param Column[] $columns The column(s) of the table.
		 * @param Record[] $records The record(s) to insert into the table.
		 */
		public function __construct($name, $columnPrefix, $columns, $records = null)
		{
			$this->Name = $name;
			$this->ColumnPrefix = $columnPrefix;
			$this->Columns = $columns;
			
			if ($records == null) $records = array();
			$this->Records = $records;
			
			$this->PrimaryKey = null;
			$this->UniqueKeys = array();
			$this->ForeignKeys = array();
		}
		
		/**
		 * Gets the table with the specified name from the database.
		 * @param string $name The name of the table to search for.
		 * @param string $columnPrefix The column prefix for the columns in the table. Columns that begin with this prefix will be populated with the prefix stripped.
		 * @return Table The table with the specified name.
		 */
		public static function Get($name, $columnPrefix = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SHOW COLUMNS FROM " . System::GetConfigurationValue("Database.TablePrefix") . $name;
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			
			$count = $statement->rowCount();
			$columns = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				
				$columnName = $values["Field"];
				if (substr($columnName, 0, strlen($columnPrefix)) == $columnPrefix)
				{
					$columnName = substr($columnName, strlen($columnPrefix));
				}
				$dataTypeNameAndSize = $values["Type"];
				$dataTypeName = substr($dataTypeNameAndSize, 0, strpos($dataTypeNameAndSize, "("));
				$dataTypeSize = substr($dataTypeNameAndSize, strpos($dataTypeNameAndSize, "("), strlen($dataTypeNameAndSize) - strpos($dataTypeNameAndSize, "(") - 2);
				$defaultValue = $values["Default"];
				$allowNull = ($values["Null"] == "YES");
				$primaryKey = ($values["Key"] == "PRI");
				$autoIncrement = ($values["Extra"] == "auto_increment");
				
				$columns[] = new Column($columnName, $dataTypeName, $dataTypeSize, $defaultValue, $allowNull, $primaryKey, $autoIncrement);
			}
			
			return new Table($name, $columnPrefix, $columns);
		}
		
		/**
		 * Creates the table on the database.
		 * @return boolean True if the table was created successfully; false if an error occurred.
		 */
		public function Create()
		{
			$pdo = DataSystem::GetPDO();
			$query = "CREATE TABLE " . System::$Configuration["Database.TablePrefix"] . $this->Name;
			
			$query .= "(";
			$count = count($this->Columns);
			for ($i = 0; $i < $count; $i++)
			{
				$column = $this->Columns[$i];
				$query .= ($this->ColumnPrefix . $column->Name) . " " . $column->DataType;
				if ($column->Size != null)
				{
					$query .= "(" . $column->Size . ")";
				}
				if ($column->AllowNull == false)
				{
					$query .= " NOT NULL";
				}
				if ($column->DefaultValue != null)
				{
					$query .= " DEFAULT ";
					if ($column->DefaultValue === ColumnValue::Undefined)
					{
						$query .= "NULL";
					}
					else if ($column->DefaultValue === ColumnValue::CurrentTimestamp)
					{
						$query .= "CURRENT_TIMESTAMP";
					}
					else if (is_string($column->DefaultValue))
					{
						$query .= "\"" . $column->DefaultValue . "\"";
					}
					else
					{
						$query .= $column->DefaultValue;
					}
				}
				if ($column->PrimaryKey)
				{
					$query .= " PRIMARY KEY";
				}
				if ($column->AutoIncrement)
				{
					$query .= " AUTO_INCREMENT";
				}
				if ($i < $count - 1) $query .= ", ";
			}
			
			$count = count($this->ForeignKeys);
			if ($count > 0)
			{
				$query .= ", ";
				for ($i = 0; $i < $count; $i++)
				{
					$fk = $this->ForeignKeys[$i];
					$query .= "FOREIGN KEY ";
					if ($fk->ID != null)
					{
						$query .= $fk->ID . " ";
					}
					$query .= "(";
					if (is_array($fk->ColumnName))
					{
						$columnNameCount = count($fk->ColumnName);
						for ($j = 0; $j < $columnNameCount; $j++)
						{
							$query .= $this->ColumnPrefix . $fk->ColumnName[$j];
							if ($j < $columnNameCount - 1) $query .= ", ";
						}
					}
					else
					{
						$query .= $this->ColumnPrefix . $fk->ColumnName;
					}
					$query .= ")";
					$query .= " REFERENCES " . System::GetConfigurationValue("Database.TablePrefix") . $fk->ForeignColumnReference->Table->Name . " (";
					if (is_array($fk->ForeignColumnReference->Column))
					{
						$foreignColumnReferenceCount = count($fk->ForeignColumnReference->Column);
						for ($j = 0; $j < $foreignColumnReferenceCount; $j++)
						{
							$query .= $fk->ForeignColumnReference->Table->ColumnPrefix;
							if (is_string($fk->ForeignColumnReference->Column[$j]))
							{
								$query .= $fk->ForeignColumnReference->Column[$j];
							}
							else
							{
								$query .= $fk->ForeignColumnReference->Column[$j]->Name;
							}
							if ($j < $foreignColumnReferenceCount - 1) $query .= ", ";
						}
					}
					else
					{
						$query .= $fk->ForeignColumnReference->Table->ColumnPrefix . $fk->ForeignColumnReference->Column->Name;
					}
					$query .= ")";
					
					$query .= " ON DELETE ";
					switch ($fk->DeleteAction)
					{
						case TableForeignKeyReferenceOption::Restrict:
						{
							$query .= "RESTRICT";
							break;
						}
						case TableForeignKeyReferenceOption::Cascade:
						{
							$query .= "CASCADE";
							break;
						}
						case TableForeignKeyReferenceOption::SetNull:
						{
							$query .= "SET NULL";
							break;
						}
						case TableForeignKeyReferenceOption::NoAction:
						{
							$query .= "NO ACTION";
							break;
						}
					}
					
					$query .= " ON UPDATE ";
					switch ($fk->DeleteAction)
					{
						case TableForeignKeyReferenceOption::Restrict:
						{
							$query .= "RESTRICT";
							break;
						}
						case TableForeignKeyReferenceOption::Cascade:
						{
							$query .= "CASCADE";
							break;
						}
						case TableForeignKeyReferenceOption::SetNull:
						{
							$query .= "SET NULL";
							break;
						}
						case TableForeignKeyReferenceOption::NoAction:
						{
							$query .= "NO ACTION";
							break;
						}
					}
					
					if ($i < $count - 1) $query .= ", ";
				}
			}
			
			$query .= ")";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				trigger_error("DataSystem error: (" . $ei[1] . ") " . $ei[2]);
				trigger_error("DataSystem query: " . $query);
				DataSystem::$Errors->Clear();
				DataSystem::$Errors->Add(new DataError($ei[1], $ei[2], $query));
				return false;
			}
			
			if ($this->PrimaryKey != null)
			{	
				$key = $this->PrimaryKey;
				$query = "ALTER TABLE `" . System::$Configuration["Database.TablePrefix"] . $this->Name . "` ADD PRIMARY KEY (";
				$count = count($key->Columns);
				for ($i = 0; $i < $count; $i++)
				{
					$col = $key->Columns[$i];
					$query .= "`" . $this->ColumnPrefix . $col->Name . "`";
					if ($i < $count - 1)
					{
						$query .= ", ";
					}
				}
				$query .= ");";

				$statement = $pdo->prepare($query);
				$result = $statement->execute();
				if ($result === false)
				{
					$ei = $statement->errorInfo();
					trigger_error("DataSystem error: (" . $ei[1] . ") " . $ei[2]);
					trigger_error("DataSystem query: " . $query);
					DataSystem::$Errors->Clear();
					DataSystem::$Errors->Add(new DataError($ei[1], $ei[2], $query));
					return false;
				}
			}
			foreach ($this->UniqueKeys as $key)
			{
				$query = "ALTER TABLE `" . System::$Configuration["Database.TablePrefix"] . $this->Name . "` ADD UNIQUE (";
				$count = count($key->Columns);
				for ($i = 0; $i < $count; $i++)
				{
					$col = $key->Columns[$i];
					$query .= "`" . $this->ColumnPrefix . $col->Name . "`";
					if ($i < $count - 1)
					{
						$query .= ", ";
					}
				}
				$query .= ")";
				
				$statement = $pdo->prepare($query);
				$result = $statement->execute();
				if ($result === false)
				{
					$ei = $statement->errorInfo();
					trigger_error("DataSystem error: (" . $ei[1] . ") " . $ei[2]);
					trigger_error("DataSystem query: " . $query);
					DataSystem::$Errors->Clear();
					DataSystem::$Errors->Add(new DataError($ei[1], $ei[2], $query));
					return false;
				}
			}
			
			if ($this->Records != null)
			{
				$result = $this->Insert($this->Records);
				if ($result == null)
				{
					return false;
				}
			}
			
			return true;
		}
		
		public function Select(TableSelectCriteria $criteria)
		{
			$columnNames = "*";
			if (is_array($criteria->ColumnNames))
			{
				$count = count($criteria->ColumnNames);
				if ($count > 0)
				{
					$columnNames = "";
					for ($i = 0; $i < $count; $i++)
					{
						$columnNames .= $criteria->ColumnNames[$i];
						if ($i < $count - 1) $columnNames .= ", ";
					}
				}
			}
			
			$query = "SELECT " . $columnNames . " FROM " . System::GetConfigurationValue("Database.TablePrefix") . $this->Name;
			if (is_array($criteria->Conditions))
			{
				$count = count($criteria->Conditions);
				if ($count > 0)
				{
					$query .= " WHERE ";
					for ($i = 0; $i < $count; $i++)
					{
						$conditionalStatement = $criteria->Conditions[$i];
						
						$query .= "(";
						$query .= $this->ColumnPrefix . $conditionalStatement->Name . " ";
						switch ($conditionalStatement->Comparison)
						{
							case ConditionalComparison::Equals:
							{
								$query .= "=";
								break;
							}
						}
						$query .= " ";
						$query .= ":conditionalStatement" . $i;
						$query .= ")";
						
						if ($i < $count - 1)
						{
							$query .= " AND ";
						}
					}
				}
			}
			
			$conditionalStatementCriteria = array();
			$count = count($criteria->Conditions);
			for ($i = 0; $i < $count; $i++)
			{
				$conditionalStatement = $criteria->Conditions[$i];
				$conditionalStatementCriteria[":conditionalStatement" . $i] = $conditionalStatement->Value;
			}
			
			$pdo = DataSystem::GetPDO();
			$statement = $pdo->prepare($query);
			$result = $statement->execute($conditionalStatementCriteria);
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				trigger_error("DataSystem error: (" . $ei[1] . ") " . $ei[2]);
				trigger_error("DataSystem query: " . $query);
				DataSystem::$Errors->Clear();
				DataSystem::$Errors->Add(new DataError($ei[1], $ei[2], $query));
				return false;
			}
			
			$count = $statement->rowCount();
			$selectResult = new SelectResult();
			for ($i = 0; $i < $count; $i++)
			{
				$record = new Record();
				
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				foreach ($values as $key => $value)
				{
					if (StringMethods::StartsWith($key, $this->ColumnPrefix)) $key = substr($key, strlen($this->ColumnPrefix));
					$record->Columns[] = new RecordColumn($key, $value);
				}
				
				$selectResult->Records[] = $record;
			}
			return $selectResult;
		}
		
		/**
		 * 
		 * @param Record[] $records The record(s) to insert into the table.
		 * @param boolean $stopOnError True if processing of the records should stop if an error occurs; false to continue.
		 * @return NULL|InsertResult
		 */
		public function Insert($records, $stopOnError = true)
		{
			DataSystem::$Errors->Clear();
			
			$pdo = DataSystem::GetPDO();
			$rowCount = 0;
			$lastInsertId = 0;
			
			if (!is_array($records))
			{
				if (get_class($records) == "Phast\\Data\\Record")
				{
					// single record
					$records = array($records);
				}
				else
				{
					trigger_error("Table::Insert() called, but $records is not an array or single Record!");
					return false;
				}
			}
			
			foreach ($records as $record)
			{
				$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . $this->Name;
				$query .= " (";
				$count = count($record->Columns);
				for ($i = 0; $i < $count; $i++)
				{
					$column = $record->Columns[$i];
					$query .= ($this->ColumnPrefix . $column->Name);
					if ($i < $count - 1) $query .= ", ";
				}
				$query .= " ) VALUES ( ";
				for ($i = 0; $i < $count; $i++)
				{
					$query .= ":" . $record->Columns[$i]->Name;
					if ($i < $count - 1) $query .= ", ";
				}
				$query .= ")";
				
				$statement = $pdo->prepare($query);
				
				$values = array();
				for ($i = 0; $i < $count; $i++)
				{
					$column = $record->Columns[$i];
					$name = ":" . $column->Name;
					if ($column->Value === null || $column->Value === ColumnValue::Undefined)
					{
						$values[$name] = null;
					}
					else if ($column->Value === ColumnValue::Now)
					{
						$values[$name] = "NOW()";
					}
					else if ($column->Value === ColumnValue::CurrentTimestamp)
					{
						$values[$name] = "CURRENT_TIMESTAMP";
					}
					else if ($column->Value === ColumnValue::Today)
					{
						$values[$name] = "TODAY()";
					}
					else if (gettype($column->Value) == "string")
					{
						$values[$name] = $column->Value;
					}
					else if (gettype($column->Value) == "object")
					{
						if (get_class($column->Value) == "DateTime")
						{
							$values[$name] = date_format($column->Value, "Y-m-d H:i:s");
						}
						else
						{
							$values[$name] = $column->Value;
						}
					}
					else
					{
						$values[$name] = $column->Value;
					}
				}
				
				$result = $statement->execute($values);
				
				if ($result === false)
				{
					$ei = $statement->errorInfo();
					trigger_error("DataSystem error: (" . $ei[1] . ") " . $ei[2]);
					trigger_error("DataSystem query: " . $query);
					DataSystem::$Errors->Clear();
					DataSystem::$Errors->Add(new DataError($ei[1], $ei[2], $query));
					if ($stopOnError) return null;
				}
				else
				{
					$rowCount += $statement->rowCount();
					$lastInsertId = $pdo->lastInsertId();
				}
			}
			return new InsertResult($rowCount, $lastInsertId);
		}
		
		/**
		 * Deletes this table from the database.
		 * @return boolean True if the table was deleted successfully; false otherwise.
		 */
		public function Delete()
		{
			$pdo = DataSystem::GetPDO();
			$query = "DROP TABLE " . System::$Configuration["Database.TablePrefix"] . $this->Name;
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				trigger_error("DataSystem error: (" . $ei[1] . ") " . $ei[2]);
				trigger_error("DataSystem query: " . $query);
				DataSystem::$Errors->Clear();
				DataSystem::$Errors->Add(new DataError($ei[1], $ei[2], $query));
				return false;
			}
			return true;
		}
		
		/**
		 * Determines if this table exists on the database.
		 * @return boolean True if this table exists; false otherwise.
		 */
		public function Exists()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SHOW TABLES LIKE '" . System::$Configuration["Database.TablePrefix"] . $this->Name . "'";
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			if ($result !== false)
			{
				return ($statement->rowCount() > 0);
			}
			return false;
		}
		
		/**
		 * Retrieves the Column with the given name on this Table.
		 * @param string $name The name of the column to search for.
		 * @return Column|NULL The column with the given name, or NULL if no columns with the given name were found.
		 */
		public function GetColumnByName($name)
		{
			foreach ($this->Columns as $column)
			{
				if ($column->Name == $name) return $column;
			}
			return null;
		}
	}
?>