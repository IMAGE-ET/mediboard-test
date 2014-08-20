<script>
  Main.add(function() {
    drawBrowserGraph();
  });

  drawBrowserGraph = function() {
    {{foreach from=$graphs key=_id item=_graph}}
      var oData = {{$_graph.data|@json}};
      var oOptions = {{$_graph.options|@json}};

      var oPh = jQuery("#placeholder_{{$_graph.title}}");
      oPh.bind('plothover', plotHover);
      var plot = jQuery.plot(oPh, oData, oOptions);
    {{/foreach}}
  };

  plotHover = function(event, pos, item) {
    if (item) {
      jQuery("#flot-tooltip").remove();

      content = "<strong>" + item.series.label + "</strong><br />" + item.series.data[0][1];

      $$("body")[0].insert(DOM.div({className: "tooltip", id: "flot-tooltip"}, content).setStyle({
        position:        'absolute',
        top:             pos.pageY + 5 + "px",
        left:            pos.pageX + 5 + "px",
        opacity:         0.8,
        backgroundColor: '#000000',
        color:           '#FFFFFF',
        borderRadius:    '4px',
        textAlign:       'center',
        maxWidth:        '300px',
        whiteSpace:      'normal'
      }));
    }
    else {
      jQuery("#flot-tooltip").remove();
    }
  };
</script>

<div id="browser_graphs" style="clear: both;">
  {{foreach from=$graphs key=_id item=_graph}}
    <div id="browser_graph_{{$_id}}" style="display: inline-block;">
      <table class="layout">
        <tr>
          <td>
            <p style="text-align: center">
              <strong>{{tr}}{{$_graph.title}}{{/tr}}</strong>
            </p>

            {{if $_graph.title == "CUserAuthentication"}}
              <div id="placeholder_{{$_graph.title}}" style="width: 800px; height: 300px;"></div>
            {{elseif $_graph.title == "CUserAgent-browser_version"}}
              <div id="placeholder_{{$_graph.title}}" style="width: 450px; height: 200px;"></div>
            {{else}}
              <div id="placeholder_{{$_graph.title}}" style="width: 300px; height: 200px;"></div>
            {{/if}}
          </td>
        </tr>
      </table>
    </div>
  {{/foreach}}
</div>