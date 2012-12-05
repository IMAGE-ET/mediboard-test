<table class="main tbl">
  <tr>
    <th></th>
    <th></th>
    <th>{{tr}}CSupervisionGraphPack-back-graph_links{{/tr}}</th>
  </tr>
  {{foreach from=$pack->_ref_graph_links item=_link}}
    <tr>
      <td class="narrow">
        <button class="edit compact notext" onclick="SupervisionGraph.editGraphToPack({{$_link->_id}})">
          {{tr}}Edit{{/tr}}
        </button>
      </td>
      <td class="narrow">{{$_link->rank}}</td>
      <td>{{$_link->_ref_graph}}</td>
    </tr>
  {{/foreach}}
  <tr>
    <td colspan="3">
      <button class="add compact" onclick="SupervisionGraph.editGraphToPack(0, '{{$pack->_id}}', 'CSupervisionGraph')">{{tr}}CSupervisionGraph{{/tr}}</button>
      <button class="add compact" onclick="SupervisionGraph.editGraphToPack(0, '{{$pack->_id}}', 'CSupervisionTimedData')">{{tr}}CSupervisionTimedData{{/tr}}</button>
    </td>
  </tr>
</table>