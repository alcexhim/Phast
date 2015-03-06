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
			if (tabs.childNodes[i].className == "Tab Visible Selected") tabs.childNodes[i].className = "Tab Visible";
			if (tabs.childNodes[i] === tab)
			{
				selectedIndex = i;
				tabs.childNodes[i].className = "Tab Visible Selected";
			}
		}
		for (var i = 0; i < tabPages.childNodes.length; i++)
		{
			if (tabPages.childNodes[i].className == "TabPage Selected") tabPages.childNodes[i].className = "TabPage";
		}
		if (selectedIndex > -1 && selectedIndex < tabPages.childNodes.length)
		{
			tabPages.childNodes[selectedIndex].className = "TabPage Selected";
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