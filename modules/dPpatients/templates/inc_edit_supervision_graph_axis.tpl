
<script type="text/javascript">
Main.add(function(){
  {{if $axis->_id}}
  SupervisionGraph.listSeries({{$axis->_id}});
  SupervisionGraph.listAxisLabels({{$axis->_id}});
  {{/if}}
  
  var row = $$("tr[data-axis_id={{$axis->_id}}]")[0];
  if (row) {
    row.addUniqueClassName("selected");
  }

  Control.Tabs.create("axis-tabs");
});
</script>

<form name="edit-supervision-graph-axis" method="post" action="?m=dPpatients" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="@class" value="CSupervisionGraphAxis" />
  <input type="hidden" name="supervision_graph_id" value="{{$axis->supervision_graph_id}}" />
  <input type="hidden" name="callback" value="SupervisionGraph.callbackEditAxis" />
  {{mb_key object=$axis}}
  
  <table class="main form">
    {{mb_include module=system template=inc_form_table_header object=$axis colspan=4}}
    
    <tr>
      <th>{{mb_label object=$axis field=title}}</th>
      <td>{{mb_field object=$axis field=title}}</td>
      
      <th>{{mb_label object=$axis field=display}}</th>
      <td>{{mb_field object=$axis field=display emptyLabel="Seulement les points"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$axis field=limit_low}}</th>
      <td>{{mb_field object=$axis field=limit_low increment=true form="edit-supervision-graph-axis"}}</td>
      
      <th>{{mb_label object=$axis field=limit_high}}</th>
      <td>{{mb_field object=$axis field=limit_high increment=true form="edit-supervision-graph-axis"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$axis field=show_points}}</th>
      <td>{{mb_field object=$axis field=show_points}}</td>
      
      <th>{{mb_label object=$axis field=symbol}}</th>
      <td>{{mb_field object=$axis field=symbol}}</td>
    </tr>
    
    <tr>
      <th></th>
      <td colspan="3">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        
        {{if $axis->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax: true, typeName:'', objName:'{{$axis->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>


<ul class="control_tabs" id="axis-tabs">
  <li><a href="#supervision-graph-series-list">S�ries</a></li>
  <li><a href="#supervision-graph-axis-labels-list">Libell�s d'axes</a></li>
</ul>
<div id="supervision-graph-series-list" style="display: none;"></div>
<div id="supervision-graph-axis-labels-list" style="display: none;"></div>
