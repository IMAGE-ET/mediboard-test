<script type="text/javascript">

var tab_prescription  = {{$tab_prescription_id|@json}};

editFieldPrescription = function(patient_id){
  var oForm = document.forms['filter-dispensations'];
  oForm.prescription_id.value = tab_prescription[patient_id];
}

Main.add(function () {
  editFieldPrescription("{{$patient_id}}");
});

</script>

<select name="patient_id" onchange="editFieldPrescription(this.value)">
  <option value="">&mdash; Sélection d'un patient</option>
  {{foreach from=$patients item=_patient}}
    <option value="{{$_patient->_id}}" {{if $_patient->_id == $patient_id}}selected="selected"{{/if}}>{{$_patient->_view}}</option>
  {{/foreach}}
</select>

<input type="hidden" name="prescription_id" value="" />