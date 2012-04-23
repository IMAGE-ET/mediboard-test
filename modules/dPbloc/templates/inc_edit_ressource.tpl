<script type="text/javascript">
  Main.add(function() {
    var form = getForm("editRessourceMaterielle");
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("object_class", "CTypeRessource");
    url.addParam("field", "_type_ressource_view");
    
    url.autoComplete(form.elements._type_ressource_view, null, {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected){
        $V(field.form.type_ressource_id, selected.getAttribute("id").split("-")[2]);
        if ($V(field.form.elements._type_ressource_view) == "") {
          $V(field.form.elements._type_ressource_view, selected.down('.view').innerHTML);
        }
      }
    });
  });
</script>
<form name="editRessourceMaterielle" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPbloc" />
  <input type="hidden" name="dosql" value="do_ressource_materielle_aed"/>
  <input type="hidden" name="callback" value="Ressource.afterEditRessource" />
  <input type="hidden" name="del" value="0" />
  
  {{mb_key object=$ressource_materielle}}
  {{mb_field object=$ressource_materielle field=group_id hidden=true}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$ressource_materielle}}
    <tr>
      <th>
        {{mb_label object=$ressource_materielle field=libelle}}
      </th>
      <td>
        {{mb_field object=$ressource_materielle field=libelle}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$ressource_materielle field=type_ressource_id}}
      </th>
      <td>
        {{mb_field object=$ressource_materielle field=type_ressource_id hidden=true}}
        <input type="text" name="_type_ressource_view" value="{{$ressource_materielle->_ref_type_ressource}}"/>
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$ressource_materielle field=deb_activite}}
      </th>
      <td>
        {{mb_field object=$ressource_materielle field=deb_activite form=editRessourceMaterielle register=true}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$ressource_materielle field=fin_activite}}
      </th>
      <td>
        {{mb_field object=$ressource_materielle field=fin_activite form=editRessourceMaterielle register=true}}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        {{if $ressource_materielle->_id}}
          <button type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form, {objName: 'ressource', ajax: true})">{{tr}}Delete{{/tr}}</button>
        {{else}}
          <button type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>