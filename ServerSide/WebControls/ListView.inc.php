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
	
	abstract class ListViewMode extends Enumeration
	{
		const Icon = 1;
		const Tile = 2;
		const Detail = 3;
	}
	
	class ListViewColumnCheckBox extends ListViewColumn
	{
		public $Checked;
		
		public function __construct($name = null, $title = null, $imageURL = null, $width = null, $checked = false)
		{
			parent::__construct($name, $title, $imageURL, ($width == null ? "64px" : $width));
			$this->Checked = $checked;
		}
	}
	class ListViewColumn
	{
		public $Name;
		public $Title;
		public $ImageURL;
		public $Width;
		
		public function __construct($name = null, $title = null, $imageURL = null, $width = null)
		{
			$this->Name = $name;
			$this->Title = $title;
			$this->ImageURL = $imageURL;
			$this->Width = $width;
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
		public $Name;
		public $Text;
		public $Content;
		public $OnRetrieveContent;
		public $UserData;
		
		public $ParseChildElements;
		
		public function __construct($name = null, $content = null, $text = null, $onRetrieveContent = null, $userData = null)
		{
			$this->Name = $name;
			$this->Content = $content;
			if ($text == null) $text = $content;
			$this->Text = $text;
			$this->OnRetrieveContent = $onRetrieveContent;
			$this->UserData = $userData;
			
			$this->ParseChildElements = true;
		}
	}
	class ListView extends WebControl
	{
		public $AllowFiltering;
		
		public $EnableAddRemoveRows;
		
		public $Columns;
		public $Items;

		public $EnableHotTracking;
		public $ShowBorder;
		public $ShowGridLines;
		public $HighlightAlternateRows;
		
		public $EnableRowCheckBoxes;
		
		public $Mode;
		
		public function GetColumnByName($name)
		{
			foreach ($this->Columns as $column)
			{
				if ($column->Name == $name) return $column;
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
			$this->HighlightAlternateRows = false;
			$this->EnableAddRemoveRows = false;
			$this->EnableHotTracking = true;
			
			$this->ParseChildElements = true;
		}
		
		protected function OnInitialize()
		{
			$parent = $this->FindParentPage();
			if ($parent != null) $parent->Scripts[] = new WebScript("$(System.StaticPath)/Scripts/Controls/ListView.js");
		}
		
		protected function RenderContent()
		{
			if (count($this->Items) <= 0)
			{
?>
<div class="ListView" style="display: table; margin-left: auto; margin-right: auto;">
	There are no items
</div>
<?php
			}
			else
			{
				switch ($this->Mode)
				{
					case ListViewMode::Detail:
					{
						$table = new HTMLControlTable();
						$table->ClassList[] = "ListView";
						if ($this->ShowBorder)
						{
							$table->ClassList[] = "HasBorder";
						}
						if ($this->EnableHotTracking)
						{
							$table->ClassList[] = "HotTracking";
						}
						if ($this->ShowGridLines)
						{
							$table->ClassList[] = "GridLines";
						}
						if ($this->HighlightAlternateRows)
						{
							$table->ClassList[] = "AlternateRowHighlight";
						}
						if ($this->AllowFiltering)
						{
							$table->ClassList[] = "AllowFiltering";
						}
						if ($this->EnableRowCheckBoxes)
						{
							$table->ClassList[] = "RowCheckBoxes";
						}
						
						$table->StyleRules = array
						(
							new WebStyleSheetRule("margin-left", "auto"),
							new WebStyleSheetRule("margin-right", "auto")
						);
						if ($this->Width != null)
						{
							$table->StyleRules[] = new WebStyleSheetRule("width", $this->Width);
						}
						
						$table->BeginContent();
						$table->BeginHeader();
						$table->BeginRow();
						
						
						if ($this->EnableAddRemoveRows)
						{
							$table->BeginHeaderCell();
							echo("<!-- edit buttons go here -->");
							$table->EndHeaderCell();
						}
						
						$table->BeginHeaderCell(array("ClassNames" => array("RowCheckBoxes")));
						echo("<input type=\"checkbox\" />");
						$table->EndHeaderCell();
						
						foreach ($this->Columns as $column)
						{
							$attributes = array();
							if ($column->Width != null)
							{
								$attributes[] = new WebStyleSheetRule("width", $column->Width);
							}
							
							$table->BeginHeaderCell(array("StyleRules" => $attributes));
							
							if (get_class($column) == "Phast\\WebControls\\ListViewColumnCheckBox")
							{
								echo("<input type=\"checkbox\" />");
							}
							else if (get_class($column) == "Phast\\WebControls\\ListViewColumn")
							{
								$link = new Anchor();
								$link->TargetScript = "lvListView.Sort('" . $column->Name . "'); return false;";
								$link->InnerHTML = $column->Title;
								$link->Render();
							}
							else
							{
								echo("<!-- Undefined column class: " . get_class($column) . " -->");
							}
							
							$table->EndHeaderCell();
						}
						$table->EndRow();
		
						$table->BeginRow(array
						(
							"ClassNames" => array ("Filter")
						));
						
						if ($this->EnableAddRemoveRows)
						{
							$table->BeginCell();
							echo("<!-- unused cell for edit buttons -->");
							$table->EndCell();
						}
						
						$table->BeginHeaderCell(array("ClassNames" => array("RowCheckBoxes")));
						echo("<!-- unused cell for row check boxes -->");
						$table->EndHeaderCell();
						
						foreach ($this->Columns as $column)
						{
							$table->BeginHeaderCell();
							if (get_class($column) == "Phast\\WebControls\\ListViewItemColumn")
							{
								$realColumn = $this->GetColumnByName($column->Name);
								if (get_class($realColumn) == "Phast\\WebControls\\ListViewColumnCheckBox")
								{
								}
								else
								{
									$form = new HTMLControlForm(null, HTMLControlFormMethod::Post);
									$form->BeginContent();
									
									$input = new TextBox();
									$input->Name = "ListView_" . $this->ID . "_Filter_" . $column->Name;
									$input->PlaceholderText = "Filter by " . $column->Title;
									$input->Text = $_POST["ListView_" . $this->ID . "_Filter_" . $column->Name];
									$input->Render();
									
									$form->EndContent();
								}
							}
							$table->EndHeaderCell();
						}
						
						$table->EndRow();
						
						$table->EndHeader();
						$table->BeginBody();
						
						foreach ($this->Items as $item)
						{
							$continueItem = false;
							if ($this->AllowFiltering)
							{
								foreach ($item->Columns as $column)
								{
									if (get_class($column) == "Phast\\WebControls\\ListViewItemColumn")
									{
										$realColumn = $this->GetColumnByName($column->Name);
										if (get_class($realColumn) == "Phast\\WebControls\\ListViewColumnCheckBox")
										{
										}
										else
										{
											if (isset($_POST["ListView_" . $this->ID . "_Filter_" . $column->Name]))
											{
												$vps = $_POST["ListView_" . $this->ID . "_Filter_" . $column->Name];
												if ($vps != "" && (mb_stripos($column->Text, $vps) === false))
												{
													$continueItem = true;
													break;
												}
											}
										}
									}
								}
								if ($continueItem) continue;
							}
							
							$classNames = array();
							if ($item->Selected) $classNames[] = "Selected";
							
							$table->BeginRow(array("ClassNames" => $classNames));
								
							$table->BeginHeaderCell(array("ClassNames" => array("RowCheckBoxes")));
							echo("<input type=\"checkbox\"");
							if ($item->Checked)
							{
								echo(" checked=\"checked\"");
							}
							echo(" />");
							$table->EndHeaderCell();
							
							if ($this->EnableAddRemoveRows)
							{
								$table->BeginCell();
								echo("<!-- edit buttons go here -->");
								$table->EndCell();
							}
							foreach ($item->Columns as $column)
							{
								if (get_class($column) == "Phast\\WebControls\\ListViewItemColumn")
								{
									$realColumn = $this->GetColumnByName($column->Name);
									if (get_class($realColumn) == "Phast\\WebControls\\ListViewColumnCheckBox")
									{
										$table->BeginCell();
										echo("<input type=\"checkbox\" />");
										$table->EndCell();
									}
									else if (get_class($realColumn) == "Phast\\WebControls\\ListViewColumn")
									{
										$table->BeginCell();
										if ($item->NavigateURL != null)
										{
											?><a class="Wrapper" href="<?php echo(System::ExpandRelativePath($item->NavigateURL)); ?>"><?php
										}
										if ($column->OnRetrieveContent != null)
										{
											call_user_func($column->OnRetrieveContent, $column->UserData);
										}
										else
										{
											if ($column->Content == null)
											{
												echo($column->Text);
											}
											else
											{
												echo($column->Content);
											}
										}
										
										if ($item->NavigateURL != null)
										{
											?></a><?php
										}
										$table->EndCell();
									}
								}
							}
							$table->EndRow();
						}
						
						$table->EndBody();
						$table->EndContent();
						break;
					}
					case ListViewMode::Tile:
					{
?>
<div class="ListView TileView" id="ListView_<?php echo($this->ID); ?>">
<?php
	foreach ($this->Items as $item)
	{
		?>
		<a id="ListView_<?php echo($this->ID); ?>_<?php echo($item->ID); ?>" href="<?php
		if ($item->NavigateURL != null)
		{
			echo($item->NavigateURL);
		}
		else
		{
			echo("#");
		}
		?>" onclick="<?php
		if ($item->OnClientClick != null)
		{
			echo($item->OnClientClick);
		}
		else
		{
			echo("return false;");
		}
		?>">
		<?php
			$max = count($item->Columns);
			
			for ($i = 0; $i < $max; $i++)
			{
				if ($i == 0)
				{
					?><span class="ItemText"><?php
				}
				else
				{
					?><span class="ItemDetail"><?php
				}
				echo($item->Columns[$i]->Text);
				?></span><?php
			}
		?>
		</a>
		<?php
	}
?>
</div>
<?php
						break;
					}
				}
			}
		}
	}
?>