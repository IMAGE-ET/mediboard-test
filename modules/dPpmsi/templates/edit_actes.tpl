{{assign var="chir_id" value=$selOp->_ref_chir->_id}}
{{assign var="sejour" value=$selOp->_ref_sejour}}
{{assign var="patient" value=$sejour->_ref_patient}}

<table class="tbl">
  <tr>
    <th class="title">
      <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
        <img src="images/icons/edit.png" />
      </a>
      
      {{$patient->_view}}
      ({{$patient->_age}}
      {{if $patient->_annees != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
      &mdash; Dr {{$selOp->_ref_chir->_view}}
      <br />
      
      {{if $selOp->libelle}}{{$selOp->libelle}} &mdash;{{/if}}
      {{mb_label object=$selOp field=cote}} : {{mb_value object=$selOp field=cote}}
      &mdash; {{mb_label object=$selOp field=temp_operation}} : {{mb_value object=$selOp field=temp_operation}}
      <br />
      
      {{tr}}CSejour{{/tr}}
      du {{mb_value object=$sejour field=entree}}
      au {{mb_value object=$sejour field=sortie_prevue}}
    </th>
  </tr>
  
  <tr>
    <td id="codage_actes">
      <!-- codage des acte ccam et ngap -->
      {{mb_include module=salleOp template="inc_codage_actes" subject=$selOp}}
    </td>
  </tr>    
</table>
