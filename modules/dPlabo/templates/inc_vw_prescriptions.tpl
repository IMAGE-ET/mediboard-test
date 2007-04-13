<script type="text/javascript">

var Prescription = {
  dropElement: function(element_id, prescription_id) {
    oForm = $('newPrescriptionItem');
    oForm.examen_labo_id.value       = examen_id.substring(7);
    oForm.prescription_labo_id.value = prescription_id;
    submitFormAjax(oForm, 'systemMsg', { onComplete: reloadPrescriptions });
    return true;
  }
}
  
</script>

<form name="editPrescriptionItem" id="newPrescriptionItem" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPlabo" />
  <input type="hidden" name="dosql" value="do_prescription_examen_aed" />
  <input type="hidden" name="prescription_labo_examen_id" value="" />
  <input type="hidden" name="examen_labo_id" value="" />
  <input type="hidden" name="prescription_labo_id" value="" />
  <input type="hidden" name="del" value="0" />
</form>

{{foreach from=$patient->_ref_prescriptions item="curr_prescription"}}
<div class="tree-header {{if $curr_prescription->_id == $prescription->_id}}selected{{/if}}" id="drop-prescription-{{$curr_prescription->_id}}">
  <script>
  Droppables.add('drop-prescription-{{$curr_prescription->_id}}', {
    onDrop: function(element) {
      Prescription.dropExamen(element.id, {{$curr_prescription->_id}})
    }, 
    hoverclass:'selected'
  } );
  </script>
  <div style="float:right;">
    {{$curr_prescription->_ref_prescription_labo_examens|@count}} Examens
  </div>
  <a href="#nothing" onclick="reloadPrescriptions({{$curr_prescription->_id}})">
    {{$curr_prescription->_view}}
  </a>
</div>
{{/foreach}}

{{if $prescription->_id}}
<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      <a style="float:right;" href="#nothing" onclick="view_log('{{$prescription->_class_name}}', {{$prescription->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      {{$prescription->_view}}
    </th>
  </tr>
  <tr>
    <th class="category">Examen</th>
    <th class="category">Type</th>
    <th class="category">Unité</th>
    <th class="category">Min</th>
    <th class="category">Max</th>
  </tr>
  {{foreach from=$prescription->_ref_prescription_labo_examens item="curr_item"}}
  {{assign var="curr_examen" value=$curr_item->_ref_examen_labo}}
  <tr>
    <td>
      {{$curr_examen->_view}}
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->type}}
      </a>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->unite}}
      </a>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->min}} {{$curr_examen->unite}}
      </a>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->max}} {{$curr_examen->unite}}
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>
{{/if}}