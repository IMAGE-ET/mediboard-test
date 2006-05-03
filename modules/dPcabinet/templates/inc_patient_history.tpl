<script language="JavaScript" type="text/javascript">
{literal}

function incPatientHistoryMain() {
  initEffectClass("groupconsultations"   , "triggerconsultations"   );
  initEffectClass("grouphospitalisations", "triggerhospitalisations");
  initEffectClass("groupoperations"      , "triggeroperations");
}

{/literal}  
</script>
  

<table class="form">
  <tr class="triggerShow" id="triggeroperations" onclick="flipEffectElement('groupoperations', 'SlideDown', 'SlideUp', 'triggeroperations')">
    <td colspan="2">Interventions ({$patient->_ref_operations|@count})</td>
  </tr>
  <tbody id="groupoperations" style="display:none">
    {foreach from=$patient->_ref_operations item=curr_op}
    <tr>
      <td>
        <a href="index.php?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={$curr_op->operation_id}">
          {$curr_op->_ref_plageop->date|date_format:"%d %b %Y"}
        </a>
      </td>
      <td>Dr. {$curr_op->_ref_chir->_view}</td>
    </tr>
    {foreachelse}
    <tr>
      <td colspan="2"><em>Aucune opération disponible</em></td>
    </tr>
    {/foreach}
  </tbody>
  <tr class="triggerShow" id="triggerhospitalisations" onclick="flipEffectElement('grouphospitalisations', 'SlideDown', 'SlideUp', 'triggerhospitalisations')">
    <td colspan="2">Hospitalisations ({$patient->_ref_hospitalisations|@count})</td>
  </tr>
  <tbody id="grouphospitalisations" style="display:none">
    {foreach from=$patient->_ref_hospitalisations item=curr_op}
    <tr>
      <td>
        <a href="index.php?m=dPplanningOp&amp;tab=vw_edit_hospi&amp;hospitalisation_id={$curr_op->operation_id}">
          {$curr_op->_ref_plageop->date|date_format:"%d %b %Y"}
       </a>
      </td>
      <td>Dr. {$curr_op->_ref_chir->_view}</td>
    </tr>
    {foreachelse}
    <tr>
      <td colspan="2"><em>Aucune hospitalisation disponible</em></td>
    </tr>
    {/foreach}
  </tbody>
  <tr class="triggerShow" id="triggerconsultations" onclick="flipEffectElement('groupconsultations', 'SlideDown', 'SlideUp', 'triggerconsultations')">
    <td colspan="2">Consultations ({$patient->_ref_consultations|@count})</td>
  </tr>
  <tbody id="groupconsultations" style="display:none">
    {foreach from=$patient->_ref_consultations item=curr_consult}
    <tr>
      <td>
        <a href="index.php?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={$curr_consult->consultation_id}">
          {$curr_consult->_ref_plageconsult->date|date_format:"%d %b %Y"}
        </a>
      </td>
      <td>Dr. {$curr_consult->_ref_plageconsult->_ref_chir->_view}</td>
    </tr>
    {foreachelse}
    <tr>
      <td colspan="2"><em>Aucune consultation disponible</em></td>
    </tr>
    {/foreach}
  </tbody>
</table>
