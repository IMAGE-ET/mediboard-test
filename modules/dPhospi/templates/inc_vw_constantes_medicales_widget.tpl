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

<ul id="tab-constantes-widget" class="control_tabs small" style="font-size: 0.8em;">
{{foreach from=$graphs item=_graph key=_title}}
  <li><a href="#tab-{{$_title|md5}}">{{$_title}}</a></li>
{{/foreach}}
</ul>

{{foreach from=$graphs item=_graph key=_title}}
  {{assign var=_id value=$_title|md5}}

  {{*$_graph|@json*}}

  <script>
    Main.add(function(){
      var graph = {{$_graph|@json}};
      var options = graph.options;

      options.mouse.trackFormatter = function(obj){
        var date = new Date();
        date.setTime(obj.x);

        return "<strong>#{label}</strong><br />#{date}<br /><strong style='font-size: 1.2em;'>#{value} #{unit}</strong>".interpolate({
          label: obj.series.label,
          date:  date.toLocaleDateTime(),
          value: obj.y,
          unit:  obj.series.unit
        });
      };

      options.legend.container = $("legend-{{$_id}}");

      Flotr.draw($("graph-{{$_id}}"), graph.series, options);
    });
  </script>

  <div id="tab-{{$_id}}">
    <table class="layout">
      <tr>
        <td>
          <div id="graph-{{$_id}}" style="width: 300px; height: 100px;"></div>
        </td>
        <td id="legend-{{$_id}}" class="graph-legend"></td>
      </tr>
    </table>
  </div>
{{/foreach}}

<script>
  Main.add(function(){
    Control.Tabs.create("tab-constantes-widget");
  })
</script>