<style xmlns="http://www.w3.org/1999/html">
  .graph-legend {
    vertical-align: top;
    line-height: 1;
    padding-left: 1em !important;
    padding-top: 0.2em !important;
  }

  .graph-legend table {
    border-spacing: 0;
    border-collapse: collapse;
  }
</style>

<script type="text/javascript">
plotHover = function(event, pos, item) {
  if (item) {
    var key = item.dataIndex+"-"+item.seriesIndex;
    if (previousPoint != key) {
      var legend_labels = $$('.legend-onhover');
      legend_labels.each(function(item) {
        item.removeClassName('legend-onhover');
      });

      window.previousPoint = key;
      jQuery("#flot-tooltip").remove();
      var oPh = $(event.target.id);
      var top = item.pageY;
      var left;
      if (item.pageX < oPh.offsetLeft) {
        left = oPh.offsetLeft + 30;
      }
      else {
        left = item.pageX - 15;
      }

      var content = item.series.data[item.dataIndex].date;
      if (item.series.data[item.dataIndex].hour != null) {
        content = content + ' ' + item.series.data[item.dataIndex].hour;
      }

      content = content + "<br /><strong>" + item.datapoint[1];
      if (item.series.bandwidth.show) {
        content = content + "/" + item.series.data[item.dataIndex][2];
      }
      content = content + " " + item.series.unit;

      if (item.series.data[item.dataIndex].users != null) {
        content = content + "</strong>";
        item.series.data[item.dataIndex].users.each(function(user) {
          content = content + "<br />" + user;
        });
      }

      $$("body")[0].insert(DOM.div({className: "tooltip", id: "flot-tooltip"}, content).setStyle({
        position: 'absolute',
        top:  top + "px",
        left: left + "px",
        opacity: 0.8,
        backgroundColor: '#000000',
        color: '#FFFFFF',
        borderRadius: '4px',
        textAlign: 'center',
        maxWidth: '300px',
        whiteSpace: 'normal'
      }));

      var legend_labels = $$('#legend-' + event.target.id.substring(6) + ' td.legendLabel');
      var i = item.seriesIndex;

      if (item.series.bars.show) {
        i = 0;
      }
      if (i >= legend_labels.length) {
        i = legend_labels.length - 1;
      }
      legend_labels[i].addClassName('legend-onhover');
    }
  }
  else {
    $$('.legend-onhover').invoke('removeClassName', 'legend-onhover');

    jQuery("#flot-tooltip").remove();
    window.previousPoint = null;
  }
};

drawGraphs = function() {
  {{foreach from=$graphs item=_graph key=_title}}
    {{assign var=_id value=$_title|md5}}

    var oData = {{$_graph.datas|@json}};
    var oOptions = {{$_graph.options|@json}};

    oOptions.legend = {container: jQuery('#legend-{{$_id}}')};
    var oPh = jQuery('#graph-{{$_id}}');
    oPh.bind('plothover', plotHover);
    var plot = jQuery.plot(oPh, oData, oOptions);

    /* Adding the values for the bandwidth */
    oData.each(function(serie) {
      if (serie.bars) {
        serie.data.each(function (data) {
          var oPoint = plot.pointOffset({x: data[0], y: data[1]});
          oPh.append('<div style="position: absolute; left:' + (oPoint.left + 5) + 'px; top: ' + (oPoint.top + 5) + 'px; font-size: smaller">' + data[1] + '</div>');
        });
       }
      else if(serie.bandwidth) {
        serie.data.each(function (data) {
          var max = Math.max(data[1], data[2]);
          var min = Math.min(data[1], data[2]);
          var oPointMax = plot.pointOffset({x: data[0], y: max, yaxis: serie.yaxis});
          var oPointMin = plot.pointOffset({x: data[0], y: min, yaxis: serie.yaxis});

          oPh.append('<div style="position: absolute; left: ' + (oPointMax.left - 8) + 'px; top: ' + (oPointMax.top - 15) + 'px; font-size: smaller">' + max + '</div>');
          oPh.append('<div style="position: absolute; left: ' + (oPointMin.left - 8) + 'px; top: ' + (oPointMin.top + 5) + 'px; font-size: smaller">' + min + '</div>');
        });
      }
    });
  {{/foreach}}
}
</script>

<ul id="tab-constantes-widget" class="control_tabs small" style="font-size: 0.8em;">
{{foreach from=$graphs item=_graph key=_title}}
  <li><a href="#tab-{{$_title|md5}}">{{$_title}}</a></li>
{{/foreach}}
</ul>

{{foreach from=$graphs item=_graph key=_title}}
  {{assign var=_id value=$_title|md5}}

  <div id="tab-{{$_id}}">
    <table class="layout">
      <tr>
        <td>
            <div id="graph-{{$_id}}" style="width: 350px; height: 120px;"></div>
        </td>
        <td id="legend-{{$_id}}" class="graph-legend"></td>
      </tr>
    </table>
  </div>
{{/foreach}}

<script>
  Main.add(function(){
    drawGraphs();
    Control.Tabs.create("tab-constantes-widget");
  })
</script>