<!-- Haut de page, informations patient et opération (idem Salle d'op) -->
<table class="form">
  {{assign var=patient value=$selOp->_ref_sejour->_ref_patient}}
  <tr>
    <th class="title text" colspan="2">
      <button class="hslip notext" id="listplages-trigger" type="button" style="float:left">
        {{tr}}Programme{{/tr}}
      </button>
      <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
        <img src="images/icons/edit.png" alt="modifier" />
 			</a>
      {{$patient->_view}}
      ({{$patient->_age}} ans
      {{if $patient->_age != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
      &mdash; Dr {{$selOp->_ref_chir->_view}}
      <br />
      {{if $selOp->libelle}}{{$selOp->libelle}} &mdash;{{/if}}
      {{mb_label object=$selOp field=cote}} : {{mb_value object=$selOp field=cote}}
      &mdash; {{mb_label object=$selOp field=temp_operation}} : {{mb_value object=$selOp field=temp_operation}}
    </th>
  </tr>
</table>
{{if $blood_salvage->_id }}
  <!-- Informations sur le patient (Groupe, rhésus, ASA, RAI...) -->
	<div id="info-patient">
		{{include file="inc_vw_patient_infos.tpl"}}
	</div>
	<!-- Informations sur l'opération de récupération. Volumes récupérés etc -->
	<div id="cell-saver-infos">
		{{include file="inc_vw_cell_saver_infos.tpl}}
	</div>
	<div id="start-timing">
		{{include file=inc_vw_recuperation_start_timing.tpl}}
	</div>
	
	<div id="materiel">
   {{include file=inc_blood_salvage_conso.tpl}}
  </div>
  
{{else}}
	<div class="big-info">
		Aucun Cell Saver n'est prévu pour cette opération.
	</div>
	<form name="inscriptionRSPO" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
	<input type="hidden" name="operation_id" value="{{$selOp->_id}}">
	<input type="hidden" name="m" value="bloodSalvage" />
  <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
  <button type=submit" class="submit">Inscrire le patient au protocole RSPO</button>
  </form>

{{/if}}