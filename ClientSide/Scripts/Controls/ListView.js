var ListViewMode =
{
	"Detail": 1,
	"Tile": 2
};

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
	
	this.SelectedRows =
	{
		"NativeObject": null,
		"Clear": function()
		{
			for (var i = 0; i < this.NativeObject.ItemsElement.children.length; i++)
			{
				System.ClassList.Remove(this.NativeObject.ItemsElement.children[i], "Selected");
			}
		},
		"AddRange": function(indices)
		{
			for (var i = 0; i < indices.length; i++)
			{
				System.ClassList.Add(this.NativeObject.ItemsElement.children[indices[i]], "Selected");
			}
		},
		"RemoveRange": function(indices)
		{
			for (var i = 0; i < indices.length; i++)
			{
				System.ClassList.Remove(this.NativeObject.ItemsElement.children[indices[i]], "Selected");
			}
		},
		"Add": function(index)
		{
			this.AddRange([index]);
		},
		"Remove": function(index)
		{
			this.RemoveRange([index]);
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
				this.NativeObject.SelectedRows.Clear();
				this.NativeObject.SelectedRows.Add(this.m_Index);
			}
			e.preventDefault();
			e.stopPropagation();
			return false;
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
