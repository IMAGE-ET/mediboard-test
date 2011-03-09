{{assign var="chir_id" value=$selOp->_ref_chir->_id}}
{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="module" value="dPpsmi"}}
{{assign var="object" value=$selOp}}
{{assign var="sejour" value=$selOp->_ref_sejour}}
{{assign var="patient" value=$sejour->_ref_patient}}
{{mb_include module=dPsalleOp template=js_codage_ccam}}
<script type="text/javascript">
Main.add (function () {
  Control.Tabs.create('tab-actes', false);
} );
</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="2">
      <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
        <img src="images/icons/edit.png" />
      </a>
      
      {{$patient->_view}}
      ({{$patient->_age}} ans
      {{if $patient->_age != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
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
    <td colspan="2">
      <ul id="tab-actes" class="control_tabs">
        <li><a href="#one">CCAM</a></li>
        <li><a href="#two">NGAP</a></li>
      </ul>
    </td>
  </tr>       
  <tr id="one" style="display: none;">
    <th>Actes<br /><br />
      {{tr}}{{$selOp->_class_name}}{{/tr}}
      {{if ($module=="dPplanningOp") || ($module=="dPsalleOp")}}
      <br />
      Côté {{tr}}COperation.cote.{{$selOp->cote}}{{/tr}}
      <br />
      ({{$selOp->temp_operation|date_format:$conf.time}})
      {{/if}}
    </th>
    <td>
      <div id="ccam">
        {{assign var="module" value="dPpmsi"}}
        {{assign var="subject" value=$selOp}}
		    {{mb_include module=dPsalleOp template=inc_codage_ccam}}
      </div>
    </td> 
  </tr>
  <tr id="two" style="display: none;">
    <th class="category" style="vertical-align: middle">
      Actes <br />NGAP
    </th>
    <td id="listActesNGAP">
      {{assign var="object" value=$selOp}}
	    {{mb_include module=dPcabinet template=inc_codage_ngap}}
    </td>
  </tr>
</table>
