{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
sejours = {{$sejours|@json}};
var tab_prescription  = {{$tab_prescription_id|@json}};

editFieldPrescription = function(patient_id){
  var form = getForm('filter');
	if(patient_id) {
    $V(form.prescription_id, tab_prescription[patient_id]);
    $V(form.sejour_id, sejours[patient_id]);
  } else {
	  $V(form.prescription_id, '');
    $V(form.sejour_id, '');
  }
}

Main.add(function () {
  editFieldPrescription("{{$patient_id}}");
});

</script>

<select name="patient_id" onchange="editFieldPrescription($V(this))">
  <option value="">&mdash; Globales</option>
  {{foreach from=$patients item=_patient}}
    <option value="{{$_patient->_id}}">{{$_patient->nom}} {{$_patient->prenom}}</option>
  {{/foreach}}
</select>
<input type="hidden" name="sejour_id" value="" />
<input type="hidden" name="prescription_id" value="" />