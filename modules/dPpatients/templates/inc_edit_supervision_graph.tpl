<script type="text/javascript">
Main.add(function(){
  {{if $graph->_id}}
    SupervisionGraph.listAxes({{$graph->_id}});
  {{/if}}

  var item = $("list-{{$graph->_guid}}");
  if (item) {
    item.addUniqueClassName("selected");
  }
});
</script>

<form name="edit-supervision-graph" method="post" action="?m=dPpatients" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="@class" value="CSupervisionGraph" />
  <input type="hidden" name="owner_class" value="CGroups" />
  <input type="hidden" name="owner_id" value="{{$g}}" />
  <input type="hidden" name="callback" value="SupervisionGraph.editGraph" />
  {{mb_key object=$graph}}

  <table class="main form">
  {{mb_include module=system template=inc_form_table_header object=$graph colspan=7}}

    <tr>
      <th>{{mb_label object=$graph field=title}}</th>
      <td>{{mb_field object=$graph field=title}}</td>

      <th>{{mb_label object=$graph field=disabled}}</th>
      <td>{{mb_field object=$graph field=disabled typeEnum=checkbox}}</td>

      <th>{{mb_label object=$graph field=height}}</th>
      <td>{{mb_field object=$graph field=height form="edit-supervision-graph" increment=true}}</td>

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