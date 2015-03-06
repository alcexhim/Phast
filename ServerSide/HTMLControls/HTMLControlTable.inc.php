<?php
	namespace Phast\HTMLControls;
	
	use Phast\HTMLControl;
	use Phast\WebControl;
	
	class HTMLControlTable extends HTMLControl
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->TagName = "table";
		}
		
		public function BeginHeader()
		{
			echo("<thead>");
		}
		public function EndHeader()
		{
			echo("</thead>");
		}
		
		public function BeginBody()
		{
			echo("<tbody>");
		}
		public function EndBody()
		{
			echo("</tbody>");
		}
		
		public function BeginRow($namedParameters = null)
		{
			WebControl::BeginTag("tr", $namedParameters);
		}
		public function EndRow()
		{
			WebControl::EndTag("tr");
		}
		
		public function BeginCell($namedParameters = null)
		{
			WebControl::BeginTag("td", $namedParameters);
		}
		public function EndCell()
		{
			WebControl::EndTag("td");
		}
		
		public function BeginHeaderCell($namedParameters = null)
		{
			WebControl::BeginTag("th", $namedParameters);
		}
		public function EndHeaderCell()
		{
			WebControl::EndTag("th");
		}
	}
?>