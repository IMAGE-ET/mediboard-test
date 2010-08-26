{{unique_id var=graph_id}}

<script type="text/javascript">
Main.add(function(){
  var data = {{$data|@json}};
  
  data.options.mouse.trackFormatter = function(obj) {
    return parseInt(obj.y);
  }

  var container = $("consumption-graph-{{$graph_id}}");
  
  Flotr.draw(container, data.series, data.options);
});
</script>

<div style="width: 300px; height: 80px;" id="consumption-graph-{{$graph_id}}"></div>
