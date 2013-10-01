<script type="text/javascript">
Main.add(function(){
  var ph = jQuery("#placeholder-preview");
  var series = {{$data.series|@json}};
  var xaxes = {{$data.xaxes|@json}};

  xaxes[0].ticks = 10;

  var plothover = function (event, pos, item) {
    if (item) {
      var key = item.dataIndex+"-"+item.seriesIndex;
      if (window.previousPoint != key) {
        window.previousPoint = key;
        jQuery("#flot-tooltip").remove();

        var contents = SupervisionGraph.formatTrack(item);

        $$("body")[0].insert(DOM.div({className: "tooltip", id: "flot-tooltip"}, contents).setStyle({
          position: 'absolute',
          top:  item.pageY + 5 + "px",
          left: item.pageX + 5 + "px"
        }));
      }
    }
    else {
      jQuery("#flot-tooltip").remove();
      window.previousPoint = null;
    }
  };

  ph.bind("plothover", plothover);

  jQuery.plot(ph, series, {
    grid: { hoverable: true },
    series: SupervisionGraph.defaultSeries,
    xaxes: xaxes,
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
    {{*<span class="title">{{$data.title}}</span>*}}
  </div>
  <div id="placeholder-preview" style="width:600px;height:{{$graph->height}}px;"></div>
</div>