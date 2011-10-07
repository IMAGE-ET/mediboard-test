{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

var oFormClick = window.opener.document.click;

function submitConstantes(){
  var oFormConstantes = getForm("edit-constantes-medicales");
  checkForm(oFormConstantes);
  return onSubmitFormAjax(oFormConstantes);
}

function submitAdmission(constantes_medicales_id){
  var oFormAdministration = getForm("addAdministration");
  var quantite = $V(oFormAdministration.quantite);
  if (quantite && quantite >= 0) {
    $V(oFormAdministration.constantes_medicales_id, constantes_medicales_id);
    checkForm(oFormAdministration);
    return onSubmitFormAjax(oFormAdministration);
  }
  else {
    submitTransmission();
  }
}

function submitCancelAdm(){
  var oFormTransmission   = getForm("editTrans");
  $V(oFormTransmission._text_data, "Administration annulée");
  
	var oFormAdministration = getForm("addAdministration");
  $V(oFormAdministration.quantite, '0');
  return onSubmitFormAjax(oFormAdministration);
}

function submitPlanification(){
  var oForm = getForm("addPlanification");
	
  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
    {{if $mode_plan}}
      window.opener.calculSoinSemaine('{{$date_sel}}',"{{$prescription_id}}"); 
    {{else}} 
      window.opener.PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$line->_id}}','{{$line->_class}}',{{$key_tab|json}});
    {{/if}}
    window.close();
  } } ); 
}

// Fonction appelée en callback du formulaire d'administration
function submitTransmission(administration_id){
  var oFormTransmission   = getForm("editTrans");
  if (administration_id) {
    $V(oFormTransmission.object_class, "CAdministration", false);
    $V(oFormTransmission.object_id, administration_id, false);
    {{if $line instanceof CPrescriptionLineElement && $line->_ref_element_prescription->consultation && $is_praticien}}
      $V(oFormTransmission.callback, "window.opener.createConsult", false);
    {{/if}}
  }
  else {
    $V(oFormTransmission.object_class, '{{$line->_class}}', false);
    $V(oFormTransmission.object_id, '{{$line->_id}}', false);
  }
  
  submitFormAjax(oFormTransmission, 'systemMsg', { onComplete: function(){
    {{if $mode_plan}}
      window.opener.calculSoinSemaine('{{$date_sel}}',"{{$prescription_id}}"); 
    {{else}}
      // Si les transmissions sont sur une administration, reload de la ligne dans le plan de soin
      if (administration_id) {
        if (window.opener.PlanSoins.loadTraitement) {
          window.opener.PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$line->_id}}','{{$line->_class}}',{{$key_tab|json}});
        }
      }
      // Sinon rechargement de toute la zone
      else {
        if (window.opener.refreshTabState) {
          window.opener.refreshTabState();
        }
        if (window.opener.updatePlanSoinsPatients) {
          window.opener.updatePlanSoinsPatients();
        }
      }
    {{/if}}
    if (window.opener.loadSuivi) {
      window.opener.loadSuivi('{{$sejour->_id}}');
    }
		if (window.opener.updateNbTrans) {
		  window.opener.updateNbTrans('{{$sejour->_id}}');
		}
		
    {{if "forms"|module_active}}
		  if (administration_id) {
	      ExObject.trigger("CAdministration-"+administration_id, "validation", {
	        onTriggered: function(){ window.close(); }
	      });
			}
    {{else}}
      window.close();
    {{/if}}
  } } );
}

function cancelAdministration(administration_id){
  var oFormDelAdministration = document.delAdministration;
  oFormDelAdministration.administration_id.value = administration_id;
  submitFormAjax(oFormDelAdministration, 'systemMsg', { onComplete: function(){
    {{if $mode_plan}}
      window.opener.calculSoinSemaine('{{$date_sel}}',"{{$prescription_id}}"); 
    {{else}} 
      window.opener.PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$line->_id}}','{{$line->_class}}',{{$key_tab|json}});
    {{/if}}
    window.close();
  } } );
}

function checkTransmission(quantite_prevue, quantite_saisie){
  var oFormTrans = document.editTrans;
  if(quantite_prevue && parseFloat(quantite_prevue) != parseFloat(quantite_saisie) && oFormTrans.text.value == ""){
    alert("Veuillez saisir une transmission");
    return false;
  }
  return true;
}

updateQuantite = function(ratio_UI, oField){
  if(!ratio_UI){
	  return;
	}
	var oForm = getForm("addAdministration");
  
	if(oField.name == "quantite"){
	  var quantite_UI = ($V(oField) / ratio_UI).toFixed(3);
		$V(oForm._quantite_UI, quantite_UI, false);
	}
	if(oField.name == "_quantite_UI"){
    var quantite = ($V(oField) * ratio_UI).toFixed(3);
    $V(oForm.quantite, quantite, false);
  }
}

chooseSubmit = function() {
  {{if $line->_class == "CPrescriptionLineElement" && $selection|@count}}
    if (getForm("edit-constantes-medicales").select("input[type='text']").all(function(elt){ return elt.value == ''})) {
      submitAdmission();
    }
    else {
      submitConstantes();
    }
  {{else}}
    submitAdmission();
  {{/if}}
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
  	    	{{if $_administration->administrateur_id == $app->user_id || $can->admin}}
  	      <button class="trash notext" type="button" onclick="cancelAdministration('{{$_administration->_id}}')"></button>
					{{/if}}
  	      {{$log->_ref_object->quantite}} 
  	      {{if $line->_class == "CPrescriptionLineMedicament"}}
  				  {{if $line->_ref_produit_prescription->_id}}
  					  {{$_administration->_ref_object->_ref_produit_prescription->unite_prise}} 
            {{else}}
  	          {{$_administration->_ref_object->_ref_produit->libelle_unite_presentation}} 
  					{{/if}}
  	      {{else}}
  	        {{$line->_unite_prise}}
  	      {{/if}}
  	      administré par {{$log->_ref_user->_view}} le {{$log->_ref_object->dateTime|date_format:$conf.datetime}}
          <br/>
          <ul style="margin-left: 2em;">
            {{if $_administration->_ref_constantes_medicales && $_administration->_ref_constantes_medicales->_id}}
              {{assign var=constantes_med value=$_administration->_ref_constantes_medicales}}
                <li>
                  {{tr}}CConstantesMedicales{{/tr}} de {{$constantes_med->_ref_user}} le {{$constantes_med->datetime|date_format:$conf.datetime}} <br/>
                  {{foreach from=$params key=_key item=_field name="const"}}
                    {{if $constantes_med->$_key != null && $_key|substr:0:1 != "_"}}
                      {{mb_title object=$constantes_med field=$_key}} :
                      {{mb_value object=$constantes_med field=$_key}}{{$_field.unit}},
                    {{/if}}
                  {{/foreach}}
                </li>
            {{/if}}
            {{foreach from=$_administration->_ref_transmissions item=_transmission}}
              <li>
                {{tr}}CTransmissionMedicale{{/tr}} de {{$_transmission->_ref_user}} le {{$_transmission->date|date_format:$conf.datetime}} <br/>
                {{mb_value object=$_transmission field=text}}
              </li>
            {{/foreach}}
          </ul>
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
    <input type="hidden" name="object_class" value="{{$line->_class}}" />
    <input type="hidden" name="unite_prise" value="{{$unite_prise}}" />
	
    
		<input type="hidden" name="prise_id" value="{{$prise_id}}" />
    <input type="hidden" name="callback" value="submitTransmission" />
    <input type="hidden" name="_quantite_prevue" value="{{$prise->quantite}}" />
    <input type="hidden" name="constantes_medicales_id" value="" />
  	<table class="form">
  	  <tr>
  	    <th class="title" colspan="2">Administration de {{$line->_view}}<br />{{$dateTime|date_format:"%d/%m/%Y à %H:%M"}}</th>
  	  </tr>
  	  <tr>
  	    <td>
          {{if $notToday}}
            <div class="small-info">
              {{if $mode_plan}}
                Attention, vous êtes sur le point d'administrer pour le {{$dateTime|date_format:"%d/%m/%Y"}}, 
  	            or nous sommes le {{$smarty.now|date_format:"%d/%m/%Y"}}.
  	          {{else}}
  	            Attention, cette prise est pour le {{$dateTime|date_format:"%d/%m/%Y à %H:%M"}}, 
  	            or nous sommes le {{$smarty.now|date_format:"%d/%m/%Y"}}.
              {{/if}}
            </div>
          {{/if}}
  				
  				{{assign var=ratio_UI value=""}}
  				{{if $line instanceof CPrescriptionLineMedicament}}
  				{{assign var=ratio_UI value=$line->_ref_produit->_ratio_UI}}
  				{{/if}}
  				
  				{{mb_label object=$prise field=quantite}}
  	      {{mb_field object=$prise field=quantite min=0 increment=1 form=addAdministration onchange="updateQuantite('$ratio_UI', this)"}}
  	      
					
					
  	      {{if $line instanceof CPrescriptionLineMedicament}}
  				  {{if $line->_ref_produit_prescription->_id}}
  					  {{$line->_ref_produit_prescription->unite_prise}}
  					{{else}}
  	          {{$line->_ref_produit->libelle_unite_presentation}}
  					{{/if}}
  	      {{else}}
  	        {{$line->_unite_prise}}
  	      {{/if}} 
  	      			
		      <input type="hidden" name="_date" value="{{$dateTime|iso_date}}" />
          à {{mb_field object=$new_adm field=_time form=addAdministration}}
      		
  				{{if $line instanceof CPrescriptionLineMedicament && $line->_ref_produit->_ratio_UI}}
  					soit 
  				  {{mb_field object=$prise field=_quantite_UI min=0 increment=1 form=addAdministration onchange="updateQuantite('$ratio_UI', this)"}} UI
          {{/if}}
  				
  	    </td>
  	  </tr>
  	</table>
  </form>
  <br/>
  
  {{if $line->_class == "CPrescriptionLineElement" && $selection|@count}}
    {{assign var=patient value=$sejour->_ref_patient}}
    {{assign var=context_guid value=$sejour->_guid}}
    {{assign var=readonly value=0}}
    {{assign var=hide_save_button value=1}}
    {{assign var=callback_administration value=1}}
    {{assign var=display_graph value=0}}
    {{mb_include module=dPhospi template=inc_form_edit_constantes_medicales}}
  {{/if}}
  
  {{assign var=hide_cible value=1}}
  {{assign var=hide_button_add value=1}}
  {{mb_include module=dPhospi template=inc_transmission refreshTrans=0}}
  <button type="button" class="add singleclick" onclick="chooseSubmit()">{{tr}}Validate{{/tr}}</button>
  <button type="button" class="cancel" onclick="submitCancelAdm();">{{tr}}Cancel{{/tr}}</button>
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
		      <button class="trash notext" type="button" onclick="cancelAdministration('{{$planification->_id}}')"></button>
		      {{$log->_ref_object->quantite}} 
		      {{if $line->_class == "CPrescriptionLineMedicament"}}
		        {{$planification->_ref_object->_ref_produit->libelle_unite_presentation}} 
		      {{else}}
		        {{$line->_unite_prise}}
		      {{/if}}
		      le {{$log->_ref_object->dateTime|date_format:"%d/%m/%Y à %Hh%M"}}
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
	  <input type="hidden" name="object_class" value="{{$line->_class}}" />
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
		      
		      {{if $line->_class == "CPrescriptionLineMedicament"}}
					  {{if $line->_ref_produit_prescription->_id}}
						  {{$line->_ref_produit_prescription->unite_prise}}
						{{else}}
		          {{$line->_ref_produit->libelle_unite_presentation}}
		        {{/if}}
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
		      <button type="button" class="submit singleclick" onclick="submitPlanification();">Planifier</button>
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