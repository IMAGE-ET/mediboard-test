<!-- $Id$ -->

{{mb_include_script module="dPcompteRendu" script="document"}}
{{mb_include_script module="dPpatients" script="patient" ajax=true}}

<script type="text/javascript">

Document.refreshList = function() {
  new Url("dPpatients", "httpreq_vw_patient").
    addParam("patient_id", document.actionPat.patient_id.value).
    requestUpdate('vwPatient');
}

</script>

{{if $vip}}
<div class="big-warning">
  Vous n'avez pas accès à l'identité de ce patient.
  Veuillez contacter un administrateur de la clinique
  pour avoir plus d'information sur ce problème.
</div>
{{else}}

{{mb_include module=dPpatients template=inc_vw_identite_patient}}

<table class="form">
  <tr>
    <th class="category" colspan="4">Planifier</th>
  </tr>
  <tr>
    <td class="button" colspan="10">
	    {{if !$app->user_prefs.simpleCabinet}}
		    {{if $canPlanningOp->edit}}
		      <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$patient->_id}}&amp;operation_id=0&amp;sejour_id=0">
		        {{tr}}COperation{{/tr}}
		      </a>
		    {{/if}}
		    {{if $canPlanningOp->read}}
		      <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;pat_id={{$patient->_id}}&amp;operation_id=0&amp;sejour_id=0">
		        Interv. hors plage
		      </a>
		      <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;patient_id={{$patient->_id}}&amp;sejour_id=0">
            {{tr}}CSejour{{/tr}}
		      </a>
		    {{/if}}
	    {{/if}}
	
	    {{if $canCabinet->read}}
		    <a class="button new" href="?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->_id}}&amp;consultation_id=0">
		      {{tr}}CConsultation{{/tr}}
		    </a>
	    {{/if}}
    </td>
  </tr>
  {{if $listPrat|@count && $canCabinet->edit}}
  <tr><th class="category" colspan="4">Consultation immédiate</th></tr>
  <tr>
    <td class="button" colspan="4">
      <form name="addConsFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="dosql" value="do_consult_now" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="patient_id" class="notNull ref" value="{{$patient->_id}}" />

      <label for="prat_id" class="checkNull" title="Praticien pour la consultation immédiate. Obligatoire">Praticien</label>

      <select name="prat_id" class="notNull ref">
        <option value="">&mdash; Choisir un praticien</option>
        {{foreach from=$listPrat item=curr_prat}}
          <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" 
            {{if $curr_prat->user_id == $app->user_id}} selected="selected" {{/if}}>
            {{$curr_prat->_view}}
          </option>
        {{/foreach}}
      </select>

      <button class="new" type="submit">Consulter</button>

      </form>
    </td>
  </tr>
  {{/if}}
</table>

<table class="form">
  {{assign var="affectation" value=$patient->_ref_curr_affectation}}
  {{if $affectation && $affectation->affectation_id}}
  <tr>
  	<th colspan="3" class="category">Chambre actuelle</th>
  </tr>
  <tr>
    <td colspan="3">
      {{$affectation->_ref_lit}}
      depuis le {{mb_value object=$affectation field=entree}}
    </td>
  </tr>
  {{assign var="affectation" value=$patient->_ref_next_affectation}}
  {{elseif $affectation && $affectation->affectation_id}}
  <tr>
    <th colspan="3" class="category">Prochaine chambre</th>
  </tr>
  <tr>
    <td colspan="3">
      {{$affectation->_ref_lit}}
      depuis le {{mb_value object=$affectation field=entree}}
    </td>
  </tr>
  {{/if}}

  {{if $patient->_ref_sejours}}
  <tr>
    <th colspan="2" class="category">Séjours</th>
  </tr>
  {{foreach from=$patient->_ref_sejours item=object}}
    {{mb_include module=dPpatients template=inc_vw_elem_dossier}}
  {{/foreach}}
  {{/if}}
  
  {{if $patient->_ref_consultations}}
  <tr>
    <th colspan="2" class="category">Consultations</th>
  </tr>
  {{foreach from=$patient->_ref_consultations item=object}}
    {{mb_include module=dPpatients template=inc_vw_elem_dossier}}
  {{/foreach}}
  {{/if}}
</table>
{{/if}}