<tr>
  <td class="not-printable">
    {{assign var=_mouv value=$_sortie}}
    {{if $sens == "entrants"}}
      {{assign var=_mouv value=$_sortie->_ref_prev}}
    {{/if}}
    <form name="Edit-{{$_mouv->_guid}}" action="?m={{$m}}" method="post"
      onsubmit="return onSubmitFormAjax(this, { onComplete: function() { refreshList(null, null, 'deplacements');} })">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      {{mb_key object=$_mouv}}
      {{mb_field object=$_mouv field=sortie hidden=1}}
    
      {{if $_mouv->effectue}}
        <input type="hidden" name="effectue" value="0" />
        <button type="submit" class="cancel"> Annuler </button>
      {{else}}
        <input type="hidden" name="effectue" value="1" />
        <button type="submit" class="tick"> Effectuer </button>
      {{/if}}
    </form>
  </td>
  <td class="text">
  {{assign var=sejour value=$_sortie->_ref_sejour}}
  {{assign var=patient value=$sejour->_ref_patient}}
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')"
      {{if !$sejour->entree_reelle}} class="patient-not-arrived"{{/if}}>
      {{$patient}}
    </strong>
  </td>
  
  <td class="text">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
  </td>
  
  <td class="text">
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">{{$sejour->_motif_complet}}</strong>
  </td>
  
  <td class="text {{if $_mouv->effectue && $sens == "sortants"}}arretee{{/if}}">
    {{$_sortie->_ref_lit}}
  </td>
  
  
   <td class="text {{if $_mouv->effectue && $sens == "entrants"}}arretee{{/if}}">
    {{if $sens == "sortants"}}
      {{$_sortie->_ref_next->_ref_lit}}
    {{/if}}
    {{if $sens == "entrants"}}
      {{$_sortie->_ref_prev->_ref_lit}}
    {{/if}}
  </td>
  
  <td>
    {{$_mouv->sortie|date_format:$conf.time}}
  </td>
</tr>