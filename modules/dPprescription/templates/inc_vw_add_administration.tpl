<script type="text/javascript">

function submitAdmission(){
  oFormAdministration = document.addAdministration;
  submitFormAjax(oFormAdministration, 'systemMsg');
}

// Fonction appelée en callback du formulaire d'administration
function submitTransmission(administration_id){
  oFormTransmission   = document.editTrans;

  oFormTransmission.object_class.value = "CAdministration";
  oFormTransmission.object_id.value = administration_id;
  if(oFormTransmission.text.value != ''){
    submitFormAjax(oFormTransmission, 'systemMsg', { onComplete: function(){ 
      window.opener.loadTraitement('{{$sejour_id}}','{{$date}}');
      window.opener.loadSuivi('{{$sejour_id}}');
      window.close();
    } } )
  } else {
    window.opener.loadTraitement('{{$sejour_id}}','{{$date}}');
    window.close();  
  }
}

</script>

<form name="addAdministration" method="post" action="?">
  <input type="hidden" name="dosql" value="do_administration_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="administration_id" value="" />
  <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
  <input type="hidden" name="object_id" value="{{$line->_id}}" />
  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
  <input type="hidden" name="unite_prise" value="{{$unite_prise}}" />
  <input type="hidden" name="dateTime" value="{{$dateTime}}" />
  <input type="hidden" name="prise_id" value="{{$prise_id}}" />
  <input type="hidden" name="callback" value="submitTransmission" />
	<table class="form">
	  <tr>
	    <th class="title" colspan="2">Administration de {{$line->_view}}</th>
	  </tr>
	  <tr>
	    <td>
	      {{mb_label object=$prise field=quantite}}
	      {{mb_field object=$prise field=quantite min=1 increment=1 form=addAdministration}}
	      
	      {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	        {{$line->_ref_produit->libelle_unite_presentation}}
	      {{else}}
	        {{$line->_unite_prise}}
	      {{/if}} 
	    </td>
	  </tr>
	</table>
</form>

<table class="form">
  <tr>
    <td>
			<form name="editTrans" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
				<input type="hidden" name="dosql" value="do_transmission_aed" />
				<input type="hidden" name="del" value="0" />
				<input type="hidden" name="m" value="dPhospi" />
				<input type="hidden" name="object_class" value="" />
				<input type="hidden" name="object_id" value="" />
				<input type="hidden" name="sejour_id" value="{{$sejour_id}}" />
				<input type="hidden" name="user_id" value="{{$app->user_id}}" />
				<input type="hidden" name="date" value="now" />
				{{mb_label object=$transmission field="text"}}
				{{mb_field object=$transmission field="degre"}}
				<br />
				{{mb_field object=$transmission field="text"}}
			</form>
	  </td>
	</tr>
	<tr>
	  <td>
	  <button type="button" class="add" onclick="submitAdmission()">{{tr}}Add{{/tr}}</button>
	  </td>
	</tr>
</table>