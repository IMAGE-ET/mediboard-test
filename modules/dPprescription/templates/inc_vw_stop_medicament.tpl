<form name="stopMedicament-{{$curr_line->_id}}" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
  {{if $curr_line->date_arret}}
    <input type="hidden" name="date_arret" value="{{$curr_line->date_arret}}" />
	  <button type="button" class="cancel" onclick="this.form.date_arret.value = ''; Prescription.submitFormStop(this.form);">
	    Annuler l'arrêt
	  </button>
	  Date de l'arrêt: {{mb_value object=$curr_line field=date_arret}}<br />
  {{else}}
    <table>
      <tr>
        <td class="date" style="border:none;">
          {{assign var=curr_line_id value=$curr_line->_id}}
	        {{mb_field object=$curr_line field=date_arret form=stopMedicament-$curr_line_id}}
	      </td>
	    </tr>
	  </table>
    <button type="button" class="tick" onclick="calculDateArret(this.form);">Arrêter la ligne</button>
  {{/if}}
</form>


<script type="text/javascript">

calculDateArret = function(oForm){
  if(!oForm.date_arret.value){
    oForm.date_arret.value = "{{$today}}";
  }
  Prescription.submitFormStop(oForm);
}

// Preparation du formulaire
prepareForm(document.forms['stopMedicament-{{$curr_line->_id}}']);

Main.add( function(){
    regFieldCalendar('stopMedicament-{{$curr_line->_id}}', "date_arret");
} );

</script>
          