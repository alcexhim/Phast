<?php
	namespace Phast\Data;
	
	use Phast\System;
	use Phast\Enumeration;
	
	use PDO;
		
	abstract class ColumnValue extends Enumeration
	{
		const Undefined = 0;
		const Now = 1;
		const Today = 2;
		const CurrentTimestamp = 3;
	}

	require_once("Column.inc.php");
	require_once("DataObject.inc.php");
	require_once("DatabaseOperationResult.inc.php");
	require_once("Record.inc.php");
	require_once("RecordColumn.inc.php");
	require_once("SelectResult.inc.php");
	require_once("Table.inc.php");
	require_once("TableForeignKey.inc.php");
	require_once("TableKey.inc.php");
	require_once("TableSelectCriteria.inc.php");
	
	class DataSystem
	{
		/**
		 * @var DataErrorCollection
		 */
		public static $Errors;
		
		private static $_PDO = null;
		/**
		 * Returns the currently-loaded PDO engine.
		 * @return \PDO The currently-loaded PDO engine.
		 */
		public static function GetPDO()
		{
			if (DataSystem::$_PDO == null)
			{
				$engine = 'mysql';
				$host = System::GetConfigurationValue("Database.ServerName");
				$database = System::GetConfigurationValue("Database.DatabaseName");
				$user = System::GetConfigurationValue("Database.UserName");
				$pass = System::GetConfigurationValue("Database.Password");
				
				$dns = $engine . ':dbname=' . $database . ";host=" . $host;
				
				DataSystem::$_PDO = new PDO($dns, $user, $pass);
			}
			return DataSystem::$_PDO;
		}
	}
	DataSystem::$Errors = new DataErrorCollection();
	
	class DataError
	{
		public $Code;
		public $Message;
		public $Query;
		
		public function __construct($code, $message, $query = null)
		{
			$this->Code = $code;
			$this->Message = $message;
			$this->Query = $query;
		}
	}
	class DataErrorCollection
	{
		public function __construct()
		{
			$this->Clear();
		}
		
		/**
		 * 
		 * @var DataError[]
		 */
		public $Items;
		public function Add($item)
		{
			$this->Items[] = $item;
		}
		public function Clear()
		{
			$this->Items = array();
		}
	}
?>
