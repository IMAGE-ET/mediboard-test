{{mb_default var=incident value=0}}

<form name="addAnesthPerop-{{$incident}}" action="?" method="post" 
              onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ refreshAnesthPerops('{{$selOp->_id}}'); $V(this.libelle, '');}.bind(this)  } )">
  
  <input type="hidden" name="m" value="dPsalleOp" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_anesth_perop_aed" />
  <input type="hidden" name="operation_id" value="{{$selOp->_id}}" />
  <input type="hidden" name="datetime" value="now" />
  {{mb_key object=$anesth_perop}}
  
  {{if $incident == 1}}
    <input type="hidden" name="incident" value="1" />
  {{/if}}
  <table class="main layout">
    <tr>  
      <td>
        {{if $selOp->_ref_anesth->_id}}
          {{assign var=contextUserId value=$selOp->_ref_anesth->_id}}
          {{assign var=contextUserView value=$selOp->_ref_anesth->_view|smarty:nodefaults:JSAttribute}}
        {{else}}
          {{assign var=contextUserId value=$app->_ref_user->_id}}
          {{assign var=contextUserView value=$app->_ref_user->_view|smarty:nodefaults:JSAttribute}}
        {{/if}}
        {{mb_field object=$anesth_perop field="libelle" form="addAnesthPerop-$incident"
          aidesaisie="contextUserId: '$contextUserId', contextUserView: '$contextUserView'"}}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="submit" class="submit">Ajouter</button>
      </td>
    </tr>
  </table>
</form>