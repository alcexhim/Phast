<?php
	namespace Phast\Parser;
	
	use UniversalEditor\ObjectModels\Markup\MarkupObjectModel;
	
	use Phast\System;
	
	use Phast\WebControl;
	use Phast\WebPage;
	use Phast\WebNamespaceReference;
	use Phast\WebScript;
	use Phast\WebStyleSheet;
	use Phast\WebVariable;
	use Phast\WebPageMessage;
	
	use Phast\WebControlAttribute;
	use Phast\WebControlClientIDMode;
	
	use Phast\HTMLControl;
	use Phast\HTMLControls\HTMLControlLiteral;
	
	use Phast\EventArgs;
	use Phast\CancelEventArgs;
		
	require("XMLParser.inc.php");
	
	
	class PhastPage
	{
		/**
		 * The Page generated by this template.
		 * @var WebPage
		 */
		public $Page;
		
		/**
		 * Code executed before the page is initialized.
		 * @param CancelEventArgs $e
		 */
		public function OnInitializing(CancelEventArgs $e)
		{
		}
		/**
		 * Code executed after the page is initialized.
		 * @param EventArgs $e
		 */
		public function OnInitialized(EventArgs $e)
		{
		}
		/**
		 * Code executed after the class definition is loaded by the Phast parser.
		 * @param EventArgs $e
		 */
		public function OnClassLoaded(EventArgs $e)
		{
		}
	}
	
	class PhastParser
	{
		public $Controls;
		public $MasterPages;
		public $Pages;
		
		public function GetControlByVirtualTagPath($path)
		{
			foreach ($this->Controls as $ctl)
			{
				if ($ctl->VirtualTagPath == $path) return $ctl;
			}
			return null;
		}
		public function GetMasterPageByFileName($filename)
		{
			foreach ($this->MasterPages as $page)
			{
				if ($page->FileName == $filename) return $page;
			}
			return null;
		}
		public function GetPageByFileName($filename)
		{
			foreach ($this->Pages as $page)
			{
				if ($page->FileName == $filename) return $page;
			}
			return null;
		}
		
		public function __construct()
		{
			$this->Clear();
		}
		
		public function Clear()
		{
			$this->Controls = array();
			$this->MasterPages = array();
			$this->Pages = array();
		}
		
		/**
		 * Loads an XML file describing a portion or portions of the Phast environment.
		 * @param string $filename The name of the XML file to load into the environment.
		 */
		public function LoadFile($filename)
		{
			$markup = MarkupObjectModel::FromFile($filename);
			
			$tagWebsite = $markup->GetElement("Website");
			if ($tagWebsite == null) return;
			
			$tagControls = $tagWebsite->GetElement("Controls");
			if ($tagControls != null)
			{
				foreach ($tagControls->Elements as $element)
				{
					if (get_class($element) != "UniversalEditor\\ObjectModels\\Markup\\MarkupTagElement") continue;
					if ($element->Name == "Control")
					{
						$ctl = WebControl::FromMarkup($element, $this);
						$ctl->PhysicalFileName = $filename;
						$this->Controls[] = $ctl;
					}
				}
			}
			
			$tagMasterPages = $tagWebsite->GetElement("MasterPages");
			if ($tagMasterPages != null)
			{
				foreach ($tagMasterPages->Elements as $element)
				{
					if (get_class($element) != "UniversalEditor\\ObjectModels\\Markup\\MarkupTagElement") continue;
					if ($element->Name == "MasterPage")
					{
						$page = WebPage::FromMarkup($element, $this);
						$page->PhysicalFileName = $filename;
						$this->MasterPages[] = $page;
					}
				}
			}
			
			$tagPages = $tagWebsite->GetElement("Pages");
			if ($tagPages != null)
			{
				foreach ($tagPages->Elements as $element)
				{
					if (get_class($element) != "UniversalEditor\\ObjectModels\\Markup\\MarkupTagElement") continue;
					if ($element->Name == "Page")
					{
						$page = WebPage::FromMarkup($element, $this);
						$page->PhysicalFileName = $filename;
						$this->Pages[] = $page;
					}
				}
			}
		}
	}
?>