{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}


<script type="text/javascript">

var constantesMedicalesDrawn = false;
function refreshConstantesHack(sejour_id) {
  if (constantesMedicalesDrawn == false && $('constantes').visible() && sejour_id) {
    refreshConstantesMedicales('CSejour-'+sejour_id);
    constantesMedicalesDrawn = true;
  }
}

function refreshConstantesMedicales(context_guid) {
  if(context_guid) {
    var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
    url.addParam("context_guid", context_guid);
    url.requestUpdate("constantes", { waitingText: null } );
  }
}

function loadPatient(patient_id) {
  var url = new Url("system", "httpreq_vw_complete_object");
  url.addParam("object_class","CPatient");
  url.addParam("object_id",patient_id);
  url.requestUpdate('viewPatient', {
   onComplete: initNotes
  } );
}

function loadSejour(sejour_id) {
  var url = new Url("system", "httpreq_vw_complete_object");
  url.addParam("object_class","CSejour");
  url.addParam("object_id",sejour_id);
  url.requestUpdate('viewSejourHospi', {
   onComplete: initNotes
  } );
}


function loadTraitement(sejour_id, date, nb_decalage, mode_dossier, object_id, object_class, unite_prise, chapitre) {
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_dossier_soin");
  url.addParam("sejour_id", sejour_id);
  url.addParam("date", date);
  url.addParam("line_type", "bloc");
  url.addParam("mode_bloc", "1");
  url.addParam("mode_dossier", mode_dossier);
  if(nb_decalage){
    url.addParam("nb_decalage", nb_decalage);
  }
  url.addParam("chapitre", chapitre);
  url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("unite_prise", unite_prise);
    
    if(object_id && object_class){
      if(object_class == 'CPerfusion'){
        url.requestUpdate("line_"+object_class+"-"+object_id, { waitingText: null , onComplete: function() { 
          $("line_"+object_class+"-"+object_id).hide();
          moveDossierSoin($("line_"+object_class+"-"+object_id));
        } } );
      }
      else {
        first_td = $('first_'+object_id+"_"+object_class+"_"+unite_prise);
        last_td = $('last_'+object_id+"_"+object_class+"_"+unite_prise);
        
        // Suppression des td entre les 2 td bornes
        td = first_td;
        first_td.colSpan = 0;
        
        while(td.next().id != last_td.id){
          if(td.next().visible()){
            first_td.colSpan = first_td.colSpan + 1;
          }
          td.next().remove();
          first_td.show();
        }
        
        unite_prise = unite_prise.replace(/[^a-z0-9_-]/gi, '_');
                
        url.requestUpdate(first_td, {
                          waitingText: null, 
                          insertion: Insertion.After,
                          onComplete: function(){
                            moveDossierSoin($("line_"+object_class+"_"+object_id+"_"+unite_prise));
                            first_td.hide().colSpan = 0;
                          }
                          } );
      }
    } else {
      if(chapitre){
        if(chapitre == "med" || chapitre == "perf" || chapitre == "inj"){
          chapitre = "_"+chapitre;
        } else {
          chapitre = "_cat-"+chapitre;
        }
        url.requestUpdate(chapitre, { onComplete: function() { moveDossierSoin($(chapitre)); } } );
      } else {
        url.requestUpdate("soins", { waitingText: null } );
      }
    }
}

function loadSuivi(sejour_id, user_id) {
  if(sejour_id) {
    var urlSuivi = new Url("dPhospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.addParam("user_id", user_id);
    urlSuivi.requestUpdate("dossier_suivi", { waitingText: null } );
  }
}

function submitSuivi(oForm, prescription_id) {
  sejour_id = oForm.sejour_id.value;
  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { 
    loadSuivi(sejour_id); 
    if(oForm.object_class.value != "" || oForm.libelle_ATC.value != ''){
      // Refresh de la partie administration
      loadTraitement(sejour_id,'{{$date}}','','administration');
    }  
  } });
}

{{if $isPrescriptionInstalled}}
function reloadPrescription(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '', null, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}});
}
{{/if}}

Main.add(function () {
  headerPrescriptionTabs = Control.Tabs.create('tabs_reveil', false);

  {{if $operation->_id}}
  loadPatient('{{$sejour->patient_id}}');
  loadSejour('{{$sejour->_id}}');
	{{/if}}
	
	if($('Imeds_tab')){
    var url = new Url;
    url.setModuleAction("dPImeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", '{{$sejour->_id}}');
    url.requestUpdate('Imeds_tab', { waitingText : null });
  }
});

</script>

{{if $operation->_id}}
	{{assign var=patient value=$sejour->_ref_patient}}
	<table class="tbl">
	  <tr>
	    <th class="title text">
	      <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}"'>
	        {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$patient size=42}}
	      </a>
	      <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
	        <img src="images/icons/edit.png" alt="modifier" />
	       </a>
	      {{$patient->_view}}
	      ({{$patient->_age}} ans
	      {{if $patient->_age != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
	      &mdash; Dr {{$operation->_ref_chir->_view}}
	      <br />
	      {{if $operation->libelle}}{{$operation->libelle}} &mdash;{{/if}}
	      {{mb_label object=$operation field=cote}} : {{mb_value object=$operation field=cote}}
	    </th>
	  </tr>
	</table>
	
	<ul id="tabs_reveil" class="control_tabs">
		<li><a href="#viewPatient">Patient</a></li>
	  <li><a href="#viewSejourHospi">Séjour</a></li>
		  <li onclick="refreshConstantesHack('{{$sejour->_id}}');"><a href="#constantes">Constantes</a></li>
		{{if $isPrescriptionInstalled}}
	    <li onclick="loadTraitement('{{$sejour->_id}}','{{$date}}','','administration');"><a href="#soins">Soins</a></li>
		  <li onclick="Prescription.reloadPrescSejour('','{{$sejour->_id}}', null, null, null, null, null, true, Preferences.mode_readonly == 0);"><a href="#prescription_sejour">Prescription</a></li>
	  {{/if}}  
		<li><a href="#dossier_tab">Documents</a></li>
		{{if $isImedsInstalled}}
	    <li><a href="#Imeds_tab">Labo</a></li>
	  {{/if}}
	</ul>
	<hr class="control_tabs" />
	
	<div id="viewPatient"></div>
	<div id="viewSejourHospi" style="display: none;"></div>
	<div id="constantes" style="display: none;"></div>
	{{if $isPrescriptionInstalled}}
		<div id="soins" style="display: none;"></div>
		<div id="prescription_sejour" style="display: none;"></div>
	{{/if}}
	
	<!-- Dossier Medical et documents-->
	{{assign var="dossier_medical" value=$sejour->_ref_dossier_medical}}
	<div id="dossier_tab" style="display:none">
	  <table class="form">
	    <tr>
	      <th class="title">Documents</th>
	    </tr>
	    <tr>
	      <td>
	        <div id="documents">
	          {{mb_include_script module="dPcompteRendu" script="document"}}
	          {{mb_include_script module="dPcompteRendu" script="modele_selector"}}
	          {{include file=../../dPplanningOp/templates/inc_documents_operation.tpl}}
	        </div>
	      </td>
	    </tr>
	    <tr>
	      <td>
	        {{if !$dossier_medical->_id}}
	        <div class="big-info">
	          Le dossier médical pour ce séjour n'est pas créé, ou ne contient pas d'éléments parmi :
	          <ul>
	            <li>{{tr}}CAntecedent{{/tr}}</li>
	            <li>Diagnostics associés</li>
	          </ul>
	          Ces informations doivent être renseignés pendant la consultation de pré-anesthésie
	        </div>
	        {{else}}
	        <div class="text">
	          {{include file=../../dPpatients/templates/CDossierMedical_complete.tpl object=$dossier_medical}}
	        </div>
	        {{/if}}
	      </td>
	    </tr>
	  </table>
	</div>
	{{if $isImedsInstalled}}
		<div id="Imeds_tab" style="display:none"></div>
	{{/if}}

{{else}}
	<div class="small-info">
		Veuillez sélectionner un patient dans l'onglet <strong>Salle de réveil</strong> pour accéder à ces soins.
	</div>
{{/if}}