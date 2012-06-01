<script type="text/javascript">
  var graph = {{$graph|@json}};
  
  for (var i = 0; i < graph['series'].length ; i ++) {
    graph['series'][i].markers.labelFormatter = function(obj) {
      if (parseFloat(obj.data[obj.index][2]) > 0) {
        return Math.round(obj.data[obj.index][2] * 100) + "%";
      }
      else {
        return ""
      }
    }
  }
  
  Main.add(function(){
    Flotr.draw($('graph_occupation'), graph.series, graph.options);
  });
</script>

{{mb_include module=hospi template=inc_form_stats type=occupation}}

<div style="width: 1000px; height: 520px; float: left; margin: 1em;" id="graph_occupation"></div>