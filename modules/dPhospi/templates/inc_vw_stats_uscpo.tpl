<script type="text/javascript">
  var graph = {{$graph|@json}};
  
  Main.add(function(){
    Flotr.draw($('graph_uscpo'), graph.series, graph.options);
    var form = getForm("filter_uscpo");
    Calendar.regField(form.date_min);
    Calendar.regField(form.date_max);
  });
</script>

{{mb_include module=hospi template=inc_form_stats type=uscpo}}

<div style="width: 640px; height: 480px; float: left; margin: 1em;" id="graph_uscpo"></div>