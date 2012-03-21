<script type="text/javascript">
  var graph = {{$graph|@json}};
  
  Main.add(function(){
    Flotr.draw($('graph'), graph.series, graph.options);
  });
</script>

<div style="width: 640px; height: 480px; float: left; margin: 1em;" id="graph"></div>