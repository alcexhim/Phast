function AdditionalDetailWidget(parent)
{
	this.Parent = parent;
	this.Show = function ()
	{
		this.Parent.classList.add("Visible");
	};
	this.Hide = function ()
	{
		this.Parent.classList.remove("Visible");
	};

	this.TextLink = null;
	this.ButtonLink = null;
}
window.addEventListener("load", function(e)
{
	var items = document.getElementsByClassName("AdditionalDetailWidget");
	for (var i = 0; i < items.length; i++)
	{
		var obj = new AdditionalDetailWidget(items[i]);
		items[i].ObjectReference = obj;

		obj.TextLink = items[i].childNodes[0];
		obj.ButtonLink = items[i].childNodes[1];

		(function(itm)
		{
			itm.ButtonLink.addEventListener("click", function (e)
			{
				if (e.button == System.MouseButtons.Left)
				{
					itm.Show();
				}
			});
		})(obj);
	}
});
window.addEventListener("mousedown", function (e)
{
	var sender = null;
	if (!e) e = window.event;
	if (e.target)
	{
		sender = e.target;
	}
	else if (e.srcElement)
	{
		sender = e.srcElement;
	}
	if (!System.TerminateIfSenderIs(sender, ["AdditionalDetailWidget"]))
	{
		var items = document.getElementsByClassName("AdditionalDetailWidget");
		for (var i = 0; i < items.length; i++)
		{
			items[i].ObjectReference.Hide();
		}
	}
});