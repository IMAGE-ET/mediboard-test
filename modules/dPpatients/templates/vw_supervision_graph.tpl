{{mb_script module=patients script=supervision_graph}}
{{mb_script module=mediusers script=color_selector}}

<script type="text/javascript">
Main.add(function(){
  {{if $supervision_graph_id}}
    SupervisionGraph.editGraph({{$supervision_graph_id}});
  {{/if}}
});
</script>

<table class="main layout">
  <tr>
    <td style="width: 15%">
      <table class="main tbl">
        <tr>
          <th colspan="2" class="title">
            <button class="new notext" onclick="SupervisionGraph.editGraph(0)" style="float: right;">
              {{tr}}New{{/tr}}
            </button>
            Graphiques
          </th>
        </tr>
        
        {{foreach from=$graphs item=_graph}}
          <tr id="list-{{$_graph->_guid}}" class="{{if $_graph->disabled}} opacity-50 {{/if}}">
            <td>
              <a href="#{{$_graph->_guid}}" onclick="return SupervisionGraph.editGraph({{$_graph->_id}})">
                {{mb_value object=$_graph field=title}}
              </a>
            </td>
            <td class="compact">
              {{foreach from=$_graph->_back.axes item=_axis}}
                <div style="clear: both;">
                  {{foreach from=$_axis->_back.series|@array_reverse item=_series}}
                    <span style="float: right; width: 4px; height: 9px; background-color: #{{$_series->color}}; margin-left: 1px;"></span>
                  {{/foreach}}
                  {{mb_include module=patients template=inc_axis_symbol axis=$_axis small=true}}
                  {{$_axis}}
                </div>
              {{/foreach}}
            </td>
          </tr>
        {{foreachelse}}
          <tr>
            <td class="empty" colspan="2">{{tr}}CSupervisionGraph.none{{/tr}}</td>
          </tr>
        {{/foreach}}

        <tr>
          <th colspan="2" class="title">
            <button class="new notext" onclick="SupervisionGraph.editTimedData(0)" style="float: right;">
            {{tr}}New{{/tr}}
            </button>
            Données horodatées
          </th>
        </tr>
        {{foreach from=$timed_data item=_timed_data}}
          <tr id="list-{{$_timed_data->_guid}}" class="{{if $_timed_data->disabled}} opacity-50 {{/if}}">
            <td colspan="2">
              <a href="#edit-{{$_timed_data->_guid}}" onclick="return SupervisionGraph.editTimedData({{$_timed_data->_id}})">
                {{mb_value object=$_timed_data field=title}}
              </a>
            </td>
          </tr>
          {{foreachelse}}
          <tr>
            <td class="empty" colspan="2">{{tr}}CSupervisionTimedData.none{{/tr}}</td>
          </tr>
        {{/foreach}}

        <tr>
          <th colspan="2" class="title">
            <button class="new notext" onclick="SupervisionGraph.editPack(0)" style="float: right;">
              {{tr}}New{{/tr}}
            </button>
            Packs
          </th>
        </tr>
        {{foreach from=$packs item=_pack}}
          <tr id="list-{{$_pack->_guid}}" class="{{if $_pack->disabled}} opacity-50 {{/if}}">
            <td colspan="2">
              <a href="#edit-{{$_pack->_guid}}" onclick="return SupervisionGraph.editPack({{$_pack->_id}})">
                {{mb_value object=$_pack field=title}}
              </a>
            </td>
          </tr>
          {{foreachelse}}
          <tr>
            <td class="empty" colspan="2">{{tr}}CSupervisionGraphPack.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td id="supervision-graph-editor">&nbsp;</td>
  </tr>
</table>
