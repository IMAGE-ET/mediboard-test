<form name="edit-evenement-{{$evenement->_guid}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: function(){ Control.Modal.close(); reloadSurveillancePerop(); }})">
  <input type="hidden" name="m" value="dPsalleOp" />
  {{mb_class object=$evenement}}
  {{mb_key object=$evenement}}
  {{mb_field object=$evenement field=operation_id hidden=true}}
  
  <table class="main form">
    {{mb_include module=system template=inc_form_table_header object=$evenement}}
    
    {{if $evenement instanceof CAnesthPerop}}
      <tr>
        <th>{{mb_label object=$evenement field=datetime}}</th>
        <td>{{mb_field object=$evenement field=datetime form="edit-evenement-`$evenement->_guid`" register=true}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$evenement field=libelle}}</th>
        <td>
          {{if $operation->_ref_anesth->_id}}
            {{assign var=contextUserId value=$operation->_ref_anesth->_id}}
            {{assign var=contextUserView value=$operation->_ref_anesth->_view|smarty:nodefaults:JSAttribute}}
          {{else}}
            {{assign var=contextUserId value=$app->_ref_user->_id}}
            {{assign var=contextUserView value=$app->_ref_user->_view|smarty:nodefaults:JSAttribute}}
          {{/if}}

          {{mb_field object=$evenement field=libelle form="edit-evenement-`$evenement->_guid`"
                     aidesaisie="contextUserId: '$contextUserId', contextUserView: '$contextUserView'"}}
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$evenement field=incident}}</th>
        <td>{{mb_field object=$evenement field=incident typeEnum=radio}}</td>
      </tr>
      <tr>
        <td></td>
        <td>
          <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
        </td>
      </tr>
    {{/if}}
  </table>
</form>