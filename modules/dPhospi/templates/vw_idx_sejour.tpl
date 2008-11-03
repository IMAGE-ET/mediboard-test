{{mb_include_script module="dPcompteRendu" script="document"}}
{{mb_include_script module="dPcompteRendu" script="modele_selector"}}
{{mb_include_script module="dPcabinet" script="file"}}
{{mb_include_script module="dPplanningOp" script="cim10_selector"}}
{{mb_include_script module="dPImeds" script="Imeds_results_watcher"}}
{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="dPcompteRendu" script="aideSaisie"}}

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

function loadPatient(patient_id) {
  url = new Url;
  url.setModuleAction("system", "httpreq_vw_complete_object");
  url.addParam("object_class","CPatient");
  url.addParam("object_id",patient_id);
  url.requestUpdate('viewPatient', {
   onComplete: initPuces
  } );
}

function loadSejour(sejour_id) {
  url = new Url;
  url.setModuleAction("system", "httpreq_vw_complete_object");
  url.addParam("object_class","CSejour");
  url.addParam("object_id",sejour_id);
  url.requestUpdate('viewSejourHospi', {
   onComplete: initPuces
  } );
}

function loadDocuments(sejour_id) {
  var url = new Url;
  url.setModuleAction("dPhospi", "httpreq_documents_sejour");
  url.addParam("sejour_id" , sejour_id);
  url.requestUpdate("documents", { waitingText: null } );
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
  url.requestUpdate("cim", {   waitingText : null } );
}


function reloadPrescription(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '');
}


function reloadAntAllergie(sejour_id){
  if($('antecedent_allergie')){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_antecedent_allergie");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate("antecedent_allergie", { waitingText: null } );
  }
}


function loadViewSejour(sejour_id, praticien_id, patient_id, date){
  // Affichage de la prescription
  if($('prescription_sejour')){
    Prescription.reloadPrescSejour('', sejour_id, null, null, null, null, null, true);
  }
  loadPatient(patient_id);
  loadSejour(sejour_id); 
  loadDocuments(sejour_id);

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
    document.form_prescription.sejour_id.value = sejour_id;
    if($('dossier_soins').visible()) {
      loadTraitement(sejour_id, date);
    }
    loadSuivi(sejour_id);
  }
  if($('constantes-medicales')){
    constantesMedicalesDrawn = false;
    refreshConstantesHack(sejour_id);
  }
  {{if $can_view_dossier_medical}}
  if($('antecedents')){
    loadAntecedents(sejour_id);    
  }
  {{/if}}
}

{{if $can_view_dossier_medical}}
function loadAntecedents(sejour_id){
  var url = new Url;
  url.setModuleAction("dPcabinet","httpreq_vw_antecedents");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate('antecedents', { waitingText: null } )
}
{{/if}}

function loadResultLabo(sejour_id) {
  var url = new Url;
  url.setModuleAction("dPImeds", "httpreq_vw_sejour_results");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate('Imeds', { waitingText : null });
}

function loadTraitement(sejour_id, date, nb_decalage) {
  if(sejour_id) {
    var urlTt = new Url;
    urlTt.setModuleAction("dPprescription", "httpreq_vw_dossier_soin");
    urlTt.addParam("sejour_id", sejour_id);
    urlTt.addParam("date", date);
    if(nb_decalage){
      urlTt.addParam("nb_decalage", nb_decalage);
    }
    urlTt.requestUpdate("dossier_traitement", { waitingText: null } );
  }
}

function loadSuivi(sejour_id) {
  if(sejour_id) {
    var urlSuivi = new Url;
    urlSuivi.setModuleAction("dPhospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.requestUpdate("dossier_suivi", { waitingText: null } );
  }
}

function submitSuivi(oForm, prescription_id) {
  sejour_id = oForm.sejour_id.value;
  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { 
    loadSuivi(sejour_id); 
    if(oForm.object_class.value != ""){
      // Refresh de la partie administration
      if($('jour').visible()){
        loadTraitement(sejour_id,'{{$date}}');
      }
      // Refresh de la partie plan de soin
      if($('semaine').visible()){
        calculSoinSemaine('{{$date}}', prescription_id);
      }  
    }  
  } });
}

var constantesMedicalesDrawn = false;
function refreshConstantesHack(sejour_id) {
  if (constantesMedicalesDrawn == false && $('constantes-medicales').visible() && sejour_id) {
    refreshConstantesMedicales(sejour_id);
    constantesMedicalesDrawn = true;
  }
}

function refreshConstantesMedicales(sejour_id) {
  if(sejour_id) {
    var url = new Url;
    url.setModuleAction("dPhospi", "httpreq_vw_constantes_medicales");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate("constantes-medicales", { waitingText: null } );
  }
}

function printPatient(patient_id) {
  var url = new Url();
  url.setModuleAction("dPpatients", "print_patient");
  url.addParam("patient_id", patient_id);
  url.popup(700, 550, "Patient");
}

Main.add(function () {
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");

  /* Tab initialization */
  var tab_sejour = Control.Tabs.create('tab-sejour', true);
  
  {{if $app->user_prefs.ccam_sejour == 1 }}
  var tab_actes = Control.Tabs.create('tab-actes', false);
  {{/if}}

  {{if $object->_id}}
  loadViewSejour('{{$object->_id}}', null, '{{$object->patient_id}}', '{{$date}}');
  {{/if}}
  
  {{if $isImedsInstalled}}
    ImedsResultsWatcher.loadResults();
  {{/if}}
});


viewBilanService = function(service_id, date){
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_bilan_service");
  url.addParam("service_id", service_id);
  url.addParam("date", date);
  url.popup(800,500,"Bilan par service");
}

</script>

<table class="main">
  <tr>
    <td rowspan="3" style="width:0.1%;">
      <form name="form_prescription" action="">
        <input type="hidden" name="sejour_id" value="{{$object->_id}}" />
      </form>
      <table class="form" id="left-column" style="width:200px;">
        <tr>
          <th class="category">
            {{$date|date_format:"%A %d %B %Y"}}
            <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
          </th>
        </tr>
        <tr>
          {{include file="../../dPhospi/templates/inc_mode_hospi.tpl"}}
        </tr>

        {{if 1 || !$praticien || $anesthesiste}}
        <tr>
          <td>
            {{if $m == "dPhospi" || $anesthesiste}}
            <form name="selService" action="?m={{$m}}" method="get">
              <label for="service_id">Service</label>
              <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="sejour_id" value="" />

              <select name="service_id" onchange="this.form.submit()">
                <option value="">&mdash; Choix d'un service</option>
                {{foreach from=$services item=curr_service}}
                <option value="{{$curr_service->_id}}" {{if $curr_service->_id == $service_id}} selected="selected" {{/if}}>{{$curr_service->nom}}</option>
                {{/foreach}}
                <option value="NP" {{if $service_id == "NP"}} selected="selected" {{/if}}>Non placés</option>
              </select>
              {{if $service_id && $isPrescriptionInstalled}}
                <button type="button" class="search" onclick="viewBilanService('{{$service_id}}','{{$date}}');">Bilan par service</button>
        			{{/if}}
            </form>
            <br />
            {{/if}}
            <form name="selPraticien" action="?m={{$m}}" method="get">
              <label for="praticien_id">Praticien</label>
              <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="mode" value="0" />
							<input type="hidden" name="sejour_id" value="" />
              <select name="praticien_id" onchange="this.form.submit();">
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
            {{foreach from=$sejoursParService key=_service_id item=service}}
              {{if array_key_exists($_service_id, $services)}}
              <tr>
                {{assign var=_service value=$services.$_service_id}}
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
                  {{assign var=prescription_sejour value=$prescriptions.sejour}}
                  {{assign var=prescription_sortie value=$prescriptions.sortie}}

                  <a href="#1" onclick="loadViewSejour({{$curr_affectation->_ref_sejour->_id}}, {{$curr_affectation->_ref_sejour->praticien_id}}, {{$curr_affectation->_ref_sejour->patient_id}}, '{{$date}}');">
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
											{{if !$prescription_sortie->_id}}
		                      <img src="images/icons/warning.png" alt="Aucune prescription de sortie" title="Aucune prescription de sortie" />
		                  {{/if}}
		                  {{if $prescription_sejour->_counts_no_valide}}
		                    <img src="images/icons/flag.png" alt="Lignes non validées" title="Lignes non validées" />
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
             <th class="title" colspan="6">Non placés</th>
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
        <li><button type="button" class="hslip notext" onclick="$('left-column').toggle();" title="Afficher/cacher la colonne de gauche"></button>
        <li><a href="#viewPatient">Patient</a></li>
        <li><a href="#viewSejourHospi">Séjour</a></li>
        
        <li onclick="refreshConstantesHack(document.form_prescription.sejour_id.value)"><a href="#constantes-medicales">Constantes</a></li>
        
        {{if $isPrescriptionInstalled}}
        <li onclick="loadTraitement(document.form_prescription.sejour_id.value,'{{$date}}')"><a href="#dossier_soins">Soins</a></li>
        <li onclick="reloadAntAllergie(document.form_prescription.sejour_id.value)"><a href="#prescription_sejour">Prescription</a></li>
        {{/if}}
        
        {{if $app->user_prefs.ccam_sejour == 1 }}
          <li><a href="#Actes">Cotation</a></li>
        {{/if}}
    
        {{if $isImedsInstalled}}
          <li><a href="#Imeds">Labo</a></li>
        {{/if}}
    
        <li><a href="#documents">Documents</a></li>
        
        {{if $isPrescriptionInstalled && $can_view_dossier_medical}}
        <li><a href="#antecedents">Antécédents</a></li>
        {{/if}}
        
      </ul>
      <hr class="control_tabs" />
      
      
      <!-- Tabs -->
      <div id="viewPatient" style="display: none;">
        <div class="big-info">
          Veuillez selectionner un séjour dans la liste de gauche pour afficher
          ici toutes les informations sur le patient.
        </div>
      </div>
      
      <div id="viewSejourHospi" style="display: none;">
        <div class="big-info">
          Veuillez selectionner un séjour dans la liste de gauche pour afficher
          ici toutes les informations le concernant.
        </div>
      </div>
      
      <div id="constantes-medicales" style="display: none;">
        <div class="big-info">
          Veuillez selectionner un séjour dans la liste de gauche pour afficher
          les constantes du patient concerné.
        </div>
      </div>
      
      {{if $isPrescriptionInstalled}}
      <div id="dossier_soins" style="display: none;">
        <div id="dossier_traitement">
          <div class="big-info">
            Veuillez selectionner un séjour dans la liste de gauche pour afficher
            le dossier de soin du patient concerné.
          </div>
        </div>
        <hr />
        <div id="dossier_suivi"></div>
      </div>
      
      <div id="prescription_sejour" style="display: none;">
        <div class="big-info">
          Veuillez selectionner un séjour dans la liste de gauche pour afficher
          la prescription du patient concerné.
        </div>
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
            <td id="ccam">
              <div class="big-info text">
                Veuillez selectionner un séjour dans la liste de gauche pour pouvoir
                ajouter des actes CCAM au patient concerné.
              </div>
            </td>
          </tr>
          <tr id="two" style="display: none;">
            <td id="listActesNGAP">
              <div class="big-info text">
                Veuillez selectionner un séjour dans la liste de gauche pour pouvoir
                ajouter des actes NGAP au patient concerné.
              </div>
            </td>
          </tr>
          <tr id="three" style="display: none;">
            <td id="cim">
              <div class="big-info text">
                Veuillez selectionner un séjour dans la liste de gauche pour pouvoir
                ajouter des actes diagnostics CIM au patient concerné.
              </div>
            </td>
          </tr>
        </table>
      </div>
      {{/if}}
    
      {{if $isImedsInstalled}}
      <div id="Imeds" style="display: none;">
        <div class="big-info">
          Veuillez selectionner un séjour dans la liste de gauche pour pouvoir
          consulter les résultats de laboratoire disponibles pour le patient concerné.
        </div>
      </div>
      {{/if}}
      
      <div id="documents" style="display: none;">
        <div class="big-info">
          Veuillez selectionner un séjour dans la liste de gauche pour pouvoir
          consulter et ajouter des documents pour le patient concerné.
        </div>
      </div>

      {{if $can_view_dossier_medical}}
      <div id="antecedents" style="display: none;">
        <div class="big-info">
          Veuillez selectionner un séjour dans la liste de gauche pour pouvoir
          consulter et modifier les antécédents du le patient concerné.
        </div>
      </div>
      {{/if}}
      
    </td>
  </tr>
</table>


