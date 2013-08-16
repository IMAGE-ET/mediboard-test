<table class="main tbl">
  <tr>
    <th style="width: 16px;"></th>
    <th>{{mb_title class=CSupervisionGraphSeries field=title}}</th>
    <th>{{mb_title class=CSupervisionGraphSeries field=value_type_id}}</th>
    <th>{{mb_title class=CSupervisionGraphSeries field=value_unit_id}}</th>
    <th class="narrow"></th>
  </tr>
  
  {{foreach from=$series item=_series}}
    <tr>
      <td style="background-color: #{{$_series->color}}"></td>
      <td>
        <a href="#1" onclick="return SupervisionGraph.editSeries({{$_series->_id}})">
          {{$_series}}
        </a>
      </td>
      <td>{{mb_value object=$_series field=value_type_id}}</td>
      <td>{{mb_value object=$_series field=value_unit_id}}</td>
      <td>
        <button class="edit notext compact" onclick="SupervisionGraph.editSeries({{$_series->_id}})">
          {{tr}}Edit{{/tr}}
        </button>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="5" class="empty">
        {{tr}}CSupervisionGraphSeries.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>

<button class="new" onclick="SupervisionGraph.editSeries(0, {{$axis->_id}})">
  {{tr}}CSupervisionGraphSeries-title-create{{/tr}}
</button>