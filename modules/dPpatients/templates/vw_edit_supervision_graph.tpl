{{mb_script module=dPpatients script=supervision_graph}}
{{mb_script module=mediusers script=color_selector}}

<script type="text/javascript">
Main.add(function(){
  {{if $graph->_id}}
    SupervisionGraph.listAxes({{$graph->_id}});
  {{/if}}
})
</script>

<table class="main layout">
  <tr>
    <td style="width: 15%">
      <a href="?m=dPpatients&amp;tab=vw_edit_supervision_graph&amp;supervision_graph_id=0" class="button new">
        {{tr}}CSupervisionGraph-title-create{{/tr}}
      </a>
      
      <table class="main tbl">
        <tr>
          <th colspan="2" class="title">Graphiques</th>
        </tr>
        <tr>
          <th>{{mb_title class=CSupervisionGraph field=title}}</th>
          <th>{{tr}}CSupervisionGraph-back-axes{{/tr}}</th>
        </tr>
        
        {{foreach from=$graphs item=_graph}}
          <tr {{if $_graph->_id == $graph->_id}} class="selected" {{/if}}>
            <td>
              <a href="?m=dPpatients&amp;tab=vw_edit_supervision_graph&amp;supervision_graph_id={{$_graph->_id}}">
                {{mb_value object=$_graph field=title}}
              </a>
            </td>
            <td class="compact">
              {{foreach from=$_graph->_back.axes item=_axis}}
                <div style="clear: both;">
                  {{foreach from=$_axis->_back.series|@array_reverse item=_series}}
                    <span style="float: right; width: 4px; height: 9px; background-color: #{{$_series->color}}; margin-left: 1px;"></span>
                  {{/foreach}}
                  {{mb_include module=dPpatients template=inc_axis_symbol axis=$_axis small=true}}
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
      </table>
    </td>
    <td>
      <form name="edit-supervision-graph" method="post" action="?m=dPpatients">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="@class" value="CSupervisionGraph" />
        <input type="hidden" name="owner_class" value="CGroups" />
        <input type="hidden" name="owner_id" value="{{$g}}" />
        {{mb_key object=$graph}}
        
        <table class="main form">
          {{mb_include module=system template=inc_form_table_header object=$graph colspan=5}}
          
          <tr>
            <th>{{mb_label object=$graph field=title}}</th>
            <td>{{mb_field object=$graph field=title}}</td>
            <th>{{mb_label object=$graph field=disabled}}</th>
            <td>{{mb_field object=$graph field=disabled}}</td>
            <td>
              <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
              
              {{if $graph->_id}}
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'', objName:'{{$graph->_view|smarty:nodefaults|JSAttribute}}'})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
      
      {{if $graph->_id}}
      <table class="main tbl">
        <tr>
          <th class="title" colspan="2">
            {{tr}}CSupervisionGraph-back-axes{{/tr}}
          </th>
        </tr>
      </table>
      {{/if}}
      
      <table class="main layout" style="height: 240px;">
        <tr>
          <td id="supervision-graph-axes-list" style="width: 40%;"></td>
          <td id="supervision-graph-axis-editor">&nbsp;</td>
        </tr>
      </table>
      <hr />
      <div id="supervision-graph-preview" class="supervision"></div>
    </td>
  </tr>
</table>
