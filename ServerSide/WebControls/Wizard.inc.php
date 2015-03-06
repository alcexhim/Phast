<?php
	namespace Phast\WebControls;
	
	use Phast\WebControl;
	use Phast\HTMLControl;
	use Phast\WebControlAttribute;
		
	class WizardPage
	{
		/**
		 * The unique identifier of this WizardPage.
		 * @var string
		 */
		public $ID;
		
		/**
		 * The controls contained in this WizardPage.
		 * @var WebControl[]
		 */
		public $Controls;
		
		/**
		 * A short description of this WizardPage.
		 * @var string
		 */
		public $Description;
		/**
		 * The title of this WizardPage.
		 * @var string
		 */
		public $Title;
	}
	class Wizard extends WebControl
	{
		/**
		 * The pages contained in this Wizard.
		 * @var WizardPage[]
		 */
		public $Pages;
		
		/**
		 * The ID of the currently-selected page.
		 * @var string
		 */
		public $SelectedPageID;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->TagName = "div";
			$this->ClassList[] = "Wizard";
			$this->ParseChildElements = true;
		}
		
		protected function OnInitialize()
		{
			$this->Controls = array();
			
			$olWizardSteps = new HTMLControl("ol");
			$i = 1;
			foreach ($this->Pages as $page)
			{
				$li = new HTMLControl("li");
				if ($this->SelectedPageID != "" && ($page->ID == $this->SelectedPageID))
				{
					$li->ClassList[] = "Selected";
				}
				$li->Attributes[] = new WebControlAttribute("role", "tab");
				$li->Attributes[] = new WebControlAttribute("aria-selected", "false");

				$span = new HTMLControl("span");
				$span->ClassList[] = "Number";
				$span->InnerHTML = $i;
				$li->Controls[] = $span;
				
				$span = new HTMLControl("span");
				$span->ClassList[] = "Title";
				$span->InnerHTML = $page->Title;
				$li->Controls[] = $span;
				
				$span = new HTMLControl("span");
				$span->ClassList[] = "Description";
				$span->InnerHTML = $page->Description;
				$li->Controls[] = $span;
				
				if ($page->IconName != "")
				{
					$span = new HTMLControl("i");
					$span->ClassList[] = "fa";
					$span->ClassList[] = "fa-" . $page->IconName;
					$li->Controls[] = $span;
				}
				
				$olWizardSteps->Controls[] = $li;
				$i++;
			}
			$this->Controls[] = $olWizardSteps;
			
			$divWizardPages = new HTMLControl("div");
			$divWizardPages->ClassList[] = "WizardPages";
			
			foreach ($this->Pages as $page)
			{
				$divWizardPage = new HTMLControl("div");
				$divWizardPage->Attributes[] = new WebControlAttribute("aria-role", "tabpanel");
				$divWizardPage->ClassList[] = "WizardPage";
				if ($this->SelectedPageID != "" && ($page->ID == $this->SelectedPageID))
				{
					$divWizardPage->ClassList[] = "Selected";
				}
				
				foreach ($page->Controls as $ctl)
				{
					$divWizardPage->Controls[] = $ctl;
				}
				
				$divWizardPages->Controls[] = $divWizardPage;
			}
			$this->Controls[] = $divWizardPages;
		}
	}
?>