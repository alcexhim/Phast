<?php
	namespace Phast\Data;
	use Phast\System;
	use Phast\Enumeration;
		
	abstract class ColumnValue extends Enumeration
	{
		const Undefined = 0;
		const Now = 1;
		const Today = 2;
		const CurrentTimestamp = 3;
	}
	
	require_once("DataObject.inc.php");
	
	class DataSystem
	{
		public static $Errors;
		
		/**
		 * Returns the currently-loaded PDO engine. Efforts are being made to move from MySQLi to PDO but have not been finished completely just yet.
		 * @return \PDO The currently-loaded PDO engine.
		 */
		public static function GetPDO()
		{
			global $pdo;
			return $pdo;
		}
		
		public static function Initialize()
		{
			global $MySQL;
			if (!(isset(System::$Configuration["Database.ServerName"]) && isset(System::$Configuration["Database.UserName"]) && isset(System::$Configuration["Database.Password"]) && isset(System::$Configuration["Database.DatabaseName"])))
			{
				// Phast\Data error!
				return false;
			}
			
			$MySQL = new \mysqli(System::$Configuration["Database.ServerName"], System::$Configuration["Database.UserName"], System::$Configuration["Database.Password"], System::$Configuration["Database.DatabaseName"]);
			$MySQL->set_charset("utf8");
			
			if ($MySQL->connect_error)
			{
				Phast\Data::$Errors->Clear();
				Phast\Data::$Errors->Add(new Phast\DataError($MySQL->connect_errno, $MySQL->connect_error));
				return false;
			}
			
			require_once("Column.inc.php");
			require_once("DatabaseOperationResult.inc.php");
			require_once("Record.inc.php");
			require_once("RecordColumn.inc.php");
			require_once("Table.inc.php");
			require_once("TableForeignKey.inc.php");
			require_once("TableKey.inc.php");
			return true;
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
	
	DataSystem::Initialize();
?>
