
<form name="edit-mode-{{$mode->_class}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this, function(){document.location.reload()})">
  <input type="hidden" name="m" value="planningOp" />
  {{mb_class object=$mode}}
  {{mb_key object=$mode}}
  {{mb_field object=$mode field=group_id hidden=true}}

  <table class="main form">
    {{mb_include module=system template=inc_form_table_header object=$mode}}

    <tr>
      <th>{{mb_label object=$mode field=code}}</th>
      <td>{{mb_field object=$mode field=code}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$mode field=libelle}}</th>
      <td>{{mb_field object=$mode field=libelle}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$mode field=mode}}</th>
      <td>{{mb_field object=$mode field=mode}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$mode field=actif}}</th>
      <td>{{mb_field object=$mode field=actif}}</td>
    </tr>

    <tr>
      <td colspan="2" class="button">
        {{if $mode->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax: true, typeName:'', objName:'{{$mode->_view|smarty:nodefaults|JSAttribute}}'},function(){document.location.reload()})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
