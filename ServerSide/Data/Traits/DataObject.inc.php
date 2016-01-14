<?php
	namespace Phast\Data\Traits;
	
	use Phast\Data\DataSystem;
	use Phast\System;
	use PDO;
	
	trait DataObjectTrait
	{
		public static $DataObjectTableName;
		public static $DataObjectColumnPrefix;

		/**
		 * Called when a property on the DataObject is being bound to a data column.
		 * @param string $columnName The name of the column to bind.
		 * @param mixed $value The value to assign to the column.
		 * @return boolean True if the data binding was handled by this function; false otherwise.
		 */
		protected function BindDataColumn($columnName, $value)
		{
			return false;
		}
		
		public static function GetByAssoc($values)
		{
			$columnNames = array_keys($values);
			$columnPrefix = self::$DataObjectColumnPrefix;
			$columnCount = count($columnNames);
			
			$item = new self();
			for ($j = 0; $j < $columnCount; $j++)
			{
				$columnName = $columnNames[$j];
				$realColumnName = $columnName;
				if (stripos($realColumnName, $columnPrefix) === 0)
				{
					$realColumnName = substr($realColumnName, strlen($columnPrefix));
				}
				if (!$item->BindDataColumn($realColumnName, $values[$columnName]))
				{
					$item->{$realColumnName} = $values[$columnName];
				}
			}
			return $item;
		}
		
		/**
		 * Returns all the instances of this DataObject.
		 */
		public static function Get()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . (System::GetConfigurationValue("Database.TablePrefix") . self::$DataObjectTableName);
			$statement = $pdo->prepare($query);
			$statement->execute();
			$count = $statement->rowCount();
			$retval = array();
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = self::GetByAssoc($values);
				$retval[] = $item;
			}
			return $retval;
		}
	}
?>