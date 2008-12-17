<script type="text/javascript">
sejours = {{$sejours|@json}};
var tab_prescription  = {{$tab_prescription_id|@json}};

editFieldPrescription = function(patient_id){
  if(patient_id) {
    var form = document.forms['filter'];
    form.prescription_id.value = tab_prescription[patient_id];
    form.sejour_id.value = sejours[patient_id];
  }
}

Main.add(function () {
  editFieldPrescription("{{$patient_id}}");
});
</script>

<select name="patient_id" onchange="editFieldPrescription($V(this))">
  <option value="">&mdash; Dispensations globales</option>
  {{foreach from=$patients item=_patient}}
    <option value="{{$_patient->_id}}">Dispensation pour {{$_patient->_view}}</option>
  {{/foreach}}
</select>
<input type="hidden" name="sejour_id" value="" />
<input type="hidden" name="prescription_id" value="" />