<script type="text/javascript">
Main.add(function(){
  var data = {{$data|@json}};
  
  data.options.mouse.trackFormatter = function(obj) {
    return data.options.xaxis.ticks[obj.index] + " : " + parseInt(obj.y);
  }

  var container = $("consumption-graph-{{$product->_guid}}");
  
  Flotr.draw(container, data.series, data.options);
});
</script>

<div style="width: 300px; height: 50px;" id="consumption-graph-{{$product->_guid}}"></div>
