<?php
	require("jsmin.php");
	header ("Content-Type: text/javascript");
	
	$bundles = array();
	$files = glob("*.js");
	foreach ($files as $file)
	{
		$bundles[] = $file;
	}
	$files = glob("Controls/*.js");
	foreach ($files as $file)
	{
		$bundles[] = $file;
	}
	
	$input = "";
	foreach ($bundles as $bundle)
	{
		$input .= "/* BEGIN '" . $bundle . "' */\r\n";
		$input .= file_get_contents($bundle) . "\r\n";
		$input .= "/* END '" . $bundle . "' */\r\n\r\n";
	}
	
	if (isset($_GET["minify"]) && $_GET["minify"] == "false")
	{
		echo($input);
	}
	else
	{
		$output = JSMin::minify($input);
		echo($output);
	}
?>