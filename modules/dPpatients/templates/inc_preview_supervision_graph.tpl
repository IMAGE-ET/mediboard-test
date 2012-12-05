<script type="text/javascript">
Main.add(function(){
  var ph = jQuery("#placeholder-preview");
  var series = {{$data.series|@json}};
  
  //ph.bind("plothover", plothover);
  jQuery.plot(ph, series, {
    series: SupervisionGraph.defaultSeries,
    xaxes: {{$data.xaxes|@json}},
    yaxes: {{$data.yaxes|@json}}
  });
});
</script>

<button onclick="SupervisionGraph.preview({{$supervision_graph_id}})" class="change">Rafraîchir l'aperçu</button>

<div style="margin: 6px;">
  <div class="yaxis-labels">
    {{foreach from=$data.yaxes|@array_reverse item=_yaxis}}
      <div>
        {{$_yaxis.label}}
        <div class="symbol">{{$_yaxis.symbolChar|smarty:nodefaults}}</div>
      </div>
    {{/foreach}}
    <span class="title">{{$data.title}}</span>
  </div>
  <div id="placeholder-preview" style="width:600px;height:180px;"></div>
</div>