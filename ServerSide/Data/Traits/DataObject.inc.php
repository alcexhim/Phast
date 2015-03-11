<?php
	namespace Phast\Data\Traits;
	
	use Phast\Data\DataSystem;
	use Phast\System;
	use PDO;
	
	trait DataObjectTrait
	{
		public static $DataObjectTableName;
		public static $DataObjectColumnPrefix;
		
		public static function BindDataColumn($instance, $columnName, $value)
		{
			return true;
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
			
			$columnNames = array();
			$columnCount = $statement->columnCount();
			for ($c = 0; $c < $columnCount; $c++)
			{
				$meta = $statement->getColumnMeta($c);
				$columnNames[] = $meta["name"];
			}
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = new self();
				for ($j = 0; $j < $columnCount; $j++)
				{
					$columnName = $columnNames[$j];
					$item->BindDataColumn($columnName, $values[$columnName]);
				}
				echo ("Found object " . $i);
				$retval[] = $item;
			}
			return $retval;
		}
	}
?>