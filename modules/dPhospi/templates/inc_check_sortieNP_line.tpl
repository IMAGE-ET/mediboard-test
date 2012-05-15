{{assign var=sejour value=$_sortie}}
{{assign var=patient value=$sejour->_ref_patient}}

<tr>
  {{if $show_duree_preop && $type_mouvement != "sorties"}}
    <td>
      {{mb_value object=$sejour->_ref_curr_operation field=_heure_us}}
    </td>
  {{/if}}
  <td class="text">
   {{if $canPlanningOp->read}}
    <div style="float: right">
      {{if $isImedsInstalled}}
        {{mb_include module=Imeds template=inc_sejour_labo link="#1" float="none"}}
      {{/if}}
        
      <a class="action" style="display: inline" title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
        <img src="images/icons/planning.png" alt="modifier" />
      </a>
    </div>
    {{/if}}
    
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
  {{if "dmi"|module_active}}
    <td class="button">
      {{foreach from=$sejour->_ref_operations item=_interv}}
        {{mb_include module=dmi template=inc_dmi_alert interv=$_interv}}
      {{/foreach}}
    </td>
  {{/if}}
  <td style="text-align: center;">
    -
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
    <div {{if !$sejour->sortie_reelle}}class="only-printable"{{/if}}>
      {{if $type == 'presents'}}
        {{$sejour->sortie|date_format:$conf.datetime}}
      {{else}}
        {{$sejour->sortie|date_format:$conf.time}}
      {{/if}}
    </div>
    {{if !$sejour->sortie_reelle}}
      <div class="not-printable">
      {{assign var=sejour_guid value=$sejour->_guid}}
      <form name="editSortiePrevue-{{$type}}-{{$sejour_guid}}" method="post" action="?"
        onsubmit="return onSubmitFormAjax(this, { onComplete: function() { refreshList(null, null, '{{$type}}', '{{$type_mouvement}}');} })">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$sejour}}
        {{mb_field object=$sejour field=entree_prevue hidden=true}}
        <button class="add" type="button" onclick="addDays(this, 1)">1J</button>
        {{mb_field object=$sejour field=sortie_prevue register=true form="editSortiePrevue-`$type`-`$sejour_guid`" onchange="this.form.onsubmit()"}}
        {{mb_field object=$sejour field="mode_sortie" onchange="this.form.onsubmit()"}}
        <br />
        <div id="listEtabExterne-editFrm{{$sejour->_guid}}" {{if $sejour->mode_sortie != "transfert"}} style="display: none;" {{/if}}>
          {{mb_field object=$sejour field="etablissement_sortie_id" form="editSortiePrevue-`$type`-`$sejour_guid`" 
            autocomplete="true,1,50,true,true" onchange="this.form.onsubmit()"}}
        </div>
      </form>
      </div>
    {{/if}}
  </td>
</tr>