function TextBox(parentElement)
{
	this.ParentElement = parentElement;
	this.TextBoxElement = parentElement.childNodes[0].childNodes[1];
	this.DropDownElement = parentElement.childNodes[1];
	
	if (parentElement.attributes["name"] != null)
	{
		this.Name = parentElement.attributes["name"].value;
	}
	else
	{
		this.Name = "";
	}
	if (parentElement.attributes["data-multiselect"] != null)
	{
		this.EnableMultipleSelection = (parentElement.attributes["data-multiselect"].value == "true");
	}
	else
	{
		this.EnableMultipleSelection = false;
	}
	if (parentElement.attributes["data-suggestion-url"] != null)
	{
		this.SuggestionURL = parentElement.attributes["data-suggestion-url"].value;
	}
	else
	{
		this.SuggestionURL = null;
	}
	
	this.Focus = function()
	{
		this.TextBoxElement.focus();
	};
	this.ClearText = function()
	{
		this.TextBoxElement.value = "";
	};
	this.GetText = function()
	{
		return this.TextBoxElement.value;
	};
	this.SetText = function(value)
	{
		this.TextBoxElement.value = value;
	};
	
	this.TextBoxElement.onfocus = function(sender, e)
	{
		if (sender.TextBoxElement.attributes["data-auto-open"] != null)
		{
			if (sender.TextBoxElement.attributes["data-auto-open"].value == "true")
			{
				sender.Refresh();
				sender.DropDown.Open();
			}
		}
	}.PrependArgument(this);
	
	this.RefreshTimeout = null;
	
	this.TextBoxElement.onkeyup = function(sender, e)
	{
		if (e.keyCode == 27) // ESC
		{
			sender.DropDown.Close();
		}
		else
		{
			if (sender.RefreshTimeout != null)
			{
				window.clearTimeout(sender.RefreshTimeout);
			}
			sender.RefreshTimeout = window.setTimeout(function()
			{
				sender.Refresh();
			}, 100);
		}
	}.PrependArgument(this);
	
	this.Refresh = function()
	{
		var ret = null;
		if (this.Suggest)
		{
			ret = this.Suggest(this.GetText());
		}
		
		if (ret != null)
		{
			var html = "";
			html += this.FormatStart();
			for (var i = 0; i < ret.length; i++)
			{
				html += this.FormatItem(ret[i], (i % 2) != 0);
			}
			html += this.FormatEnd();
			
			this.DropDown.SetInnerHTML(html);
			this.DropDown.Open();
		}
		else if (this.SuggestionURL)
		{
			var xhr = new XMLHttpRequest();
			xhr.parentTextbox = this;
			xhr.onreadystatechange = function()
			{
				if (xhr.readyState === 4)
				{
					if (xhr.status != 200)
					{
						console.log("TextBox: XMLHttpRequest returned response code " + xhr.status + ": " + xhr.statusText);
						return;
					}
					
					var html = "";
					html += xhr.parentTextbox.FormatStart();
					var obj = JSON.parse(xhr.responseText);
					if (obj.result == "success")
					{
						for (var i = 0; i < obj.items.length; i++)
						{
							html += xhr.parentTextbox.FormatItem(obj.items[i]);
						}
					}
					html += xhr.parentTextbox.FormatEnd();
					
					xhr.parentTextbox.DropDown.SetInnerHTML(html);
					xhr.parentTextbox.DropDown.Open();
				}
			};
			xhr.open('GET', this.SuggestionURL.replace(/\%1/g, this.GetText()), true);
			xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhr.send(null);  // No data need to send along with the request.
		}
		else
		{
			console.error("TextBox: no data retrieval functionality (SuggestionURL/Suggest) has been implemented");
		}
	};
	this.FormatStart = function()
	{
		return "<div class=\"Menu\" style=\"width: 100%;\">";
	};
	this.FormatItemID = function(item)
	{
		return item.ID;
	};
	this.FormatItemText = function(item)
	{
		return "<span class=\"Title\">" + item.Title + "</span>"
			+ "<span class=\"Subtitle\">" + item.Subtitle + "</span>"
			+ "<span class=\"Description\">" + item.Description + "</span>";
	};
	this.FormatItemTargetURL = function(item)
	{
		return item.TargetURL;
	};
	this.FormatItem = function(item, alternate)
	{
		var html = "<a";
		if (alternate)
		{
			html += " class=\"Alternate\"";
		}
		html += " href=\"" + this.FormatItemTargetURL(item) + "\" onclick=\"" + this.ID + ".AddItem('" + this.FormatItemID(item) + "');\">" + this.FormatItemText(item) + "</a>";
		return html;
	};
	this.FormatEnd = function()
	{
		return "</div>";
	};
	
	var uxparent = this;
	this.DropDown = 
	{
		"SetInnerHTML": function(html)
		{
			var popup = uxparent.DropDownElement;
			popup.innerHTML = html;
		},
		"Open": function()
		{
			var popup = uxparent.DropDownElement;
			popup.style.position = "absolute";
			popup.style.width = uxparent.ParentElement.offsetWidth + "px";
			popup.style.display = "block";
		},
		"Close": function()
		{
			var popup = uxparent.DropDownElement;
			popup.style.display = "none";
		}
	};
	/*
	
	this.Suggest = function(filter)
	{
		return null;
	};
	
	this.SelectedItems = new Array();
	
	this.CountItems = function()
	{
		var items = this.GetElement("items");
		return items.childNodes.length - 1;
	};
	
	this.AddItem = function(item)
	{
		if (this.EnableMultipleSelection)
		{
			var i = this.CountItems();
			
			var html = this.GetElement("items").innerHTML;
			html += "<span id=\"Textbox_" + this.ID + "_items_" + i + "\" class=\"TextboxSelectedItem\">";
			html += "<span class=\"TextboxSelectedItemText\">" + this.FormatItemText(item) + "</span>";
			html += "<a class=\"TextboxSelectedItemCloseButton\" onclick=\"" + this.ID + ".RemoveItemAtIndex(" + i + ");\" href=\"#\">x</a>";
			html += "</span>";
			this.GetElement("items").innerHTML = html;
			
			this.GetElement("popup").style.display = "none";
			
			this.SelectedItems.push(item);
		}
		else
		{
			this.SelectedItems = new Array();
			this.SelectedItems.push(item);
			this.SetText(this.FormatItemText(item));
		}
	};
	this.RemoveItemAtIndex = function(index)
	{
		var items = this.GetElement("items");
		index++;
		items.removeChild(items.childNodes[index]);
	};
	
	*/
}
window.addEventListener("load", function(e)
{
	var textBoxes = document.getElementsByClassName("TextBox");
	for (var i = 0; i < textBoxes.length; i++)
	{
		textBoxes[i].NativeObject = new TextBox(textBoxes[i]);
		if (textBoxes[i].id != "") eval("window." + textBoxes[i].id + " = document.getElementById('" + textBoxes[i].id + "').NativeObject;");
	}
});