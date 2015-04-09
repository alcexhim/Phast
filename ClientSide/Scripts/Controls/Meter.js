function Meter(parentElement)
{
	this.ParentElement = parentElement;
	this.ContentWrapperElement = this.ParentElement.childNodes[0];
	this.ContentElement = this.ContentWrapperElement.childNodes[0];
	this.CanvasElement = this.ContentWrapperElement.childNodes[1];
	this.LabelElement = this.ParentElement.childNodes[1];
	
	this.get_Title = function()
	{
		return this.LabelElement.innerHTML;
	};
	this.set_Title = function(value)
	{
		this.LabelElement.innerHTML = value;
	};
	
	this.ParentElement.addEventListener("contextmenu", function(e)
	{
		var contextMenu = new ContextMenu();
		contextMenu.Items = 
		[
		 	new MenuItemHeader(null, "Meter - " + this.NativeObject.get_Title()),
		 	new MenuItemCommand(null, "Decimal", null),
		 	new MenuItemCommand(null, "Percent", null)
		];
		
		if (this.NativeObject.ParentElement.hasAttribute("data-enable-default-contextmenu") && this.NativeObject.ParentElement.getAttribute("data-enable-default-contextmenu") == "true")
		{
			contextMenu.Show(e.clientX, e.clientY);
		}
		
		e.preventDefault();
		e.stopPropagation();
		return false;
	});
	
	var canvas = this.CanvasElement;
	canvas.height = canvas.width;
	
	var scaleBy = 1;
    var context = canvas.getContext('2d');
	
	// support retina devices
	if (window.devicePixelRatio > 1)
	{
		scaleBy = window.devicePixelRatio;
		// canvas.style.width = canvas.style.height = [options.size, 'px'].join('');
		// canvas.width = canvas.height = options.size * scaleBy;
		context.scale(scaleBy, scaleBy);
	}
    
    // canvas center point
    var centerX = canvas.width / 2;
    var centerY = canvas.height / 2;
    
    // radius of arc
    var lineWidth = 16;
    var radius = (canvas.width - lineWidth) / 2;
    radius -= 2;
    
    // 0% = 0.0, 100% = 2.0
    var minimumValue = this.ParentElement.getAttribute("data-minimum-value");
    var maximumValue = this.ParentElement.getAttribute("data-maximum-value");
    var currentValue = this.ParentElement.getAttribute("data-current-value");
    
    var decimalValue = ((minimumValue + currentValue) / (maximumValue - minimumValue));
    
    context.translate(centerX, centerY);
    context.rotate((-1 / 2 + 0 / 180) * Math.PI);
    
    context.beginPath();
    context.lineWidth = lineWidth;
    context.arc(0, 0, radius, 0, Math.PI * 2 * decimalValue, false);
    
    if (this.ParentElement.hasAttribute("data-foreground-color"))
    {
    	context.strokeStyle = this.ParentElement.getAttribute("data-foreground-color");
    }
    else
    {
    	context.strokeStyle = '#000000';
    }
    context.stroke();
    context.closePath();
    
    // rest of meter
    context.beginPath();
    context.arc(0, 0, radius, Math.PI * 2 * decimalValue, Math.PI * 2, false);
    
    // line color
    if (this.ParentElement.hasAttribute("data-background-color"))
    {
    	context.strokeStyle = this.ParentElement.getAttribute("data-background-color");
    }
    else
    {
    	context.strokeStyle = '#CCCCCC';
    }
    context.stroke();
    context.closePath();
}

window.addEventListener("load", function(e)
{
	var items = document.getElementsByClassName("Meter");
	for (var i = 0; i < items.length; i++)
	{
		items[i].NativeObject = new Meter(items[i]);
	}
});