<table class="main tbl">
  <tr>
    <th class="narrow"></th>
    <th class="narrow"></th>
    <th>{{tr}}CSupervisionGraphPack-back-graph_links{{/tr}}</th>
  </tr>
  {{foreach from=$pack->_ref_graph_links item=_link}}
    <tr>
      <td class="narrow">
        <button class="edit compact notext" onclick="SupervisionGraph.editGraphToPack({{$_link->_id}})">
          {{tr}}Edit{{/tr}}
        </button>
        {{if $_link->graph_class == "CSupervisionTimedData"}}
          <img src="images/icons/text.png" title="{{tr}}CSupervisionTimedData{{/tr}}" />
        {{elseif $_link->graph_class == "CSupervisionGraph"}}
          <img src="images/icons/chart.png" title="{{tr}}CSupervisionGraph{{/tr}}" />
        {{else}}
          <img src="images/icons/image.png" title="{{tr}}CSupervisionTimedPicture{{/tr}}" />
        {{/if}}
      </td>
      <td class="narrow">{{$_link->rank}}</td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_link->_ref_graph->_guid}}');">
          {{$_link->_ref_graph}}
        </span>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="4" class="empty">{{tr}}CSupervisionGraphToPack.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  <tr>
    <td colspan="4">
      <button class="compact" onclick="SupervisionGraph.editGraphToPack(0, '{{$pack->_id}}', 'CSupervisionGraph')">
        <img src="images/icons/chart.png" title="{{tr}}CSupervisionGraph{{/tr}}" />
        {{tr}}CSupervisionGraph{{/tr}}
      </button>
      <button class="compact" onclick="SupervisionGraph.editGraphToPack(0, '{{$pack->_id}}', 'CSupervisionTimedData')">
        <img src="images/icons/text.png" title="{{tr}}CSupervisionTimedData{{/tr}}" />
        {{tr}}CSupervisionTimedData{{/tr}}
      </button>
      <button class="compact" onclick="SupervisionGraph.editGraphToPack(0, '{{$pack->_id}}', 'CSupervisionTimedPicture')">
        <img src="images/icons/image.png" title="{{tr}}CSupervisionTimedPicture{{/tr}}" />
        {{tr}}CSupervisionTimedPicture{{/tr}}
      </button>
    </td>
  </tr>
</table>