{{mb_script module=system script=useragent}}

<script>
  Main.add(function () {
    drawBrowserGraph();
  });

  drawBrowserGraph = function() {
    var oPh, oData, oOptions, sTitle;
    var oData   = {{$graph.data|@json}};
    var oOptions = {{$graph.options|@json}};

    var oPh = jQuery("#browser_placeholder");
    oPh.bind('plothover', plotHover);
    var plot = jQuery.plot(oPh, oData, oOptions);
  };

  plotHover = function(event, pos, item) {
    if (item) {
      jQuery("#flot-tooltip").remove();

      aLabel  = item.series.label.split("-");
      content = "<strong>" + item.series.data[0][1] + "</strong>";

      $$("body")[0].insert(DOM.div({className: "tooltip", id: "flot-tooltip"}, content).setStyle({
        position: 'absolute',
        top: pos.pageY + 5 + "px",
        left: pos.pageX + 5 + "px",
        opacity: 0.8,
        backgroundColor: '#000000',
        color: '#FFFFFF',
        borderRadius: '4px',
        textAlign: 'center',
        maxWidth: '300px',
        whiteSpace: 'normal'
      }));
    }
    else {
      jQuery("#flot-tooltip").remove();
    }
  };
</script>

<div id="browser_graph">
  <table class="layout">
    <tr>
      <td>
        <p style="text-align: center">
          <strong>{{$graph.title}}</strong>
        </p>
        <div id="browser_placeholder" style="width: 400px; height: 300px;"></div>
      </td>
    </tr>
  </table>
</div>

<table class="main tbl">
  <tr>
    <th class="narrow"></th>
    <th class="narrow">{{mb_title class=CUserAgent field=browser_name}}</th>
    <th class="narrow">{{mb_title class=CUserAgent field=browser_version}}</th>

    <th class="narrow">{{mb_title class=CUserAgent field=platform_name}}</th>
    <th class="narrow">{{mb_title class=CUserAgent field=platform_version}}</th>

    <th class="narrow">{{mb_title class=CUserAgent field=device_name}}</th>
    <th class="narrow">{{mb_title class=CUserAgent field=device_maker}}</th>
    <th class="narrow">{{mb_title class=CUserAgent field=device_type}}</th>
    <th class="narrow">{{mb_title class=CUserAgent field=pointing_method}}</th>
    <th class="narrow">{{tr}}CUserAgent-back-user_authentications{{/tr}}</th>
    <th>{{mb_title class=CUserAgent field=user_agent_string}}</th>
  </tr>
  
  {{foreach from=$user_agents item=_user_agent}}
    <tr>
      <td>
        <button class="edit notext compact" onclick="UserAgent.edit({{$_user_agent->_id}})">{{tr}}Edit{{/tr}}</button>
      </td>

      <td style="text-align: right;">{{mb_value object=$_user_agent field=browser_name}}</td>
      <td>{{mb_value object=$_user_agent field=browser_version}}</td>

      <td style="text-align: right;">{{mb_value object=$_user_agent field=platform_name}}</td>
      <td {{if $_user_agent->platform_version == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=platform_version}}</td>

      <td {{if $_user_agent->device_name == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=device_name}}</td>
      <td {{if $_user_agent->device_maker == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=device_maker}}</td>
      <td {{if $_user_agent->device_type == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=device_type}}</td>
      <td {{if $_user_agent->pointing_method == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=pointing_method}}</td>

      <td>
        <a href="?m=system&amp;tab=vw_user_authentications&amp;user_agent_id={{$_user_agent->_id}}">
          {{$_user_agent->_count.user_authentications}}
        </a>
      </td>

      <td class="compact text">{{mb_value object=$_user_agent field=user_agent_string}}</td>
    </tr>
  {{/foreach}}
</table>