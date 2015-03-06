function Popup(id)
{
	this.ID = id;
	this.Show = function()
	{
		Popup.HideAll();
		
		var obj = document.getElementById("Popup_" + this.ID);
		obj.classList.add("Visible");
	};
	this.Hide = function()
	{
		var obj = document.getElementById("Popup_" + this.ID);
		obj.classList.remove("Visible");
	};
}
Popup.HideAll = function()
{
	var elems = document.getElementsByClassName("Popup");
	for (var i = 0; i < elems.length; i++)
	{
		elems[i].classList.remove("Visible");
	}
};

window.addEventListener("mousedown", function(e)
{
	var sender;
	if (!e)
	{
		e = window.event;
	}
	if (e.target)
	{
		sender = e.target;
	}
	else if (e.srcElement)
	{
		sender = e.srcElement;
	}
	
	while (sender != null)
	{
		if (sender.classList)
		{
			if (sender.classList.contains("Popup"))
			{
				// do not close the popup when we click inside itself
				e.preventDefault();
				e.stopPropagation();
				return;
			}
		}
		sender = sender.parentNode;
		if (sender == null) break;
	}
	Popup.HideAll();
});