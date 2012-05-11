<tr>
  <td class="not-printable">
    <form name="Edit-{{$_sortie->_guid}}" action="?m={{$m}}" method="post"
      onsubmit="return onSubmitFormAjax(this, { onComplete: function() { refreshList(null, null, 'deplacements');} })">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      {{mb_key object=$_sortie}}
      {{mb_field object=$_sortie field=sortie hidden=1}}
    
      {{if $_sortie->effectue}}
        <input type="hidden" name="effectue" value="0" />
        <button type="submit" class="cancel"> Annuler </button>
      {{else}}
        <input type="hidden" name="effectue" value="1" />
        <button type="submit" class="tick"> Effectuer </button>
      {{/if}}
    </form>
  </td>
  {{if $_sortie->effectue}}
    <td class="text" class="arretee">
  {{else}}
    <td class="text">
  {{/if}}
 
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
    {{$_sortie->_ref_lit}}
  </td>
  
  <td class="text">
    {{$_sortie->_ref_next->_ref_lit}}
  </td>
  
  <td>
    {{$_sortie->sortie|date_format:$conf.time}}
  </td>
</tr>