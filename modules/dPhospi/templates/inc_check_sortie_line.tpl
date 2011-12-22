<tr>
  <td class="not-printable">
    <form name="Sortie-{{$_sortie->_guid}}" action="?m={{$m}}" method="post"
      onsubmit="return onSubmitFormAjax(this, {onComplete: function() { refreshList(null, null, '{{$type}}'); } })">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      {{mb_key object=$_sortie}}
      {{if $_sortie->confirme}}
        <input type="hidden" name="confirme" value="0" />
        <button type="submit" class="cancel">
          Annuler
        </button>
      {{else}}
        <input type="hidden" name="confirme" value="1" />
        <button type="submit" class="tick">
          Autoriser
        </button>
      {{/if}}
    </form>
  </td>
  
  <td class="text {{if $_sortie->confirme}}arretee{{/if}}">
   {{assign var=sejour value=$_sortie->_ref_sejour}}
   {{if $canPlanningOp->read}}
   <a class="action" style="float: right"  title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
     <img src="images/icons/planning.png" alt="modifier" />
   </a>
   {{/if}}
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
  <td class="text {{if $sejour->sortie_reelle}}effectue{{/if}}">
    {{$_sortie->_ref_lit}}
  </td>
  <td class="narrow">
    {{$sejour->entree|date_format:$conf.datetime}}
    <div style="position: relative;">
    <div class="sejour-bar" title="arrivée il y a {{$sejour->_entree_relative}}j et départ prévu dans {{$sejour->_sortie_relative}}j ">
      <div style="width: {{if $sejour->_duree}}{{math equation='100*(-entree / (duree))' entree=$sejour->_entree_relative duree=$sejour->_duree format='%.2f'}}{{else}}100{{/if}}%;"></div>
    </div>
    </div>
  </td>
  <td class="narrow">
    <div {{if !$_sortie->confirme && !$sejour->sortie_reelle}}class="only-printable"{{/if}}>
      {{if $type == 'presents'}}
        {{$_sortie->sortie|date_format:$conf.datetime}}
      {{else}}
        {{$_sortie->sortie|date_format:$conf.time}}
      {{/if}}
      </div>
    {{if !$_sortie->confirme && !$sejour->sortie_reelle}}
      <div class="not-printable">
      {{assign var=aff_guid value=$_sortie->_guid}}
      <form name="editSortiePrevue-{{$type}}-{{$aff_guid}}" method="post" action="?"
        onsubmit="return onSubmitFormAjax(this, { onComplete: function() { refreshList(null, null, '{{$type}}', {{$type_mouvement}}); } })">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$sejour}}
        {{mb_field object=$sejour field=entree_prevue hidden=true}}
        <button class="add" type="button" onclick="addDays(this, 1)">1J</button>
        {{mb_field object=$sejour field=sortie_prevue register=true form="editSortiePrevue-$type-$aff_guid" onchange="this.form.onsubmit()"}}
      </form>
      </div>
    {{/if}}
  </td>
</tr>