{{mb_include_script module="dPpatients" script="autocomplete"}}
<script type="text/javascript">
  Main.add(function () {
    InseeFields.initCPVille("editFrm", "cp", "ville", "tel");
  });
</script>

<form name="editFrm" action="?m={{$m}}" method="post" onSubmit="return checkForm(this)">
  <input type="hidden" name="m" value="mediusers" />
  <input type="hidden" name="dosql" value="do_functions_aed" />
  <input type="hidden" name="function_id" value="{{$userfunction->function_id}}" />
  <input type="hidden" name="del" value="0" />
  <table class="form">
    <tr>
      {{if $userfunction->_id}}
      <th class="title modify text" colspan="2">
        {{mb_include module=system template=inc_object_idsante400 object=$userfunction}}
        {{mb_include module=system template=inc_object_history object=$userfunction}}
        
        {{tr}}CFunctions-title-modify{{/tr}} '{{$userfunction}}'
      </th>
      {{else}}
      <th class="title" colspan="2">
        {{tr}}CFunctions-title-create{{/tr}}
      </th>
      {{/if}}
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="text"}}</th>
      <td>{{mb_field object=$userfunction field="text"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="soustitre"}}</th>
      <td>{{mb_field object=$userfunction field="soustitre"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="compta_partagee"}}</th>
      <td>{{mb_field object=$userfunction field="compta_partagee"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="group_id"}}</th>
      <td>
        <select name="group_id" class="{{$userfunction->_props.group_id}}">
          <option value="">&mdash; {{tr}}CGroups.select{{/tr}}</option>
          {{foreach from=$groups item=_group}}
          <option value="{{$_group->group_id}}" {{if $_group->group_id == $userfunction->group_id}} selected="selected" {{/if}}>
            {{$_group->text}}
          </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="type"}}</th>
      <td>{{mb_field object=$userfunction field="type"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="color"}}</th>
      <td>
        <a href="#1" id="select_color" style="background: #{{$userfunction->color}}; padding: 0 3px; border: 1px solid #aaa;" onclick="ColorSelector.init()">Cliquer pour changer</a>
        {{mb_field object=$userfunction field="color" hidden=1}}
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="adresse"}}</th>
      <td>{{mb_field object=$userfunction field="adresse"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="cp"}}</th>
      <td>{{mb_field object=$userfunction field="cp"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="ville"}}</th>
      <td>{{mb_field object=$userfunction field="ville"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="tel"}}</th>
      <td>{{mb_field object=$userfunction field="tel"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="fax"}}</th>
      <td>{{mb_field object=$userfunction field="fax"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="actif"}}</th>
      <td>{{mb_field object=$userfunction field="actif"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$userfunction field="admission_auto"}}</th>
      <td>{{mb_field object=$userfunction field="admission_auto"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
      {{if $userfunction->function_id}}
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la fonction',objName:'{{$userfunction->text|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
      {{else}}
        <button class="submit" name="btnFuseAction" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
      </td>
    </tr>
  </table>
</form>

{{include file="inc_prim_secon_users.tpl"}}