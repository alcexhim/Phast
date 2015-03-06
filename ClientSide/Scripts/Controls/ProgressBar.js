function ProgressBar(id)
{
	this.ID = id;
	this.MinimumValue = 0;
	this.MaximumValue = 100;
	this.CurrentValue = 0;
	this.SetCurrentValue = function(value)
	{
		this.CurrentValue = value;
		this.Update();
	};
	this.Update = function()
	{
		var pb_fill = document.getElementById("ProgressBar_" + this.ID + "_ValueFill");
		var pb_label = document.getElementById("ProgressBar_" + this.ID + "_ValueLabel");
		pb_fill.style.width = ((this.CurrentValue / (this.MaximumValue - this.MinimumValue)) * 100).toFixed(0).toString() + "%";
		pb_label.innerHTML = ((this.CurrentValue / (this.MaximumValue - this.MinimumValue)) * 100).toFixed(0).toString() + "%";
	};
}
ProgressBar.Create = function(id, parentElement)
{
	var pb = document.createElement("div");
	pb.className = "ProgressBar";
	
	var pb_fill = document.createElement("div");
	pb_fill.id = "ProgressBar_" + id + "_ValueFill";
	pb.appendChild(pb_fill);
	
	var pb_label = document.createElement("div");
	pb_label.id = "ProgressBar_" + id + "_ValueLabel";
	pb.appendChild(pb_label);
	
	parentElement.appendChild(pb);
	
	pb.nativeObject = new ProgressBar(id);
	return pb.nativeObject;
}