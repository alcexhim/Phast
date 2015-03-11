<?php
	namespace Phast\Data\Traits;
	
	use Phast\Data\DataSystem;
	use Phast\System;
	use PDO;
	
	trait DataObjectTrait
	{
		public static $DataObjectTableName;
		public static $DataObjectColumnPrefix;
		
		protected function BindDataColumn($columnName, $value)
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

			$columnPrefix = self::$DataObjectColumnPrefix;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = new self();
				for ($j = 0; $j < $columnCount; $j++)
				{
					$columnName = $columnNames[$j];
					$realColumnName = $columnName;
					if (stripos($realColumnName, $columnPrefix) === 0)
					{
						$realColumnName = substr($realColumnName, strlen($columnPrefix)); 
					}
					if ($item->BindDataColumn($realColumnName, $values[$columnName]))
					{
						$item->{$realColumnName} = $values[$columnName];
					}
				}
				$retval[] = $item;
			}
			return $retval;
		}
	}
?>