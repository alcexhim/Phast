<?php
	namespace Phast\WebControls;

	use Phast\Enumeration;
	use Phast\System;
	use Phast\WebControl;
	
	use Phast\WebControlAttribute;
	use Phast\WebStyleSheetRule;
	
	use Phast\HTMLControl;
	use Phast\HTMLControls\Anchor;
use Phast\Phast;
		
	/**
	 * Provides an enumeration of predefined values for orientation of a menu.
	 * @author Michael Becker
	 */
	abstract class MenuOrientation extends Enumeration
	{
		/**
		 * The menu is displayed horizontally.
		 * @var int 1
		 */
		const Horizontal = 1;
		/**
		 * The menu is displayed vertically.
		 * @var int 2
		 */
		const Vertical = 2;
	}
	
	class Menu extends WebControl
	{
		/**
		 * Determines whether the menu displays horizontally or vertically.
		 * @var MenuOrientation
		 */
		public $Orientation;
		/**
		 * A collection of MenuItems on this Menu.
		 * @var MenuItem
		 */
		public $Items;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->Items = array();
			$this->ParseChildElements = true;
			
			$this->TagName = "ul";
			$this->ClassList[] = "Menu";
			
			if ($this->Top != null) $this->StyleRules[] = new WebStyleSheetRule("top", $this->Top);
			if ($this->Left != null) $this->StyleRules[] = new WebStyleSheetRule("left", $this->Left);
			if ($this->Width != null) $this->StyleRules[] = new WebStyleSheetRule("width", $this->Width);
			if ($this->Height != null) $this->StyleRules[] = new WebStyleSheetRule("height", $this->Height);
			if ($this->MaximumWidth != null) $this->StyleRules[] = new WebStyleSheetRule("max-width", $this->MaximumWidth);
			if ($this->MaximumHeight != null) $this->StyleRules[] = new WebStyleSheetRule("max-height", $this->MaximumHeight);
		}
		
		public function GetItemByID($id)
		{
			foreach ($this->Items as $item)
			{
				if ($item->ID == $id) return $item;
			}
			return null;
		}
		
		protected function RenderBeginTag()
		{
			if ($this->Orientation == "Horizontal" || $this->Orientation == MenuOrientation::Horizontal)
			{
				$this->ClassList[] = "Horizontal";
			}
			else if ($this->Orientation == "Vertical" || $this->Orientation == MenuOrientation::Vertical)
			{
				$this->ClassList[] = "Vertical";
			}
			
			$this->Controls = array();
			
			$liArrow = new HTMLControl("li");
			$liArrow->ClassList[] = "Arrow";
			$this->Controls[] = $liArrow;
			
			foreach ($this->Items as $menuItem)
			{
				$this->Controls[] = Menu::CreateMenuItemControl($menuItem);
			}
			parent::RenderBeginTag();
		}
		
		public static function CreateMenuItemControl($menuItem)
		{
			if (get_class($menuItem) == "Phast\\WebControls\\MenuItemCommand")
			{
				$li = new HTMLControl();
				$li->TagName = "li";
				
				if ($menuItem->Visible)
				{
					$li->ClassList[] = "Visible";
				}
				if ($menuItem->Selected)
				{
					$li->ClassList[] = "Selected";
				}
				if (count($menuItem->Items) > 0)
				{
					$li->ClassList[] = "HasChildren";
				}
				
				$a = new Anchor();
				$a->TargetURL = $menuItem->TargetURL;
				if ($menuItem->OnClientClick != null)
				{
					$a->Attributes[] = new WebControlAttribute("onclick", $menuItem->OnClientClick);
				}
				
				if ($menuItem->IconName != "")
				{
					$iIcon = new HTMLControl();
					$iIcon->TagName = "i";
					$iIcon->ClassList[] = "fa";
					$iIcon->ClassList[] = "fa-" . $menuItem->IconName;
					$a->Controls[] = $iIcon;
				}
				
				if ($menuItem->Description != null)
				{
					$spanTitle = new HTMLControl();
					$spanTitle->TagName = "span";
					$spanTitle->ClassList[] = "Title";
					$spanTitle->InnerHTML = $menuItem->Title;
					$a->Controls[] = $spanTitle;
					
					$spanDescription = new HTMLControl();
					$spanDescription->TagName = "span";
					$spanDescription->ClassList[] = "Description";
					$spanDescription->InnerHTML = $menuItem->Description;
					$a->Controls[] = $spanDescription;
				}
				else
				{
					$spanTitle = new HTMLControl();
					$spanTitle->TagName = "span";
					$spanTitle->ClassList[] = "Title NoDescription";
					$spanTitle->InnerHTML = $menuItem->Title;
					$a->Controls[] = $spanTitle;
				}
					
				$li->Controls[] = $a;
					
				if (count($menuItem->Items) > 0)
				{
					$menu = new Menu();
					foreach ($menuItem->Items as $item1)
					{
						$menu->Items[] = $item1;
					}
					$li->Controls[] = $menu;
				}
				return $li;
			}
			else if (get_class($menuItem) == "Phast\\WebControls\\MenuItemHeader")
			{
				$li = new HTMLControl();
				$li->TagName = "li";
				if ($menuItem->Visible)
				{
					$li->ClassList[] = "Visible";
				}
				$li->ClassList[] = "Header";
				
				$spanHeader = new HTMLControl();
				$spanHeader->TagName = "span";
				$spanHeader->ClassList[] = "Text";
				$spanHeader->InnerHTML = $menuItem->Title;
				$li->Controls[] = $spanHeader;
				
				return $li;
			}
			else if (get_class($menuItem) == "Phast\\WebControls\\MenuItemSeparator")
			{
				$hr = new HTMLControl();
				if ($menuItem->Visible)
				{
					$hr->ClassList[] = "Visible";
				}
				$hr->TagName = "hr";
				return $hr;
			}
			else
			{
				System::WriteErrorLog("Unknown MenuItem class: " . get_class($menuItem));
			}
		}
	}
	
	class MenuItem extends WebControl
	{
		public $Visible;
		
		public function __construct()
		{
			$this->ParseChildElements = true;
			$this->Visible = true;
		}
	}
	class MenuItemHeader extends MenuItem
	{
		public $Title;
		public $Subtitle;
		public function __construct($title = null, $subtitle = null)
		{
			parent::__construct();
			$this->Title = $title;
			$this->Subtitle = $subtitle;
		}
	}
	class MenuItemCommand extends MenuItem
	{
		public $Items;
		public $IconName;
		public $Title;
		public $TargetURL;
		public $OnClientClick;
		public $Selected;
		public $Description;
		
		public function GetItemByID($id)
		{
			foreach ($this->Items as $item)
			{
				if ($item->ID == $id) return $item;
			}
			return null;
		}
		
		public function __construct($title = null, $targetURL = null, $onClientClick = null, $description = null, $items = null)
		{
			parent::__construct();
			$this->Title = $title;
			$this->TargetURL = $targetURL;
			$this->OnClientClick = $onClientClick;
			$this->Description = $description;
			if ($items == null) $items = array();
			$this->Items = $items;
		}
	}
	class MenuItemSeparator extends MenuItem
	{
	}
	class MenuItemMenu extends MenuItem
	{
		public $Title;
		public $Items;
		
		public function __construct($title, $menuItems = array())
		{
			$this->Title = $title;
			$this->Items = $menuItems;
		}
	}
?>