{{assign var=patient value=$selOp->_ref_sejour->_ref_patient}}
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