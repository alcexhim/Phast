<?php
	include("jsmin.php");
	
	$last_modified_time = filemtime(__FILE__);
	$etag = md5_file(__FILE__);
	// always send headers
	header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
	header("Etag: $etag");
	
	// exit if not modified
	if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
		@trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag)
	{
		header("HTTP/1.1 304 Not Modified");
		exit;
	}
	
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
		if (class_exists("JSMin"))
		{
			$output = JSMin::minify($input);
		}
		else
		{
			$output = "/* could not find class 'JSMin'; minify is unavailable */\r\n";
			$output .= $input;
		}
		echo($output);
	}
?>