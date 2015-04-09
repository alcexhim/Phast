function TabContainer(parentElement)
{
	this.ParentElement = parentElement;
	
	this.mvarSelectedTabID = null;
	this.GetSelectedTabID = function()
	{
		return this.mvarSelectedTabID;
	};
	
	this.SetSelectedTab = function(tab)
	{
		var tabContainer = this.ParentElement;
		if (tabContainer == null) return;
		
		var tabs = tabContainer.childNodes[0];
		var tabPages = tabContainer.childNodes[1];
		var selectedIndex = -1;
		for (var i = 0; i < tabs.childNodes.length; i++)
		{
			if (System.ClassList.Contains(tabs.childNodes[i], "Selected"))
			{
				System.ClassList.Remove(tabs.childNodes[i], "Selected");
			}
			
			if (tabs.childNodes[i] === tab)
			{
				selectedIndex = i;
				System.ClassList.Add(tabs.childNodes[i], "Selected");
			}
		}
		for (var i = 0; i < tabPages.childNodes.length; i++)
		{
			if (selectedIndex > -1 && selectedIndex < tabPages.childNodes.length && i == selectedIndex)
			{
				System.ClassList.Add(tabPages.childNodes[i], "Selected");
			}
			else
			{
				System.ClassList.Remove(tabPages.childNodes[i], "Selected");
			}
		}
		
		System.SetClientProperty(this.ID, "SelectedTabIndex", selectedIndex);
		
		if (tabs.childNodes[selectedIndex] != null && tabs.childNodes[selectedIndex].attributes["data-id"] != null)
		{
			this.mvarSelectedTabID = tabs.childNodes[selectedIndex].attributes["data-id"].value;
		}
		
		var attOnClientTabChanged = tabContainer.attributes["data-onclienttabchanged"];
		if (attOnClientTabChanged != null)
		{
			eval(attOnClientTabChanged.value);
		}
	};
	
	var tabContainer = this.ParentElement;
	var tabs = tabContainer.childNodes[0];
	for (var i = 0; i < tabs.childNodes.length; i++)
	{
		(function(i, tc)
		{
			tabs.childNodes[i].addEventListener("click", function(e)
			{
				tc.SetSelectedTab(tabs.childNodes[i]);
				
				e.preventDefault();
				e.stopPropagation();
				return false;
			});
		})(i, this);
	}
	
	eval("window." + tabContainer.attributes["id"].value + " = this;");
}
window.addEventListener("load", function(e)
{
	var tbss = document.getElementsByClassName("TabContainer");
	for (var i = 0; i < tbss.length; i++)
	{
		tbss[i].ObjectReference = new TabContainer(tbss[i]);
	}
});