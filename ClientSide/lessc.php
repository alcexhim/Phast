<?php

require_once ("Phast/Compilers/StyleSheet/Internal/LessStyleSheetCompiler.inc.php");
require_once ("Phast/Compilers/StyleSheet/Internal/Formatters/CompressedFormatter.inc.php");

use Phast\Compilers\StyleSheet\Internal\LessStyleSheetCompiler;
use Phast\Compilers\StyleSheet\Internal\Formatters\CompressedFormatter;

header("Content-Type: text/css");

$filename = "StyleSheets/" . $_GET["filename"];

if ((isset($_GET["compile"]) && $_GET["compile"] == "false") || file_exists($filename . ".css"))
{
	readfile($filename . ".css");
}
else
{
	try
	{
		$less = new LessStyleSheetCompiler();
		$less->formatter = new CompressedFormatter();
		
		$v = $less->compileFile($filename . ".less");
		
		echo("/* compiled with lessphp v0.4.0 - GPLv3/MIT - http://leafo.net/lessphp */\n");
		echo("/* for human-readable source of this file, replace .css with .less in the file name */\n");
		echo($v);
	}
	catch (Exception $e)
	{
		echo "/* " . $e->getMessage() . " */\n";
	}
}
?>