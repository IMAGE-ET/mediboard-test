
<script type="text/javascript">
Main.add(function () {
  InseeFields.initCPVille("group", "cp", "ville", "tel");
});
</script>


<form name="group" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

{{mb_class object=$group}}
{{mb_key   object=$group}}

<table class="form">
  {{mb_include module=system template=inc_form_table_header object=$group}}

  <tr>
    <th>{{mb_label object=$group field="text"}}</th>
    <td>{{mb_field object=$group field="text"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="raison_sociale"}}</th>
    <td>{{mb_field object=$group field="raison_sociale"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="adresse"}}</th>
    <td>{{mb_field object=$group field="adresse"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="cp"}}</th>
    <td>{{mb_field object=$group field="cp"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="ville"}}</th>
    <td>{{mb_field object=$group field="ville"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="tel"}}</th>
    <td>{{mb_field object=$group field="tel"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="fax"}}</th>
    <td>{{mb_field object=$group field="fax"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="tel_anesth"}}</th>
    <td>{{mb_field object=$group field="tel_anesth"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="mail"}}</th>
    <td>{{mb_field object=$group field="mail"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="mail_apicrypt"}}</th>
    <td>{{mb_field object=$group field="mail_apicrypt"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="web"}}</th>
    <td>{{mb_field object=$group field="web" size="35"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="directeur"}}</th>
    <td>{{mb_field object=$group field="directeur"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="domiciliation"}}</th>
    <td>{{mb_field object=$group field="domiciliation"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="siret"}}</th>
    <td>{{mb_field object=$group field="siret"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="finess"}}</th>
    <td>{{mb_field object=$group field="finess"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$group field="ape"}}</th>
    <td>{{mb_field object=$group field="ape"}}</td>
  </tr>

  {{if $group->_id}}
  <tr>
    <th>{{mb_label object=$group field="service_urgences_id"}}</th>
    <td>
      <select name="service_urgences_id">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_function list=$group->_ref_functions selected=$group->service_urgences_id}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$group field="pharmacie_id"}}</th>
    <td>
      <select name="pharmacie_id">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_function list=$group->_ref_functions selected=$group->pharmacie_id}}
      </select>
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <th>{{mb_label object=$group field="chambre_particuliere"}}</th>
    <td>{{mb_field object=$group field="chambre_particuliere"}}</td>
  </tr>
  
  {{if $conf.ref_pays == 2}}
    <tr>
      <th>{{mb_label object=$group field="ean"}}</th>
      <td>{{mb_field object=$group field="ean"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$group field="rcc"}}</th>
      <td>{{mb_field object=$group field="rcc"}}</td>
    </tr>
  {{/if}}
  
  <tr>
    <td class="button" colspan="2">
    {{if $group->_id}}
      <button class="modify" type="submit" name="modify">
        {{tr}}Save{{/tr}}
      </button>
      <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'l\'établissement', objName: $V(this.form.text)})">
        {{tr}}Delete{{/tr}}
      </button>
    {{else}}
      <button class="new" type="submit" name="create">
        {{tr}}Create{{/tr}}
      </button>
    {{/if}}
    </td>
  </tr>
</table>
</form>