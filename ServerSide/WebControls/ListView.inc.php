<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	use Phast\WebScript;
	use Phast\WebStyleSheetRule;
	
	use Phast\HTMLControls\Anchor;
	use Phast\HTMLControls\HTMLControlTable;
	
	use Phast\HTMLControls\HTMLControlForm;
	use Phast\HTMLControls\HTMLControlFormMethod;
	
	use Phast\Enumeration;
	use Phast\HTMLControl;
	use Phast\HTMLControls\Input;
	use Phast\HTMLControls\InputType;
	use Phast\HTMLControls\Literal;
						
	abstract class ListViewMode extends Enumeration
	{
		const Icon = 1;
		const Tile = 2;
		const Detail = 3;
		const Thumbnail = 4;
	}
	
	class ListViewColumnCheckBox extends ListViewColumn
	{
		public $Checked;
		
		public function __construct($id = null, $title = null, $imageURL = null, $width = null, $checked = false)
		{
			parent::__construct($id, $title, $imageURL, ($width == null ? "64px" : $width));
			$this->Checked = $checked;
		}
	}
	class ListViewColumn
	{
		public $ID;
		public $Title;
		public $ImageURL;
		/**
		 * True if this ListViewColumn should be hidden on mobile devices; false if it should be displayed.
		 * @var boolean
		 */
		public $MobileHidden;
		public $Visible;
		public $Width;
		
		public $Template;
		
		public function __construct($id = null, $title = null, $imageURL = null, $width = null)
		{
			$this->ID = $id;
			$this->Title = $title;
			$this->ImageURL = $imageURL;
			$this->MobileHidden = false;
			$this->Visible = true;
			$this->Width = $width;

			$this->ParseChildElements = false;
		}
	}
	class ListViewItem
	{
		public $ID;
		public $Columns;
		public $Checked;
		public $Selected;
		public $NavigateURL;
		public $OnClientClick;
		
		public $Value;
		
		public function GetColumnByID($id)
		{
			foreach ($this->Columns as $column)
			{
				if ($column->ID == $id) return $column;
			}
			return null;
		}
		
		public function __construct($columns = null, $selected = false)
		{
			if ($columns != null)
			{
				$this->Columns = $columns;
			}
			$this->Selected = $selected;
			$this->ParseChildElements = true;
		}
	}
	class ListViewItemColumn
	{
		public $ID;
		public $Text;
		public $Content;
		public $UserData;
		
		public $ParseChildElements;
		
		public function __construct($id = null, $content = null, $text = null, $userData = null)
		{
			$this->ID = $id;
			$this->Content = $content;
			if ($text == null) $text = $content;
			$this->Text = $text;
			$this->UserData = $userData;
			
			$this->ParseChildElements = true;
		}
	}
	class ListView extends WebControl
	{
		public $AllowFiltering;
		
		public $EnableAddRemoveRows;
		public $EnableMultipleSelection;
		
		/**
		 * The columns on this ListView.
		 * @var ListViewColumn[]
		 */
		public $Columns;
		/**
		 * The items on this ListView.
		 * @var ListViewItem[]
		 */
		public $Items;

		public $EnableHotTracking;
		public $ShowBorder;
		public $ShowGridLines;
		public $HighlightAlternateRows;
		
		public $EnableRowCheckBoxes;
		public $PlaceholderText;
		
		public $Mode;
		
		public $OnItemActivate;
		
		public function GetColumnByID($id)
		{
			foreach ($this->Columns as $column)
			{
				if ($column->ID == $id) return $column;
			}
			return null;
		}
		
		public function __construct()
		{
			parent::__construct();
			$this->Columns = array();
			$this->Items = array();
			$this->AllowFiltering = true;
			$this->Mode = ListViewMode::Detail;

			$this->ShowBorder = true;
			$this->ShowGridLines = true;
			$this->HighlightAlternateRows = true;
			$this->EnableAddRemoveRows = false;
			$this->EnableHotTracking = true;
			$this->EnableMultipleSelection = false;
			
			$this->ParseChildElements = true;
			$this->PlaceholderText = "There are no items";
		}
		
		protected function OnInitialize()
		{
			$parent = $this->FindParentPage();
			if ($parent != null) $parent->Scripts[] = new WebScript("$(System.StaticPath)/Scripts/Controls/ListView.js");
		}
		
		protected function RenderContent()
		{
			$div = new HTMLControl("div");
			$div->ClassList[] = "ListView";
			if (count($this->Items) <= 0) $div->ClassList[] = "Empty";
			
			foreach ($this->ClassList as $str)
			{
				$div->ClassList[] = $str;
			}
			foreach ($this->Attributes as $att)
			{
				$div->Attributes[] = $att;
			}
			
			$div->ID = $this->ID;
			
			// set up CSS classes for properties
			if ($this->ShowBorder) $div->ClassList[] = "HasBorder";
			if ($this->EnableHotTracking) $div->ClassList[] = "HotTracking";
			if ($this->ShowGridLines) $div->ClassList[] = "GridLines";
			if ($this->HighlightAlternateRows) $div->ClassList[] = "AlternateRowHighlight";
			if ($this->AllowFiltering) $div->ClassList[] = "AllowFiltering";
			if ($this->EnableRowCheckBoxes) $div->ClassList[] = "RowCheckBoxes";
			if ($this->EnableMultipleSelection) $div->ClassList[] = "MultiSelect";
			if ($this->EnableAddRemoveRows) $div->ClassList[] = "EnableAddRemoveRows";
			if ($this->Width != null) $div->StyleRules[] = new WebStyleSheetRule("width", $this->Width);
			
			switch ($this->Mode)
			{
				case "Detail":
				case ListViewMode::Detail:
				{
					$div->Attributes[] = new WebControlAttribute("data-mode", "Detail");
					break;
				}
				case "Thumbnail":
				case ListViewMode::Thumbnail:
				{
					$div->Attributes[] = new WebControlAttribute("data-mode", "Thumbnail");
					break;
				}
				case "Icon":
				case ListViewMode::Icon:
				{
					$div->Attributes[] = new WebControlAttribute("data-mode", "Icon");
					break;
				}
				case "Tile":
				case ListViewMode::Tile:
				{
					$div->Attributes[] = new WebControlAttribute("data-mode", "Tile");
					break;
				}
			}
			
			$divColumnHeaders = new HTMLControl("div");
			$divColumnHeaders->ClassList[] = "ListViewColumnHeaders";
			
			$lvcCount = 0;
			
			$divItemColumn = new HTMLControl("div");
			$divItemColumn->ClassList[] = "ListViewColumnHeader";
			$divItemColumn->ClassList[] = "AddRemoveRowColumnHeader";
			
			$aAdd = new HTMLControl("a");
			$aAdd->ClassList[] = "Add";
			$divItemColumn->Controls[] = $aAdd;
			
			$divColumnHeaders->Controls[] = $divItemColumn;
			$lvcCount++;
			
			$count = count($this->Columns);
			for ($i = 0; $i < $count; $i++)
			{
				$column = $this->Columns[$i];
				$divColumnHeader = new HTMLControl("div");
				$divColumnHeader->Attributes[] = new WebControlAttribute("data-id", $column->ID);
				$divColumnHeader->ClassList[] = "ListViewColumnHeader";
				
				if ($column->Width != null)
				{
					$divColumnHeader->StyleRules[] = new WebStyleSheetRule("width", $column->Width);
				}
				$classList = array();
				if ($column->MobileHidden) $divColumnHeader->ClassList[] = "MobileHidden";
				
				if (get_class($column) == "Phast\\WebControls\\ListViewColumnCheckBox")
				{
					$input = new Input();
					$input->Type = InputType::CheckBox;
					$divColumnHeader->Controls[] = $input;
				}
				else if (get_class($column) == "Phast\\WebControls\\ListViewColumn")
				{
					$link = new Anchor();
					$link->TargetScript = "lvListView.Sort('" . $column->ID . "'); return false;";
					$link->InnerHTML = $column->Title;
					$divColumnHeader->Controls[] = $link;
				}
				else
				{
					$literal = new Literal();
					$literal->Value = "<!-- Undefined column class: " . get_class($column) . " -->";
					$divColumnHeader->Controls[] = $literal;
				}
				
				if (!$column->Visible)
				{
					$divColumnHeader->ClassList[] = "Hidden";
				}
				else
				{
					$lvcCount++;
				}

				if ($lvcCount == 1)
				{
					$divColumnHeader->ClassList[] = "FirstVisibleChild";
				}
				else if ($lvcCount == count($this->Columns))
				{
					$divColumnHeader->ClassList[] = "LastVisibleChild";
				}

				$divItemTemplate = new HTMLControl("div");
				$divItemTemplate->ClassList[] = "ItemTemplate";
				$divItemTemplate->Content = $column->Template;
				$divColumnHeader->Controls[] = $divItemTemplate;
				
				$divColumnHeaders->Controls[] = $divColumnHeader;
				
				if ($i < $count - 1)
				{
					$divColumnResizer = new HTMLControl("div");
					$divColumnResizer->ClassList[] = "ColumnResizer";
					$divColumnHeaders->Controls[] = $divColumnResizer;
				}
			}
			
			$div->Controls[] = $divColumnHeaders;
			
			$divEmptyMessage = new HTMLControl("div");
			$divEmptyMessage->ClassList[] = "ListViewEmptyMessage";
			$divEmptyMessage->InnerHTML = $this->PlaceholderText;
			$div->Controls[] = $divEmptyMessage;
			
			$divItems = new HTMLControl("div");
			$divItems->ClassList[] = "ListViewItems";
			
			foreach ($this->Items as $item)
			{
				$divItem = new HTMLControl("div");
				$divItem->ClassList[] = "ListViewItem";
				if ($item->Value != null)
				{
					$divItem->Attributes[] = new WebControlAttribute("data-value", $item->Value);
				}
				
				$count = count($this->Columns);
				
				$lvcCount = 0;
				
				$divItemColumn = new HTMLControl("div");
				$divItemColumn->ClassList[] = "ListViewItemColumn";
				$divItemColumn->ClassList[] = "AddRemoveRowItemColumn";
				
				$aAdd = new HTMLControl("a");
				$aAdd->ClassList[] = "Add";
				$divItemColumn->Controls[] = $aAdd;
				
				$aRemove = new HTMLControl("a");
				$aRemove->ClassList[] = "Remove";
				$divItemColumn->Controls[] = $aRemove;
				
				$divItem->Controls[] = $divItemColumn;
				$lvcCount++;
				
				for ($i = 0; $i < $count; $i++)
				{
					$column = $this->Columns[$i];
					$divItemColumn = new HTMLControl("div");
					
					if (!$column->Visible)
					{
						$divItemColumn->ClassList[] = "Hidden";
					}
					else
					{
						$lvcCount++;
					}

					if ($lvcCount == 1)
					{
						$divItemColumn->ClassList[] = "FirstVisibleChild";
					}
					else if ($lvcCount == $max)
					{
						$divItemColumn->ClassList[] = "LastVisibleChild";
					}
					
					$divItemColumn->ClassList[] = "ListViewItemColumn";
					
					$col = $item->GetColumnByID($this->Columns[$i]->ID);
					if ($col != null)
					{
						$divItemColumn->ExtraData = $col->UserData;
						$divItemColumn->Content = $col->Content;
					}
					$divItem->Controls[] = $divItemColumn;
					
					if ($i < $count - 1)
					{
						$divColumnResizer = new HTMLControl("div");
						$divColumnResizer->ClassList[] = "ColumnResizer";
						$divItem->Controls[] = $divColumnResizer;
					}
				}
				
				$divItems->Controls[] = $divItem;
			}
			$div->Controls[] = $divItems;
			$div->Render();
		}
	}
?>