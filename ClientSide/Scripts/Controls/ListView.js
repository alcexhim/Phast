var ListViewMode =
{
	"Detail": 1,
	"Tile": 2
};
function ListViewItemActivationMode(value)
{
	this._value = value;
}
ListViewItemActivationMode.SingleClick = new ListViewItemActivationMode(1);
ListViewItemActivationMode.DoubleClick = new ListViewItemActivationMode(2);

function ListViewItemColumn(parentItem)
{
	this.mvarParentItem = parentItem;
	this.get_ParentItem = function()
	{
		return this.mvarParentItem;
	};
	
	this.get_Value = function()
	{
	};
}
function ListViewItem(parentListView, index)
{
	this.mvarParentListView = parentListView;
	this.get_ParentListView = function()
	{
		return this.mvarParentListView;
	};
	
	this.mvarIndex = index;
	this.get_Index = function()
	{
		return this.mvarIndex;
	};
	
	this.get_ParentElement = function()
	{
		return this.get_ParentListView().ItemsElement.children[this.get_Index()];
	};
	
	this.get_Value = function()
	{
		return this.get_ParentElement().getAttribute("data-value");
	};
}
function ListView(parentElement)
{
	this.ParentElement = parentElement;
	this.ColumnHeaderElement = this.ParentElement.children[0];
	this.EmptyMessageElement = this.ParentElement.children[1];
	this.ItemsElement = this.ParentElement.children[2];
	
	this.mvarItemActivationMode = ListViewItemActivationMode.DoubleClick;
	this.get_ItemActivationMode = function()
	{
		return this.mvarItemActivationMode;
	};
	this.set_ItemActivationMode = function(value)
	{
		this.mvarItemActivationMode = value;
	};
	
	if (this.ParentElement.hasAttribute("data-item-activation-mode"))
	{
		switch (this.ParentElement.getAttribute("data-item-activation-mode").toLowerCase())
		{
			case "singleclick":
			{
				this.set_ItemActivationMode(ListViewItemActivationMode.SingleClick);
				break;
			}
			case "doubleclick":
			{
				this.set_ItemActivationMode(ListViewItemActivationMode.DoubleClick);
				break;
			}
		}
	}
	
	this.EventHandlers = 
	{
		"ItemActivated": new System.EventHandler(),
		"SelectionChanging": new System.EventHandler(),
		"SelectionChanged": new System.EventHandler()
	};
	
	this.SelectedRows =
	{
		"NativeObject": null,
		"Clear": function()
		{
			var changed = false;
			for (var i = 0; i < this.NativeObject.ItemsElement.children.length; i++)
			{
				if (System.ClassList.Contains(this.NativeObject.ItemsElement.children[i], "Selected"))
				{
					changed = true;
					break;
				}
			}
			if (!changed) return;
			
			this.NativeObject.EventHandlers.SelectionChanging.Execute();
			for (var i = 0; i < this.NativeObject.ItemsElement.children.length; i++)
			{
				System.ClassList.Remove(this.NativeObject.ItemsElement.children[i], "Selected");
			}
			this.NativeObject.EventHandlers.SelectionChanged.Execute();
		},
		"AddRange": function(indices)
		{
			var changed = false;
			for (var i = 0; i < indices.length; i++)
			{
				if (!System.ClassList.Contains(this.NativeObject.ItemsElement.children[indices[i]], "Selected"))
				{
					changed = true;
					break;
				}
			}
			if (!changed) return;
			
			this.NativeObject.EventHandlers.SelectionChanging.Execute();
			for (var i = 0; i < indices.length; i++)
			{
				System.ClassList.Add(this.NativeObject.ItemsElement.children[indices[i]], "Selected");
			}
			this.NativeObject.EventHandlers.SelectionChanged.Execute();
		},
		"RemoveRange": function(indices)
		{
			var changed = false;
			for (var i = 0; i < indices.length; i++)
			{
				if (System.ClassList.Contains(this.NativeObject.ItemsElement.children[indices[i]], "Selected"))
				{
					changed = true;
					break;
				}
			}
			if (!changed) return;
			
			this.NativeObject.EventHandlers.SelectionChanging.Execute();
			for (var i = 0; i < indices.length; i++)
			{
				System.ClassList.Remove(this.NativeObject.ItemsElement.children[indices[i]], "Selected");
			}
			this.NativeObject.EventHandlers.SelectionChanged.Execute();
		},
		"Add": function(index)
		{
			this.AddRange([index]);
		},
		"Remove": function(index)
		{
			this.RemoveRange([index]);
		},
		"Count": function()
		{
			return this.Get().length;
		},
		"Get": function()
		{
			var items = new Array();
			for (var i = 0; i < this.NativeObject.ItemsElement.children.length; i++)
			{
				if (System.ClassList.Contains(this.NativeObject.ItemsElement.children[i], "Selected"))
				{
					items.push(new ListViewItem(this, i));
				}
			}
			return items;
		},
		"ContainsIndex": function(index)
		{
			return System.ClassList.Contains(this.NativeObject.ItemsElement.children[index], "Selected");
		},
		"Toggle": function(index)
		{
			if (this.ContainsIndex(index))
			{
				this.Remove(index);
			}
			else
			{
				this.Add(index);
			}
		},
		"ToggleRange": function(indices)
		{
			for (var i = 0; i < indices.length; i++)
			{
				this.Toggle(indices[i]);
			}
		}
	};
	this.SelectedRows.NativeObject = this;

	/*
	if (parentElement.tHead != null && parentElement.tHead.rows[0] != null)
	{
		// begin : magic - do not even begin to attempt to understand this logic
		for (var i = 0; i < parentElement.tHead.rows[0].cells.length; i++)
		{
			if (parentElement.tHead.rows[0].cells[i].childNodes[0].className == "CheckBox")
			{
				(function(i)
				{
					parentElement.tHead.rows[0].cells[i].childNodes[1].addEventListener("change", function(e)
					{
						for (var j = 0; j < parentElement.tBodies[0].rows.length; j++)
						{
							parentElement.tBodies[0].rows[j].cells[i].childNodes[0].NativeObject.SetChecked(parentElement.tHead.rows[0].cells[i].childNodes[0].NativeObject.GetChecked());
						}
					});
				})(i);
			}
		}
		// end : magic
	}
	*/
	
	this.ParentElement.addEventListener("mousedown", function(e)
	{
		this.NativeObject.SelectedRows.Clear();
		e.preventDefault();
		e.stopPropagation();
		return false;
	});
	
	for (var i = 0; i < this.ItemsElement.children.length; i++)
	{
		var row = this.ItemsElement.children[i];
		row.m_Index = i;
		row.NativeObject = this;
		row.addEventListener("mousedown", function(e)
		{
			if (e.ctrlKey && System.ClassList.Contains(this.NativeObject.ParentElement, "MultiSelect"))
			{
				this.NativeObject.SelectedRows.Toggle(this.m_Index);
			}
			else if (e.shiftKey && System.ClassList.Contains(this.NativeObject.ParentElement, "MultiSelect"))
			{
				
			}
			else
			{
				if (!(this.NativeObject.SelectedRows.Count() == 1 && this.NativeObject.SelectedRows.ContainsIndex(this.m_Index)))
				{
					this.NativeObject.SelectedRows.Clear();
					this.NativeObject.SelectedRows.Add(this.m_Index);
				}
			}
			e.preventDefault();
			e.stopPropagation();
			return false;
		});
		row.addEventListener("dblclick", function(e)
		{
			if (this.NativeObject.get_ItemActivationMode() == ListViewItemActivationMode.DoubleClick)
			{
				this.NativeObject.EventHandlers.ItemActivated.Execute();
			}
		});
		row.addEventListener("contextmenu", function(e)
		{
			this.NativeObject.SelectedRows.Clear();
			this.NativeObject.SelectedRows.Add(this.m_Index);
			e.preventDefault();
			e.stopPropagation();
			return false;
		});
	}
}
window.addEventListener("load", function(e)
{
	var items = document.getElementsByClassName("ListView");
	for (var i = 0; i < items.length; i++)
	{
		items[i].NativeObject = new ListView(items[i]);
	}
});
