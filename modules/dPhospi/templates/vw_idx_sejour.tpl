{{mb_include_script module="dPcompteRendu" script="document"}}
{{mb_include_script module="dPplanningOp" script="cim10_selector"}}
{{mb_include_script module="dPImeds" script="Imeds_results_watcher"}}
{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}


{{assign var="do_subject_aed" value="do_sejour_aed"}}
{{assign var="module" value="dPhospi"}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}

<script language="Javascript" type="text/javascript">
     
function loadActesNGAP(sejour_id){
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_actes_ngap");
  url.addParam("object_id", sejour_id);
  url.addParam("object_class", "CSejour");
  url.requestUpdate('listActesNGAP', { waitingText: null } );
}

Document.refreshList = function(sejour_id){
  var url = new Url;
  url.setModuleAction("dPhospi", "httpreq_vw_documents");
  url.addParam("sejour_id" , sejour_id);
  url.requestUpdate('documents', { waitingText: null } );
}

function loadSejour(sejour_id) {
  url_sejour = new Url;
  url_sejour.setModuleAction("system", "httpreq_vw_complete_object");
  url_sejour.addParam("object_class","CSejour");
  url_sejour.addParam("object_id",sejour_id);
  url_sejour.requestUpdate('viewSejourHospi', {
	 onComplete: initPuces
  } );
}

function popEtatSejour(sejour_id) {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_parcours");
  url.addParam("sejour_id",sejour_id);
  url.pop(1000, 550, 'Etat du Séjour');
}

function reloadDiagnostic(sejour_id, modeDAS) {
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_diagnostic_principal");
  url.addParam("sejour_id", sejour_id);
  url.addParam("modeDAS", modeDAS);
  url.requestUpdate("cim", { 	waitingText : null } );
}


function loadViewSejour(sejour_id, praticien_id, prescription_id, date){
  // Affichage de la prescription
  if($('produits_elements')){
    if(prescription_id){
      Prescription.reload(prescription_id);
    } else {
      $('produits_elements').innerHTML = "";
    }
  }
  loadSejour(sejour_id); 
  Document.refreshList(sejour_id);
  if($('Imeds')){
    loadResultLabo(sejour_id);
  }
  if($('listActesNGAP')){
    loadActesNGAP(sejour_id);
  }
  if($('ccam')){
    ActesCCAM.refreshList(sejour_id, praticien_id);
  }
  if($('cim')){
    reloadDiagnostic(sejour_id, '1');
  }
  if($('dossier_soins')){
    loadDossierSoin(sejour_id, date);
  }
}

function loadResultLabo(sejour_id) {
  var url = new Url;
  url.setModuleAction("dPImeds", "httpreq_vw_sejour_results");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate('Imeds', { waitingText : null });
}

function loadDossierSoin(sejour_id, date){
  loadTraitement(sejour_id, date);
  loadSuivi(sejour_id);
}

function loadTraitement(sejour_id, date) {
  var urlTt = new Url;
  urlTt.setModuleAction("dPprescription", "httpreq_vw_dossier_soin");
  urlTt.addParam("sejour_id", sejour_id);
  urlTt.addParam("date", date);
  urlTt.requestUpdate("dossier_traitement", { waitingText: null } );
}

function loadSuivi(sejour_id) {
  var urlSuivi = new Url;
  urlSuivi.setModuleAction("dPhospi", "httpreq_vw_dossier_suivi");
  urlSuivi.addParam("sejour_id", sejour_id);
  urlSuivi.requestUpdate("dossier_suivi", { waitingText: null } );
}

function submitSuivi(oForm) {
  sejour_id = oForm.sejour_id.value;
  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { loadSuivi(sejour_id); } });
}

Main.add(function () {
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");

  /* Tab initialization */
  var tab_sejour = Control.Tabs.create('tab-sejour', true);
  
  {{if $app->user_prefs.ccam_sejour == 1 }}
  var tab_actes = Control.Tabs.create('tab-actes', false);
  {{/if}}

  {{if $object->_id}}
    {{assign var=prescriptions value=$object->_ref_prescriptions}}
    {{assign var=prescription value=$prescriptions.sejour.0}}
	  {{if $prescription}}
	    loadViewSejour({{$object->_id}}, null, '{{$prescription->_id}}','{{$date}}');
	  {{else}}
      loadViewSejour({{$object->_id}}, null, '','{{$date}}');
    {{/if}}
  {{/if}}
  
  {{if $isImedsInstalled}}
    ImedsResultsWatcher.loadResults();
  {{/if}}
  

/*
   var oFormService = document.selService;
   oFormService.service_id.value = "";
   $A(oFormService.service_id).each( function(input) {
	    input.disabled = true;
	 });
	 
	 var oFormMode = document.chgMode;
   oFormMode.mode.value = "1";
   $A(oFormMode.mode).each( function(input) {
	    input.disabled = true;
	 });
*/	 
	 
	  

});


</script>

<table class="main">
  <tr>
    <td style="width:200px;" rowspan="3">
      <form name="form_prescription" action="">
        <input type="hidden" name="prescription_id" value="" />
      </form>
      <table>
        <tr>
          <th>
            {{$date|date_format:"%A %d %B %Y"}}
            <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
          </th>
        </tr>
        <tr>
          {{include file="../../dPhospi/templates/inc_mode_hospi.tpl"}}
        </tr>
        {{if $m ==  "dPboard" && ($secretaire || $admin)}}
        <tr>
          <td>
            <form name="selPraticien" action="?m={{$m}}" method="get">
	            <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="mode" value="0" />
              <input type="hidden" name="service_id" value="" />
	            <select name="praticien_id" onchange="document.selService.service_id.value = ''; submit();">
	              <option value="">&mdash; Choix du praticien</option>
	              {{foreach from=$praticiens item=_prat}}
	                <option class="mediuser" style="border-color: #{{$_prat->_ref_function->color}};" value="{{$_prat->_id}}" {{if $_prat->_id == $praticien_id}}selected="selected"{{/if}}>
	                  {{$_prat->_view}}
	                </option>
	              {{/foreach}}
	            </select>
            </form>
          </td>
        </tr>
        {{/if}}
        {{if $m == "dPhospi"}}
        <tr>
          <td>
            <form name="selService" action="?m={{$m}}" method="get">
              <label for="service_id">Service</label>
              <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="sejour_id" value="" />
              <input type="hidden" name="praticien_id" value="" />
              <select name="service_id" onChange="document.selPraticien.praticien_id.value = '';submit()">
                <option value="">&mdash; Choix d'un service</option>
                {{foreach from=$services item=curr_service}}
                <option value="{{$curr_service->_id}}" {{if $curr_service->_id == $service_id}} selected="selected" {{/if}}>{{$curr_service->nom}}</option>
                {{/foreach}}
                <option value="NP" {{if $service_id == "NP"}} selected="selected" {{/if}}>Non placés</option>
              </select>
            </form>
            <br />
            <form name="selPraticien" action="?m={{$m}}" method="get">
	            <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="mode" value="0" />
              <input type="hidden" name="service_id" value="" />
	            <select name="praticien_id" onchange="document.selService.service_id.value = ''; submit();">
	              <option value="">&mdash; Choix du praticien</option>
	              {{foreach from=$praticiens item=_prat}}
	                <option class="mediuser" style="border-color: #{{$_prat->_ref_function->color}};" value="{{$_prat->_id}}" {{if $_prat->_id == $praticien_id}}selected="selected"{{/if}}>
	                  {{$_prat->_view}}
	                </option>
	              {{/foreach}}
	            </select>
            </form>
          </td>
        </tr>
        {{/if}}
        <tr>
          <td>
            <table class="tbl">
            
            {{foreach from=$sejoursParService key=service_id item=service}}
           
            {{if array_key_exists($service_id, $services)}}
            <tr>
              {{assign var=_service value=$services.$service_id}}
              <th colspan="6" class="title">{{$_service->_view}}</th>
            </tr>
            
            {{foreach from=$service->_ref_chambres item=curr_chambre}}
            
            {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
              <tr>
                <th class="category" colspan="6">
                  {{$curr_chambre->_view}} - {{$curr_lit->nom}}
                </th>
              </tr> 
              {{foreach from=$curr_lit->_ref_affectations item=curr_affectation}}
              
              {{if $curr_affectation->_ref_sejour->_id != ""}}
              <tr>
                <td>
                  <a href="#1" onclick="popEtatSejour({{$curr_affectation->_ref_sejour->_id}});">
                    <img src="images/icons/jumelle.png" alt="edit" title="Etat du Séjour" />
                  </a>
                </td>
                <td class="text">
                    {{assign var=prescriptions value=$curr_affectation->_ref_sejour->_ref_prescriptions}}
                    {{assign var=prescriptions_sejour value=$prescriptions.sejour}}
                    
                    {{if $prescriptions_sejour}}
                   {{assign var=prescription_sejour value=$prescriptions.sejour.0}}
                  
                    {{assign var=prescription_sejour_id value=$prescription_sejour->_id}}
                  {{else}}
                    {{assign var=prescription_sejour_id value=""}}
                  {{/if}}
                  <a href="#1" onclick="loadViewSejour({{$curr_affectation->_ref_sejour->_id}}, {{$curr_affectation->_ref_sejour->praticien_id}},'{{$prescription_sejour_id}}','{{$date}}');">
                    {{$curr_affectation->_ref_sejour->_ref_patient->_view}}
                  </a>
                  <script language="Javascript" type="text/javascript">
                    ImedsResultsWatcher.addSejour('{{$curr_affectation->_ref_sejour->_id}}', '{{$curr_affectation->_ref_sejour->_num_dossier}}');
                  </script>
                </td>
                <td>
                  <a href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$curr_affectation->_ref_sejour->_ref_patient->_id}}">
                    <img src="images/icons/edit.png" alt="edit" title="Editer le patient" />
                  </a>
                </td>
                <td>
                  <a href="{{$curr_affectation->_ref_sejour->_ref_patient->_dossier_cabinet_url}}&amp;patient_id={{$curr_affectation->_ref_sejour->_ref_patient->_id}}">
                    <img src="images/icons/search.png" alt="view" title="Afficher le dossier complet" />
                  </a>                             
                </td>
                <td>
                  <div id="labo_for_{{$curr_affectation->_ref_sejour->_id}}" style="display: none">
                    <img src="images/icons/labo.png" alt="Labo" title="Résultats de laboratoire disponibles" />
                  </div>
                  <div id="labo_hot_for_{{$curr_affectation->_ref_sejour->_id}}" style="display: none">
                    <img src="images/icons/labo_hot.png" alt="Labo" title="Résultats de laboratoire disponibles" />
                  </div>
                </td>
                <td class="action" style="background:#{{$curr_affectation->_ref_sejour->_ref_praticien->_ref_function->color}}">
                  {{$curr_affectation->_ref_sejour->_ref_praticien->_shortview}}
                
                  {{if $isPrescriptionInstalled}}  
		                <!-- Test des prescription de sortie -->
		                {{assign var=prescription_sortie value=""}}
		                  {{if $prescriptions}}
		                  {{if array_key_exists('sortie', $prescriptions)}}
		                    {{assign var=prescriptions_sortie value=$prescriptions.sortie}}
		                    {{if array_key_exists('0', $prescriptions_sortie)}}
		                      {{assign var=prescription_sortie value=$prescriptions_sortie.0}}
		                    {{/if}}
		                  {{/if}}
		                  {{/if}}
		                  {{if !is_object($prescription_sortie)}}
		                    <img src="images/icons/warning.png" alt="Aucune prescription de sortie" title="Aucune prescription de sortie" />
		                  {{/if}}
                  {{/if}}
                </td>
                
              </tr>
            {{/if}}
            {{/foreach}}
            {{/foreach}}
            {{/foreach}}
           
            {{/if}}
            
           {{/foreach}}
            
            <!-- Cas de l'affichage par praticien -->
            {{if $praticien_id}}
            {{if array_key_exists('NP', $sejoursParService)}}
            <tr>
             <th colspan="6">Non placés</th>
            </tr>
              {{foreach from=$sejoursParService.NP item=_sejour_NP}}
                {{include file="../../dPhospi/templates/inc_vw_sejour_np.tpl" curr_sejour=$_sejour_NP}}
              {{/foreach}}
              {{/if}}
            {{/if}}
            
            
            <!-- Cas de l'affichage par service -->
            {{if $service_id}}
	            {{foreach from=$groupSejourNonAffectes key=group_name item=sejourNonAffectes}}
	              <tr>
	                <th class="title" colspan="6">
	                  {{tr}}CSejour.groupe.{{$group_name}}{{/tr}}
	                </th>
	              </tr>
	              {{foreach from=$sejourNonAffectes item=curr_sejour}}
	                {{include file="../../dPhospi/templates/inc_vw_sejour_np.tpl"}}
	              {{/foreach}}
	            {{/foreach}}
            {{/if}}
          
            </table>    
          </td>
        </tr>
      </table>
    </td>
    <td>
    
      <!-- Tab titles -->
      <ul id="tab-sejour" class="control_tabs">
        <li><a href="#viewSejourHospi">Séjour</a></li>
        
        {{if $isPrescriptionInstalled}}
        <li><a href="#dossier_soins">Dossier de soins</a></li>
        <li><a href="#produits_elements">Prescriptions</a></li>
        {{/if}}
        
        {{if $app->user_prefs.ccam_sejour == 1 }}
          <li><a href="#Actes">Gestion des actes</a></li>
        {{/if}}
    
        {{if $isImedsInstalled}}
          <li><a href="#Imeds">Résultats labo</a></li>
        {{/if}}
    
        <li><a href="#documents">Documents</a></li>
      </ul>
      <hr class="control_tabs" />
      
      
      <!-- Tabs -->
      <div id="viewSejourHospi" style="display: none;"></div>
      {{if $isPrescriptionInstalled}}
      <div id="dossier_soins" style="display: none;">
        <div id="dossier_traitement">
        </div>
        <div id="dossier_suivi">
        </div>
      </div>
      <div id="produits_elements" style="display: none;">
      </div>
      {{/if}}
      
      {{if $app->user_prefs.ccam_sejour == 1 }}
      <div id="Actes" style="display: none;">
        <ul id="tab-actes" class="control_tabs">
          <li><a href="#one">Actes CCAM</a></li>
          <li><a href="#two">Actes NGAP</a></li>
          <li><a href="#three">Diagnostics</a></li>
        </ul>
        <hr class="control_tabs" />
        
        <table class="form">
          <tr id="one" style="display: none;">
            <td id="ccam"></td>
          </tr>
          <tr id="two" style="display: none;">
            <td id="listActesNGAP"></td>
          </tr>
          <tr id="three" style="display: none;">
            <td id="cim"></td>
          </tr>
        </table>
      </div>
      {{/if}}
    
      {{if $isImedsInstalled}}
      <div id="Imeds" style="display: none;"></div>
      {{/if}}
      
      <div id="documents" style="display: none;"></div>
    </td>
  </tr>
</table>


