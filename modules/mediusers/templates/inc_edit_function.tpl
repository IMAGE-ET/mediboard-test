<script type="text/javascript">
  Main.add(function () {
    InseeFields.initCPVille("editFrm", "cp", "ville", "tel");
    changePagePrimaryUsers();
  });

  changePagePrimaryUsers = function(page) {
    var url = new Url("mediusers", "ajax_list_mediusers");
    url.addParam("function_id", '{{$function->_id}}');
    url.addParam("page_function", page);
    url.requestUpdate("listUsers");
  }
</script>

<form name="editFrm" action="?m={{$m}}" method="post" onSubmit="return onSubmitFormAjax(this)">
  {{if !$can->edit}}
  <input name="_locked" value="1" hidden="hidden" />
  {{/if}}
  <input type="hidden" name="m" value="mediusers" />
  <input type="hidden" name="dosql" value="do_functions_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="showFunction" />
  {{mb_key object=$function}}

  <table class="form">
    <tr>
      {{if $function->_id}}
      <th class="title modify text" colspan="2">
        {{mb_include module=system template=inc_object_notes      object=$function}}
        {{mb_include module=system template=inc_object_idsante400 object=$function}}
        {{mb_include module=system template=inc_object_history    object=$function}}
        {{mb_include module=system template=inc_object_uf         object=$function}}
        {{tr}}CFunctions-title-modify{{/tr}} '{{$function}}'  
      </th>
      {{else}}
      <th class="title" colspan="2">
        {{tr}}CFunctions-title-create{{/tr}}
      </th>
      {{/if}}
    </tr>
    <tr>
      <th>{{mb_label object=$function field="text"}}</th>
      <td>{{mb_field object=$function field="text"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="soustitre"}}</th>
      <td>{{mb_field object=$function field="soustitre"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="group_id"}}</th>
      <td>{{mb_field object=$function field="group_id" options=$groups}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="type"}}</th>
      <td>{{mb_field object=$function field="type"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="color"}}</th>
      <td>
        {{mb_field object=$function field="color" form="editFrm"}}
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$function field=initials}}</th>
      <td>{{mb_field object=$function field=initials}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$function field="adresse"}}</th>
      <td>{{mb_field object=$function field="adresse"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="cp"}}</th>
      <td>{{mb_field object=$function field="cp"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="ville"}}</th>
      <td>{{mb_field object=$function field="ville"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="tel"}}</th>
      <td>{{mb_field object=$function field="tel"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="fax"}}</th>
      <td>{{mb_field object=$function field="fax"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="quotas"}}</th>
      <td>{{mb_field object=$function field="quotas"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="actif"}}</th>
      <td>{{mb_field object=$function field="actif"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="compta_partagee"}}</th>
      <td>{{mb_field object=$function field="compta_partagee"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="consults_partagees"}}</th>
      <td>{{mb_field object=$function field="consults_partagees"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="admission_auto"}}</th>
      <td>{{mb_field object=$function field="admission_auto"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$function field="facturable"}}</th>
      <td>{{mb_field object=$function field="facturable"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
      {{if $function->function_id}}
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la fonction',objName:'{{$function->text|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
      {{else}}
        <button class="submit" name="btnFuseAction" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
      </td>
    </tr>
  </table>
</form>

<div id="listUsers"></div>
