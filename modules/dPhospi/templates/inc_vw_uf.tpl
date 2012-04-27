<!-- Formulaire d'une unité foncitonnelle -->
<form name="Edit-CUniteFonctionnelle" action="" method="post" onsubmit="return submit_Ajax(this, 'UF')">
<input type="hidden" name="m" value="hospi" />
<input type="hidden" name="dosql" value="do_uf_aed" />
<input type="hidden" name="del" value="0" />
{{mb_key object=$uf}}

<table class="form">
{{mb_include module=system template=inc_form_table_header object=$uf}}  
  
<tr>
  <th>{{mb_label object=$uf field=group_id}}</th>
  <td>{{mb_field object=$uf field=group_id options=$etablissements}}</td>
</tr>

<tr>
  <th>{{mb_label object=$uf field=code}}</th>
  <td>{{mb_field object=$uf field=code}}</td>
</tr>

<tr>
  <th>{{mb_label object=$uf field=libelle}}</th>
  <td>{{mb_field object=$uf field=libelle}}</td>
</tr>

<tr>
  <th>{{mb_label object=$uf field=description}}</th>
  <td>{{mb_field object=$uf field=description}}</td>
</tr>

<tr>
  <td class="button" colspan="2">
    {{if $uf->_id}}
    <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'lUF',objName: $V(this.form.libelle)})">
      {{tr}}Delete{{/tr}}
    </button>
    {{else}}
    <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
    {{/if}}
  </td>
</tr>

</table>   

</form>