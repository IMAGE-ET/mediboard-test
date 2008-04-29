{{if $line->_class_name == "CPrescriptionLineMedicament"}}
  {{assign var=dosql value="do_prescription_line_medicament_aed"}}
{{else}}
  {{assign var=dosql value="do_prescription_line_element_aed"}}
{{/if}}

<script type="text/javascript">

// Permet de changer la couleur de la ligne lorsqu'on stoppe la ligne
changeColor = function(object_id, object_class, date_arret, traitement){
  if(traitement == "1"){
    return;
  }
  var class_th_arretee = "arretee";
  if(object_class == "CPrescriptionLineMedicament"){
    var class_th_non_arretee = "";
  } else {
    var class_th_non_arretee = "element";
  }
  date_arret ? class_th=class_th_arretee : class_th=class_th_non_arretee;
  var oDiv = $('th_line_'+object_class+'_'+object_id);
  oDiv.className = class_th;
}

</script>

<form name="form-stop-{{$object_class}}-{{$line->_id}}" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="{{$dosql}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="{{$line->_tbl_key}}" value="{{$line->_id}}" />
  {{if $line->date_arret}}
    <input type="hidden" name="date_arret" value="{{$line->date_arret}}" />
	  <button type="button"
	          class="cancel" 
	          onclick="this.form.date_arret.value = ''; 
	                   changeColor('{{$line->_id}}','{{$line->_class_name}}',this.form.date_arret.value,'{{$line->_traitement}}'); 
	                   Prescription.submitFormStop(this.form,'{{$line->_id}}','{{$line->_class_name}}');">
	    Annuler l'arrêt
	  </button>
	  {{mb_value object=$line field=date_arret}}<br />
  {{else}}
    <table>
      <tr>
        <td class="date" style="border:none;">
          {{assign var=line_id value=$line->_id}}
	        {{mb_field object=$line field=date_arret form=form-stop-$object_class-$line_id canNull=false}}
	        <button type="button" 
	                class="stop" 
	                onclick="calculDateArret(this.form, '{{$line->_id}}','{{$line->_class_name}}','{{$line->_traitement}}');">  
	          Arrêter
	        </button>
	      </td>
	    </tr>
	  </table>
  {{/if}}
</form>


<script type="text/javascript">

calculDateArret = function(oForm, object_id, object_class, traitement){
  if(!oForm.date_arret.value){
    oForm.date_arret.value = "{{$today}}";
  }
  changeColor(object_id, object_class, oForm.date_arret.value, traitement);
  Prescription.submitFormStop(oForm, object_id, object_class);
}

// Preparation du formulaire
prepareForm(document.forms['form-stop-{{$line->_class_name}}-{{$line->_id}}']);

Main.add( function(){
  Calendar.regField('form-stop-{{$line->_class_name}}-{{$line->_id}}', "date_arret", false, dates);
} );

</script>
          