var ListViewMode =
{
	"Detail": 1,
	"Tile": 2
};

function ListView(parentElement)
{
	this.ParentElement = parentElement;
	
	this.SetSelectedRows = function(indices)
	{
		for (var i = 0; i < parentElement.tBodies[0].rows.length; i++)
		{
			parentElement.tBodies[0].rows[i].className = "";
		}
		for (var i = 0; i < indices.length; i++)
		{
			parentElement.tBodies[0].rows[indices[i]].className = "Selected";
		}
	};
	this.SetSelectedRow = function(index)
	{
		for (var i = 0; i < parentElement.tBodies[0].rows.length; i++)
		{
			if (i == index)
			{
				parentElement.tBodies[0].rows[i].className = "Selected";
			}
			else
			{
				parentElement.tBodies[0].rows[i].className = "";
			}
		}
	};

	if (parentElement.tagName.toUpperCase() == "TABLE")
	{
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
		
		for (var i = 0; i < parentElement.tBodies[0].rows.length; i++)
		{
			(function(i)
			{
				var row = parentElement.tBodies[0].rows[i];
				row.addEventListener("mousedown", function(e)
				{
					parentElement.NativeObject.SetSelectedRow(i);
					e.preventDefault();
					e.stopPropagation();
					return false;
				});
				row.addEventListener("contextmenu", function(e)
				{
					parentElement.NativeObject.SetSelectedRow(i);
					e.preventDefault();
					e.stopPropagation();
					return false;
				});
			})(i);
		}
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
