<script type="text/javascript">

var oFormClick = window.opener.document.click;

function submitAdmission(){
  oFormAdministration = document.addAdministration;
  checkForm(oFormAdministration);
  submitFormAjax(oFormAdministration, 'systemMsg');
}

function submitPlanification(){
  var oForm = document.addPlanification;
  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
    {{if $mode_plan}}
      window.opener.calculSoinSemaine('{{$date_sel}}',"{{$prescription_id}}"); 
    {{else}} 
      window.opener.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$line->_id}}','{{$line->_class_name}}',{{$key_tab|json}});
    {{/if}}
    window.close();
  } } ); 
}

// Fonction appelée en callback du formulaire d'administration
function submitTransmission(administration_id){
  oFormTransmission   = document.editTrans;
  oFormTransmission.object_class.value = "CAdministration";
  oFormTransmission.object_id.value = administration_id;
  if(oFormTransmission.text.value != ''){
    submitFormAjax(oFormTransmission, 'systemMsg', { onComplete: function(){ 
      {{if $mode_plan}}
        window.opener.calculSoinSemaine('{{$date_sel}}',"{{$prescription_id}}"); 
      {{else}}
        window.opener.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$line->_id}}','{{$line->_class_name}}',{{$key_tab|json}});
      {{/if}}
      window.opener.loadSuivi('{{$sejour->_id}}');
      window.close();
    } } )
  } else {
    {{if $mode_plan}}
      window.opener.calculSoinSemaine('{{$date_sel}}',"{{$prescription_id}}"); 
    {{else}}
      window.opener.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$line->_id}}','{{$line->_class_name}}',{{$key_tab|json}});
    {{/if}}
    window.close();
  }
}

function cancelAdministration(administration_id){
  var oFormDelAdministration = document.delAdministration;
  oFormDelAdministration.administration_id.value = administration_id;
  submitFormAjax(oFormDelAdministration, 'systemMsg', { onComplete: function(){
    {{if $mode_plan}}
      window.opener.calculSoinSemaine('{{$date_sel}}',"{{$prescription_id}}"); 
    {{else}} 
      window.opener.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$line->_id}}','{{$line->_class_name}}',{{$key_tab|json}});
    {{/if}}
    window.close();
  } } );
}

function checkTransmission(quantite_prevue, quantite_saisie){
  var oFormTrans = document.editTrans;
  if(quantite_prevue && quantite_prevue != quantite_saisie && oFormTrans.text.value == ""){
    alert("Veuillez saisir une transmission");
    return false;
  }
  return true;
}


</script>

<h2>
  Soins de {{$sejour->_ref_patient->_view}} 
  ({{if $sejour->_ref_patient->_ref_curr_affectation->_id}}
    {{$sejour->_ref_patient->_ref_curr_affectation->_view}}
  {{else}}
    Non placé actuellement
  {{/if}})
</h2>


<form name="delAdministration" method="post" action="?">
  <input type="hidden" name="dosql" value="do_administration_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="administration_id" value="" />
</form>

<hr class="control_tabs" />

{{if $mode_dossier == "administration" || $mode_plan}}
{{if $administrations || $mode_plan}}
	<table class="form">
	  <tr>
	    <th class="title">Liste des soins</th>
	  </tr>
	  {{foreach from=$administrations item=_administration}}
	  {{assign var=log value=$_administration->_ref_log}}
	  <tr>
	    <td>
	      <button class="cancel notext" type="button" onclick="cancelAdministration('{{$_administration->_id}}')"></button>
	      {{$log->_ref_object->quantite}} 
	      {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	        {{$_administration->_ref_object->_ref_produit->libelle_unite_presentation}} 
	      {{else}}
	        {{$line->_unite_prise}}
	      {{/if}}
	      administré par {{$log->_ref_user->_view}} le {{$log->_ref_object->dateTime|date_format:$dPconfig.datetime}}</li>
	    </td>
	  </tr>
	  {{foreachelse}}
	  <tr>
	    <td>Aucune administration</td>
	  </tr>
	  {{/foreach}}
	</table>
{{/if}}


<form name="addAdministration" method="post" action="?" onsubmit="return checkTransmission('{{$prise->quantite}}', this.quantite.value)">
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
        {{if $notToday}}
          <div class="small-info">
            {{if $mode_plan}}
              Attention, vous êtes sur le point d'administrer pour le {{$date|date_format:"%d/%m/%Y"}}, 
	            or nous sommes le {{$smarty.now|date_format:"%d/%m/%Y"}}.
	          {{else}}
	            Attention, cette prise est pour le {{$dateTime|date_format:"%d/%m/%Y à %Hh"}}, 
	            or nous sommes le {{$smarty.now|date_format:"%d/%m/%Y"}}.
            {{/if}}
          </div>
        {{/if}}
	      {{mb_label object=$prise field=quantite}}
	      {{mb_field object=$prise field=quantite min=1 increment=1 form=addAdministration}}
	      
	      {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	        {{$line->_ref_produit->libelle_unite_presentation}}
	      {{else}}
	        {{$line->_unite_prise}}
	      {{/if}} 
	      
	      {{if $mode_plan}}
	      à
	      <select name="_hour" class="notNull" onchange="$V(this.form.dateTime, '{{$date}} '+this.value);">
	        <option value="">&mdash; Heure</option>
	        {{foreach from=$hours item=_hour}}
	        <option value="{{$_hour}}:00:00">{{$_hour}}h</option>
	        {{/foreach}}
	      </select>
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
				<input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
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
{{/if}}

{{if $mode_dossier == "planification"}}
	{{if $planification->_id}}
		<table class="form">
		  <tr>
		    <th class="title">Planification</th>
		  </tr>
		  {{assign var=log value=$planification->_ref_log}}
		  <tr>
		    <td>
		      <button class="cancel notext" type="button" onclick="cancelAdministration('{{$planification->_id}}')"></button>
		      {{$log->_ref_object->quantite}} 
		      {{if $line->_class_name == "CPrescriptionLineMedicament"}}
		        {{$planification->_ref_object->_ref_produit->libelle_unite_presentation}} 
		      {{else}}
		        {{$line->_unite_prise}}
		      {{/if}}
		      le {{$log->_ref_object->dateTime|date_format:"%d/%m/%Y à %Hh%M"}}</li>
		    </td>
		  </tr>
		</table>
	{{/if}}
  {{if !$planification->_id && !$prise->quantite}}
	<form name="addPlanification" method="post" action="?">
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
	  <input type="hidden" name="planification" value="1" />
		<table class="form">
		  <tr>
		    <th class="title" colspan="2">Planification d'administration de {{$line->_view}}</th>
		  </tr>
		  <tr>
		    <td>
		      {{mb_label object=$prise field=quantite}}
		      {{mb_field object=$prise field=quantite min=1 increment=1 form=addPlanification}}
		      
		      {{if $line->_class_name == "CPrescriptionLineMedicament"}}
		        {{$line->_ref_produit->libelle_unite_presentation}}
		      {{else}}
		        {{$line->_unite_prise}}
		      {{/if}} 
		      
		      {{if $mode_plan}}
		      à
		      <select name="_hour" class="notNull" onchange="$V(this.form.dateTime, '{{$date}} '+this.value);">
		        <option value="">&mdash; Heure</option>
		        {{foreach from=$hours item=_hour}}
		        <option value="{{$_hour}}:00:00">{{$_hour}}h</option>
		        {{/foreach}}
		      </select>
		      {{else}}
		      à {{$dateTime|date_format:"%Hh%M"}}
		      {{/if}}
		    </td>
		  </tr>
		  <tr>
		    <td colspan="2" style="text-align: center;">
		      <button type="button" class="submit" onclick="submitPlanification();">Planifier</button>
		    </td>
		  </tr>
		</table>
	</form>
	{{elseif !$planification->_id}}
     <div class="small-info">
       Il est impossible de planifier sur cette case car elle possède déjà une prise prévue.
     </div>
	{{/if}}
{{/if}}