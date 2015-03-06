<?php
	namespace Phast;
	
	class WebNamespaceReference
	{
		public $TagPrefix;
		public $NamespacePath;
		public $NamespaceURL;
		
		public function __construct($tagPrefix, $namespacePath, $namespaceURL)
		{
			$this->TagPrefix = $tagPrefix;
			$this->NamespacePath = $namespacePath;
			$this->NamespaceURL = $namespaceURL;
		}
	}
?>