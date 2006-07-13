<script language="JavaScript" type="text/javascript">

function incPatientHistoryMain() {
  initEffectClass("groupoperations"   , "triggeroperations"      );
  initEffectClass("groupconsultations", "triggerconsultations"   );
}

</script>
  

<table class="form">
  <tr class="triggerShow" id="triggeroperations" onclick="flipEffectElement('groupoperations', 'SlideDown', 'SlideUp', 'triggeroperations')">
    <td>Hospitalisations ({{$patient->_ref_sejours|@count}})</td>
  </tr>
  <tbody id="groupoperations" style="display:none">
    {{foreach from=$patient->_ref_sejours item=curr_sejour}}
    <tr>
      <td>
        <strong>Dr. {{$curr_sejour->_ref_praticien->_view}}</strong>
        Du {{$curr_sejour->entree_prevue|date_format:"%d %b %Y"}}
        au {{$curr_sejour->sortie_prevue|date_format:"%d %b %Y"}}
        <ul>
        {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
          <li>
            <a href="index.php?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
              <strong>Dr. {{$curr_op->_ref_chir->_view}}</strong>
              le {{$curr_op->_ref_plageop->date|date_format:"%d %b %Y"}}
            </a>
          </li>
        {{foreachelse}}
          <li>Pas d'intevention</li>
        {{/foreach}}
        </ul>
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td><em>Aucun séjour disponible</em></td>
    </tr>
    {{/foreach}}
  </tbody>
  <tr class="triggerShow" id="triggerconsultations" onclick="flipEffectElement('groupconsultations', 'SlideDown', 'SlideUp', 'triggerconsultations')">
    <td>Consultations ({{$patient->_ref_consultations|@count}})</td>
  </tr>
  <tbody id="groupconsultations" style="display:none">
    {{foreach from=$patient->_ref_consultations item=curr_consult}}
    <tr>
      <td>
        <a href="index.php?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->consultation_id}}">
          <strong>Dr. {{$curr_consult->_ref_plageconsult->_ref_chir->_view}}</strong>
          {{$curr_consult->_ref_plageconsult->date|date_format:"%d %b %Y"}}
        </a>
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td><em>Aucune consultation disponible</em></td>
    </tr>
    {{/foreach}}
  </tbody>
</table>
