/* * The MIT License

Copyright (c) 2010, 2011, 2012 by Juergen Marsch

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
function NearByReturnData(){
	this.found = false;
	this.serie = null;
	this.datapoint = null;
	this.value = null;
	this.pos = null;
	this.label = null;
}
function NearByData(){
	this.mouseX = null;
	this.mouseY = null;
	this.editActive = false;
	this.serie = null;
	this.datapoint = null;
	this.pos = null;
	this.value = null;
	this.label = null;
}
function NearByReturn(){
	this.item = new NearByReturnData();
	this.edit = new NearByReturnData();
	this.found = function(){ return (this.item.found || this.edit.found); }
}
function HighLighting(plot, eventHolder, findNearbyFNC, active){
	this.findNearby = findNearbyFNC;
	this.highlights = [];
	this.mouseItem = new NearByData();
	this.editMode = false;
	this.eventMode = 'replace';
	var hl = this;
	var options = plot.getOptions();
	if(options.series.editmode) hl.editMode = options.series.editmode;
	var target = $(plot.getCanvas()).parent();
	if(active && options.grid.hoverable)
	{ if(hl.eventMode=='replace')
    { eventHolder.unbind('mousemove').mousemove(onMouseMove); }
    else if (hl.eventMode=='append')
    { eventHolder.mousemove(onMouseMove); }
    else
    { eventHolder.unbind('mousemove').mousemove(onMouseMove); }
  }
	if(active && options.grid.clickable)
  { if(hl.eventMode=='replace')
    { eventHolder.unbind('click').click(onClick); }
    else if(hl.eventMode=='append')
    { eventHolder.click(onClick) ; }
    else
    { eventHolder.unbind('click').click(onClick); }
  }
	if(hl.editMode == true) target.mousedown(onMouseDown);
	function onMouseDown(e){
		var r;
		if(options.series.editmode) {
			var offset = plot.offset();
			var mouseX = parseInt(e.pageX - offset.left);
			var mouseY = parseInt(e.pageY - offset.top);
			hl.mouseItem.editActive = false;
			r = hl.findNearby(mouseX, mouseY);
			if(r.item.found == true) {
				hl.mouseItem.editActive = true;
				hl.mouseItem.serie = r.item.serie;
				hl.mouseItem.datapoint = r.item.datapoint;
				hl.mouseItem.label = r.item.label;
				hl.mouseItem.mouseX = mouseX;
				hl.mouseItem.mouseY = mouseY;
				target.mouseup(onMouseUp);
				target.unbind('mousedown');
			}
		}
	}
	function onMouseUp(e){
		var r;
		if(options.series.editmode==true && hl.mouseItem.editActive==true) {
			var offset = plot.offset();
			var mouseX = parseInt(e.pageX - offset.left);
			var mouseY = parseInt(e.pageY - offset.top);
			r = hl.findNearby(mouseX, mouseY);
			hl.mouseItem.editActive = false;
			if(r.edit.found==true)
			{	hl.mouseItem.mouseX = mouseX;
				hl.mouseItem.mouseY = mouseY;
				hl.mouseItem.value = r.edit.value;
				hl.mouseItem.pos = r.edit.pos;
			}
			plot.triggerRedrawOverlay();
			target.unbind('mouseup');
			target.mousedown(onMouseDown);
		}
		var pos = { pageX: e.pageX, pageY: e.pageY };
		target.trigger("datadrop", [pos, hl.mouseItem] );
	}
	function onMouseMove(e){ triggerClickHoverEvent('plothover', e);}
	function onClick(e){ triggerClickHoverEvent('plotclick', e);}
	function triggerClickHoverEvent(eventname, e){
		var r; var item;
		var offset = plot.offset();
		var mouseX = parseInt(e.pageX - offset.left);
		var mouseY = parseInt(e.pageY - offset.top);
		r = hl.findNearby(mouseX, mouseY);
		if(r.found()==true) {
			if(hl.mouseItem.editActive == true) {
				hl.mouseItem.mouseX = mouseX;
				hl.mouseItem.mouseY = mouseY;
				hl.mouseItem.value = r.edit.value;
				hl.mouseItem.pos = r.edit.pos;
				plot.triggerRedrawOverlay();
			}
			else {
				highlight(r);
				var pos = { pageX: e.pageX, pageY: e.pageY };
				target.trigger(eventname, [pos, r.item] );
			}
		}
		else {
			unhighlight();
		}
	}

	function highlight(nearByData){
		var i = indexOfHighlight(nearByData);
		if(i == -1){
			hl.highlights.push(nearByData);
			plot.triggerRedrawOverlay();
		}
	}
	function unhighlight(){
		hl.highlights = [];
		plot.triggerRedrawOverlay();
	}
	function indexOfHighlight(nearByData){
		for(var i = 0; i < hl.highlights.length; ++i){
			var h = hl.highlights[i];
			if (h.item.datapoint == nearByData.item.datapoint) return i;
		}
		return -1;
	}
}


/*
 * The MIT License

Copyright (c) 2010, 2011, 2012 by Juergen Marsch

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
/*
Flot plugin for bandwidth data sets

  series: {
    bandwidth: { active: true, lineWidth: 8
    ,drawBandwidth: function(ctx, bandwidth, x, y1, y2, color) { alert(color); }
  }
data: [

  $.plot($("#placeholder"), [{ data: [ [y, x, size], [....], ...], bandwidth: {show: true, lineWidth: 5} } ])

*/

(function ($){
	var options ={
		series:{
			bandwidth:{
				active: false
				,show: false
				,fill: true
				,lineWidth: 4
				,highlight: { opacity: 0.5 }
				,drawBandwidth: drawBandwidthDefault
			}
		}
	};
	function drawBandwidthDefault(ctx,bandwidth, x,y1,y2,color,isOverlay){ 
	  ctx.beginPath();
		ctx.strokeStyle = color;
		ctx.lineWidth = bandwidth.lineWidth;
		ctx.lineCap = "round";
		ctx.moveTo(x, y1);
		ctx.lineTo(x, y2);
		ctx.stroke();
	}
    function init(plot){
		var  data = null, canvas = null, target = null, axes = null, offset = null, hl=null;
		plot.hooks.processOptions.push(processOptions);
		function processOptions(plot,options){
			if(options.series.bandwidth.active){
			  plot.hooks.draw.push(draw);
				//plot.hooks.bindEvents.push(bindEvents);
				//plot.hooks.drawOverlay.push(drawOverlay);
			}
		}
		function draw(plot, ctx){
			var series;
			canvas = plot.getCanvas();
			target = $(canvas).parent();
			axes = plot.getAxes();
			offset = plot.getPlotOffset();
			data = plot.getData();
			for (var i = 0; i < data.length; i++){
				series = data[i];
				if(series.bandwidth.show){
					for (var j = 0; j < series.data.length; j++){
						drawBandwidth(ctx,series.data[j],series.bandwidth,series.color,false);
					}
				}
			}
		}
		function drawBandwidth(ctx,data,bandwidth,color,isOverlay){
			var x,y1,y2;
			x = offset.left + axes.xaxis.p2c(data[0]);
			y1 = offset.top + axes.yaxis.p2c(data[1]);
			y2 = offset.top + axes.yaxis.p2c(data[2]);
			bandwidth.drawBandwidth(ctx,bandwidth,x,y1,y2,color,isOverlay);
		}
		function bindEvents(plot, eventHolder){
			var options = plot.getOptions();
			hl = new HighLighting(plot, eventHolder, findNearby, options.series.bandwidth.active)
		}
		function findNearby(mousex, mousey){
			var series,r;
			data = plot.getData();
			axes = plot.getAxes();
			r = new NearByReturn();
			r.item = findNearByItem(mousex,mousey);
			return r;
			function findNearByItem(mousex,mousey){
				var r = new NearByReturnData();
				for(var i = 0;i < data.length;i++){
					series = data[i];
					if(series.bandwidth.show){
						for(var j = 0; j < series.data.length; j++){
							var x,y1,y2,dataitem;
							dataitem = series.data[j];
							x = axes.xaxis.p2c(dataitem[0]) - series.bandwidth.lineWidth / 2;
							y1 = axes.yaxis.p2c(dataitem[1]);
							y2 = axes.yaxis.p2c(dataitem[2]);
							if (mousex >= x && mousex <= (x + series.bandwidth.lineWidth)){
								if (y1 < y2){ if (mousey > y1 && mousey < y2) { r = CreateNearBy(i,j); } }
								else{ if (mousey > y2 && mousey < y1) { r = CreateNearBy(i,j);} }
							}
						}
					}
				}
				return r;
			}
			function CreateNearBy(i,j){
				var r = new NearByReturnData();
				r.found = true;
				r.serie = i;
				r.datapoint = j;
				r.value = data[i].data[j];
				return r;
			}
		}
		function drawOverlay(plot, octx){if(hl.highlights.length > 0)
			octx.save();
			octx.clearRect(0, 0, target.width(), target.height());
			for(i = 0; i < hl.highlights.length; ++i){ drawHighlight(hl.highlights[i]);}
			octx.restore();
			function drawHighlight(item){
				var s = data[item.item.serie];
				var c = "rgba(255, 255, 255, " + s.bandwidth.highlight.opacity + ")";
				drawBandwidth(octx,s.data[item.item.datapoint],s.bandwidth,c,true);
			}
		}
	}
	$.plot.plugins.push({
		init: init,
		options: options,
		name: 'bandwidth',
		version: '0.4.3'
	});
})(jQuery);