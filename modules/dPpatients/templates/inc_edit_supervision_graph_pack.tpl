<script type="text/javascript">
  Main.add(function(){
    var item = $("list-{{$pack->_guid}}");
    if (item) {
      item.addUniqueClassName("selected");
    }

    SupervisionGraph.listGraphToPack({{$pack->_id}});
  });
</script>

<form name="edit-supervision-graph-pack" method="post" action="?m=dPpatients" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="@class" value="CSupervisionGraphPack" />
  <input type="hidden" name="owner_class" value="CGroups" />
  <input type="hidden" name="owner_id" value="{{$g}}" />
  <input type="hidden" name="callback" value="SupervisionGraph.editPack" />
  {{mb_key object=$pack}}

  <table class="main form">
  {{mb_include module=system template=inc_form_table_header object=$pack colspan=5}}

    <tr>
      <th>{{mb_label object=$pack field=title}}</th>
      <td>{{mb_field object=$pack field=title}}</td>

      <th>{{mb_label object=$pack field=disabled}}</th>
      <td>{{mb_field object=$pack field=disabled typeEnum=checkbox}}</td>

      <td>
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>

      {{if $pack->_id}}
        <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'', objName:'{{$pack->_view|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
      {{/if}}
      </td>
    </tr>
  </table>
</form>

<div id="graph-to-pack-list"></div>