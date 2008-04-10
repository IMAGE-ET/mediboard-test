<script type="text/javascript">

// Permet de changer la couleur de la ligne lorsqu'on stoppe la ligne
changeColor = function(oForm){
  var line_id = oForm.prescription_line_id.value;
  var date_arret = oForm.date_arret.value;
  date_arret ? color="#aaa" : color="#68c";
  var oDiv = $('th_line_'+line_id);
  oDiv.style.background = color;
}

</script>

<form name="stopMedicament-{{$curr_line->_id}}" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
  {{if $curr_line->date_arret}}
    <input type="hidden" name="date_arret" value="{{$curr_line->date_arret}}" />
	  <button type="button" class="cancel" onclick="this.form.date_arret.value = ''; changeColor(this.form); Prescription.submitFormStop(this.form);">
	    Annuler l'arrêt
	  </button>
	  {{mb_value object=$curr_line field=date_arret}}<br />
  {{else}}
    <table>
      <tr>
        <td class="date" style="border:none;">
          {{assign var=curr_line_id value=$curr_line->_id}}
	        {{mb_field object=$curr_line field=date_arret form=stopMedicament-$curr_line_id}}
	        <button type="button" class="stop" onclick="calculDateArret(this.form); changeColor(this.form);">Arrêter</button>
	      </td>
	    </tr>
	  </table>
  {{/if}}
</form>


<script type="text/javascript">

calculDateArret = function(oForm){
  if(!oForm.date_arret.value){
    oForm.date_arret.value = "{{$today}}";
  }
  changeColor(oForm);
  Prescription.submitFormStop(oForm);
}

// Preparation du formulaire
prepareForm(document.forms['stopMedicament-{{$curr_line->_id}}']);

Main.add( function(){
    regFieldCalendar('stopMedicament-{{$curr_line->_id}}', "date_arret");
} );

</script>
          