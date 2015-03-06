function Menu(parentElement)
{
	this.ParentElement = parentElement;
	this.set_Expanded = function(value)
	{
		switch (value)
		{
			case true:
			{
				System.ClassList.Add(this.ParentElement.parentNode, "Opened");
				break;
			}
			case false:
			{
				System.ClassList.Remove(this.ParentElement.parentNode, "Opened");
				break;
			}
		}
	};
	
	for (var i = 0; i < this.ParentElement.childNodes.length; i++)
	{
		this.ParentElement.childNodes[i].childNodes[0].addEventListener("click", function(e)
		{
			if (this.parentNode.childNodes.length > 1)
			{
				System.ClassList.Toggle(this.parentNode, "Opened");
			}
			
			this.blur();
			
			if (this.href == "" || this.href == window.location.href + "#")
			{
				e.preventDefault();
				e.stopPropagation();
				return false;
			}
		});
	}
}

window.addEventListener("load", function(e)
{
	var items = document.getElementsByClassName("Menu");
	for (var i = 0; i < items.length; i++)
	{
		if (items[i].NativeObject) continue;
		items[i].NativeObject = new Menu(items[i]);
	}
});

function ContextMenu()
{
	this.Items = [];
	this.ParentElement = null;
	this.Show = function(x, y, parent)
	{
		if (this.ParentElement == null)
		{
			var elem = document.createElement("div");
			elem.className = "Menu Popup";
			elem.addEventListener("contextmenu", function(e)
			{
				e.preventDefault();
				e.stopPropagation();
				return false;
			});
			
			for (var i = 0; i < this.Items.length; i++)
			{
				if (this.Items[i].ClassName == "MenuItemCommand")
				{
					var elem1 = document.createElement("a");
					elem1.setAttribute("href", "#");
					elem1.addEventListener("click", function(e)
					{
						this.NativeObject.Hide();
						this.MenuItem.Execute();
						
						e.preventDefault();
						e.stopPropagation();
						return false;
					});
					elem1.innerHTML = this.Items[i].Title;
					elem1.NativeObject = this;
					elem1.MenuItem = this.Items[i];
					
					elem.appendChild(elem1);
				}
				else if (this.Items[i].ClassName == "MenuItemSeparator")
				{
					var elem1 = document.createElement("div");
					elem1.className = "Separator";
					elem1.innerHTML = this.Items[i].Title;
					elem.appendChild(elem1);
				}
			}
			
			elem.style.left = x + "px";
			elem.style.top = y + "px";
			
			if (parent == null) parent = document.body;
			
			parent.appendChild(elem);
			this.ParentElement = elem;
		}
		this.ParentElement.className = "Menu Popup Visible";
	};
	this.Hide = function()
	{
		if (this.ParentElement == null) return;
		this.ParentElement.className = "Menu Popup";
	};
}
function MenuItemSeparator(id, title)
{
	this.ClassName = "MenuItemSeparator";
	this.ID = id;
	this.Title = title;
}
function MenuItemCommand(id, title, onclick)
{
	this.ClassName = "MenuItemCommand";
	this.ID = id;
	this.Title = title;
	this.OnClientClick = onclick;
	
	this.Execute = function()
	{
		if (this.OnClientClick != null) this.OnClientClick();
	};
}