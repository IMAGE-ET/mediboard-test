{{assign var=patient value=$blood_salvage->_ref_operation->_ref_sejour->_ref_patient}}
<table class="tbl">
	<tr>
	  <th class="title text" colspan="2">
	    <button class="hslip notext" id="listRSPO-trigger" type="button" style="float:left">
	      {{tr}}Programme{{/tr}}
	    </button>
	    <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
	      <img src="images/icons/edit.png" alt="modifier" />
	    </a>
	    {{$patient->_view}}
	    ({{$patient->_age}} ans
	    {{if $patient->_age != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
	  </th>
	</tr>
</table>
<div id="timing">
  {{include file="inc_vw_bs_sspi_timing.tpl"}}
</div>
<div id="totaltime">
  {{include file="inc_total_time.tpl"}}
</div>
<div id="cell-saver-infos">
  {{include file="inc_vw_cell_saver_volumes.tpl}}
</div>
<div id="materiel">
  {{include file=inc_vw_blood_salvage_sspi_materiel.tpl}}
</div> 
  <!-- CR et FSEI -->
  <div id="incident">
    {{include file=inc_blood_salvage_cr_fsei.tpl}}
  </div>
  <div id="listNurse">
  {{include file=inc_vw_blood_salvage_personnel.tpl}}
</div>