<script type="text/javascript">
ColorSelector.init = function() {
  this.sForm  = "edit-supervision-graph-series";
  this.sColor = "color";
  this.sColorView = "supervisioon-graph-series-color";
  this.pop();
}
</script>

<form name="edit-supervision-graph-series" method="post" action="?m=dPpatients" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="@class" value="CSupervisionGraphSeries" />
  <input type="hidden" name="supervision_graph_axis_id" value="{{$series->supervision_graph_axis_id}}" />
  <input type="hidden" name="callback" value="SupervisionGraph.callbackEditSeries" />
  {{mb_key object=$series}}
  
  <table class="main form">
    {{mb_include module=system template=inc_form_table_header object=$series}}
    
    <tr>
      <th>{{mb_label object=$series field=title}}</th>
      <td>{{mb_field object=$series field=title}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$series field=value_type_id}}</th>
      <td>{{mb_field object=$series field=value_type_id autocomplete="true,1,50,true,true" form="edit-supervision-graph-series"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$series field=value_unit_id}}</th>
      <td>{{mb_field object=$series field=value_unit_id autocomplete="true,1,50,true,true" form="edit-supervision-graph-series"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$series field=integer_values}}</th>
      <td>{{mb_field object=$series field=integer_values}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$series field=color}}</th>
      <td>
        {{mb_field object=$series field=color hidden=true}}
        <button type="button" class="search" onclick="ColorSelector.init()">
          {{tr}}Choose{{/tr}}
          <span id="supervisioon-graph-series-color" 
                style="display: inline-block; vertical-align: top; padding: 0; margin: 0; border: none; width: 16px; height: 16px; background-color: {{if $series->color}}#{{$series->color}}{{else}}transparent{{/if}}; ">
          </span>
        </button>
      </td>
    </tr>
    
    <tr>
      <th></th>
      <td>
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        
        {{if $series->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax: true, typeName:'', objName:'{{$series->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

<div id="supervision-graph-series-list">
  
</div>
