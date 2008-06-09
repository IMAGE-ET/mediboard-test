<script type="text/javascript">

// Si ligne de traitement perso finie, on empeche le passage en ligne de prescription normale
Main.add( function(){
var oDiv = $('editLineTraitement-{{$line->_id}}');
if(oDiv){  
{{if $line->_traitement && $line->date_arret && $line->date_arret <= $today}}
  oDiv.hide();
{{else}}
  oDiv.show();
{{/if}}
}
} );

</script>

{{if $line->_class_name == "CPrescriptionLineMedicament"}}
  {{assign var=dosql value="do_prescription_line_medicament_aed"}}
{{else}}
  {{assign var=dosql value="do_prescription_line_element_aed"}}
{{/if}}


{{if $line->_class_name == "CPrescriptionLineElement"}}
  {{assign var=category_id value=$category->_id}}
{{else}}
  {{assign var=category_id value=""}}
{{/if}}
<form name="form-stop-{{$object_class}}-{{$line->_id}}" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="{{$dosql}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
  {{if $line->date_arret}}
    <input type="hidden" name="date_arret" value="{{$line->date_arret}}" />
	  <button type="button"
	          class="cancel" 
	          onclick="this.form.date_arret.value = ''; 
	                   changeColor('{{$line->_id}}','{{$line->_class_name}}',this.form.date_arret.value,'{{$line->_traitement}}','{{$category_id}}'); 
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
	                onclick="calculDateArret(this.form, '{{$line->_id}}','{{$line->_class_name}}','{{$line->_traitement}}','{{$category_id}}');">  
	          Arrêter
	        </button>
	      </td>
	    </tr>
	  </table>
  {{/if}}
</form>


<script type="text/javascript">

calculDateArret = function(oForm, object_id, object_class, traitement, cat_id){
  if(!oForm.date_arret.value){
    oForm.date_arret.value = "{{$today}}";
  }
  changeColor(object_id, object_class, oForm.date_arret.value, traitement, cat_id);
  Prescription.submitFormStop(oForm, object_id, object_class);
}

// Preparation du formulaire
prepareForm(document.forms['form-stop-{{$line->_class_name}}-{{$line->_id}}']);

Main.add( function(){
  Calendar.regField('form-stop-{{$line->_class_name}}-{{$line->_id}}', "date_arret", false, dates);
} );

</script>
          