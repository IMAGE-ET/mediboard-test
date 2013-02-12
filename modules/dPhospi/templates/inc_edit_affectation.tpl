<form name="editAffect" method="post" action="?"
  onsubmit="return onSubmitFormAjax(this, {onComplete: function() {
    if($V(this._lock_all_lits)){
      refreshMouvements(Control.Modal.close, '{{$affectation->lit_id}}'); 
    }
    else{
      refreshMouvements(Control.Modal.close);
    }
    loadNonPlaces();
    }});">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_affectation_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$affectation}}
  {{mb_field object=$affectation field=lit_id hidden=true}}
  
  <table class="form">
    <tr>
      {{if $affectation->_id}}
        <th class="title" colspan="4">
          {{$affectation->_ref_sejour->_ref_patient}}
        </th>
      {{/if}}
    </tr>
    {{if !$affectation->_id}}
      {{if $urgence}}
      <input type="hidden" name="function_id" value="{{$affectation->function_id}}"/>
        <tr>
          <th><input type="checkbox" name="_lock_all_lits_urgences" value="1"/></th>
          <td colspan="3">Mise à disposition de  tous les lits du service {{$lit->_ref_chambre->_ref_service->nom}} pour les urgences</td>
        </tr>
      {{else}}
        <tr>
          <th><input type="checkbox" name="_lock_all_lits" value="1"/></th>
          <td colspan="3">Bloquer tous les lits du service {{$lit->_ref_chambre->_ref_service->nom}}</td>
        </tr>
      {{/if}}
    {{/if}}
    <tr>
      <th>
        {{mb_label object=$affectation field=entree}}
      </th>
      <td>
        {{mb_field object=$affectation field=entree form=editAffect register=true}}
      </td>
      <th>
        {{mb_label object=$affectation field=sortie}}
      </th>
      <td>
        {{mb_field object=$affectation field=sortie form=editAffect register=true}}
      </td>
    </tr>
    <tr>
      <td colspan="4" class="button">
        <button type="button" class="save" onclick="this.form.onsubmit();">
          {{if $affectation->_id}}
            {{tr}}Save{{/tr}}
          {{else}}
            {{tr}}Create{{/tr}}
          {{/if}}
        {{if $affectation->_id}}
          <button type="button" class="cancel" onclick="$V(this.form.del, 1); this.form.onsubmit();">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
{{if $affectation->_id}}
  {{mb_include module=hospi template=inc_other_actions}}
{{/if}}