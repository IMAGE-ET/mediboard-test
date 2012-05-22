<script type="text/javascript">
  var graph = {{$graph|@json}};
  
  for (var i = 0; i < 4 ; i ++) {
    graph['series'][i].markers.labelFormatter = function(obj) {
      return Math.round(obj.data[obj.index][2] * 100) + "%";
    }
  }
  
  Main.add(function(){
    Flotr.draw($('graph_occupation'), graph.series, graph.options);
  });
</script>

{{mb_include module=hospi template=inc_form_stats type=occupation}}

<div style="width: 640px; height: 480px; float: left; margin: 1em;" id="graph_occupation"></div>