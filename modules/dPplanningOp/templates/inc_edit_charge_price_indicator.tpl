
<form name="edit-cpi" method="post" action="?" onsubmit="return onSubmitFormAjax(this, function(){document.location.reload()})">
  <input type="hidden" name="m" value="planningOp" />
  {{mb_class object=$charge}}
  {{mb_key object=$charge}}
  {{mb_field object=$charge field=group_id hidden=true}}
  
  <table class="main form">
    {{mb_include module=system template=inc_form_table_header object=$charge}}
    
    <tr>
      <th>{{mb_label object=$charge field=code}}</th>
      <td>{{mb_field object=$charge field=code}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$charge field=libelle}}</th>
      <td>{{mb_field object=$charge field=libelle}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$charge field=type}}</th>
      <td>{{mb_field object=$charge field=type}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$charge field=type_pec}}</th>
      <td>{{mb_field object=$charge field=type_pec emptyLabel="Tous"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$charge field=actif}}</th>
      <td>{{mb_field object=$charge field=actif}}</td>
    </tr>
    
    <tr>
      <td colspan="2" class="button">
        {{if $charge->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax: true, typeName:'', objName:'{{$charge->_view|smarty:nodefaults|JSAttribute}}'},function(){document.location.reload()})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
