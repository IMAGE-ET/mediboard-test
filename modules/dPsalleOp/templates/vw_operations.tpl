{{if $op}}
{{assign var="chir_id" value=$selOp->_ref_chir->_id}}
{{/if}}
{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="module" value="dPsalleOp"}}
{{assign var="object" value=$selOp}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}

<script type="text/javascript">

function submitTiming(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
  	onComplete : function() { 
  		reloadTiming(oForm.operation_id.value) 
  	} 
  });
}

function reloadTiming(operation_id){
  var url = new Url();
  {{if $object->_id}}
  url.setModuleAction("dPsalleOp", "httpreq_vw_timing");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("timing", { waitingText: null, onComplete: function() { ActesCCAM.refreshList({{$object->_id}},{{$object->_praticien_id}}) } } );
  {{/if}}
}

function submitAnesth(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
  	onComplete: function() { 
  		reloadAnesth(oForm.operation_id.value) 
  	}
  });
}

function reloadAnesth(operation_id){
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_vw_anesth");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("anesth", { 
  	waitingText: null,
  	onComplete: function() { 
  		ActesCCAM.refreshList(operation_id,"{{$selOp->chir_id}}"); 
  	}
  } );	
}

function reloadDiagnostic(sejour_id, modeDAS) {
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_diagnostic_principal");
  url.addParam("sejour_id", sejour_id);
  url.addParam("modeDAS", modeDAS);
  url.requestUpdate("cim", { 
		waitingText : null,
  	onComplete: CIM10Selector.close 
  } );
}

function reloadPersonnel(operation_id){
  var url = new Url;
  url.setModuleAction("dPsalleOp", "httpreq_vw_personnel");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("listPersonnel", {
    waitingText: null
  } );
}

function pageMain() {

  {{if !$dPconfig.dPsalleOp.CActeCCAM.openline}}
  PairEffect.initGroup("acteEffect");
  {{/if}}
  
  var url = new Url;
  {{if $dPconfig.dPsalleOp.COperation.mode}}
  url.setModuleAction("dPsalleOp", "httpreq_liste_op_prat");
  {{else}}
  url.setModuleAction("dPsalleOp", "httpreq_liste_plages");
  {{/if}}
  url.addParam("date", "{{$date}}");
  url.addParam("operation_id", "{{$selOp->_id}}");
  url.periodicalUpdate('listplages', { frequency: 90 });
  
  {{if $selOp->_id}}
  // Initialisation des onglets
  new Control.Tabs('main_tab_group');
  // Effet sur le programme
  new PairEffect("listplages", { sEffect : "appear", bStartVisible : true });
  // Affichage de la liste des documents de l'operation
  reloadAfterSaveDoc();

  {{/if}}
}

// Gestion des dossiers
function createDocument(modele_id, operation_id) {
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("modele_id", modele_id);
  url.addParam("object_id", operation_id);
  url.popup(700, 700, "Document");
}

function createPack(pack_id, operation_id) {
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("pack_id", pack_id);
  url.addParam("object_id", operation_id);
  url.popup(700, 700, "Document");
}

function editDocument(compte_rendu_id) {
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("compte_rendu_id", compte_rendu_id);
  url.popup(700, 700, "Document");
}

function reloadAfterSaveDoc() {
  var url = new Url;
  url.setModuleAction("dPsalleOp", "httpreq_vw_list_documents");
  url.addParam("operation_id" , "{{$selOp->_id}}");
  url.requestUpdate('documents');
}

</script>

<table class="main">
  <tr>
    <td style="width: 220px;" id="listplages"></td>
    <td class="">
      <table class="form">
        {{if $selOp->_id}}

				<!-- Informations générales sur l'intervention et le patient -->
        {{assign var=patient value=$selOp->_ref_sejour->_ref_patient}}
        <tr>
          <th class="title" colspan="2">
			      <button class="hslip notext" id="listplages-trigger" type="button" style="float:left">
			        {{tr}}Programme{{/tr}}
			      </button>
					  <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
					    <img src="images/icons/edit.png" alt="modifier" />
					  </a>
            {{$patient->_view}} 
            ({{$patient->_age}} ans 
            {{if $patient->_age != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
            &mdash; Dr. {{$selOp->_ref_chir->_view}}
            <br />
            {{if $selOp->libelle}}{{$selOp->libelle}}{{/if}}
						&mdash; {{mb_label object=$selOp field=cote}} : {{mb_value object=$selOp field=cote}}
						&mdash; {{mb_label object=$selOp field=temp_operation}} : {{mb_value object=$selOp field=temp_operation}}
            
          </th>
        </tr>
        
        <!-- Mise en avant du matériel et remarques -->
			  {{if $selOp->materiel}}
			  <tr>
			    <td class="text">
			      <strong>{{mb_label object=$selOp field=materiel}} :</strong> 
			      {{mb_value object=$selOp field=materiel}}
			    </td>
			  </tr>
			  {{/if}}
			  {{if $selOp->rques}}
			  <tr>
			    <td class="text">
			      <strong>{{mb_label object=$selOp field=rques}} :</strong> 
			      {{mb_value object=$selOp field=rques}}
			    </td>
			  </tr>
			  {{/if}}
			</table>

		  <!-- Tabulations -->
      <ul id="main_tab_group" class="control_tabs">
        <li><a href="#one">Timings</a></li>
        <li><a href="#two">Anesthésie</a></li>
        <li><a href="#threebis">Diagnostics</a></li>
        <li><a href="#three">CCAM</a></li>
        <li><a href="#four">NGAP</a></li>
        <li><a href="#five">Dossier</a></li>
      </ul>
        
      <hr class="control_tabs" />
      
      <!-- Premier onglet => Timings + Personnel -->
      <div id="one" style="display:none">
       	<div id="timing">
     	    {{include file="inc_vw_timing.tpl"}}
     	  </div>
     	  <div id="listPersonnel">
     	    {{include file="inc_vw_personnel.tpl"}}
     	  </div>
      </div>
   
      <!-- Deuxieme onglet => Anesthesie -->
      <div id="two" style="display:none">
        <div id="anesth">
			    {{include file="inc_vw_anesth.tpl"}}
	      </div>
        <div id="info_anesth">
		      {{include file="inc_vw_info_anesth.tpl"}}
        </div>
      </div>

      <!-- Troisieme onglet bis: codage diagnostics CIM -->
      <div id="threebis" style="display:none">
        <div id="cim">
          {{assign var="sejour" value=$selOp->_ref_sejour}}
          {{include file="inc_diagnostic_principal.tpl" modeDAS=true}}
        </div>
      </div>

      <!-- Troisieme onglet: codage acte ccam -->
      <div id="three" style="display:none">
        <div id="ccam">
          {{assign var="subject" value=$selOp}}
          {{include file="inc_gestion_ccam.tpl"}}
        </div>
      </div>
      <!-- Fin du troisieme onglet -->
      
      <!-- Quatrième onglet => Codage acte NGAP -->
      <div id="four" style="display:none">
        <div id="listActesNGAP">
          {{assign var="object" value=$selOp}}
          {{include file="../../dPcabinet/templates/inc_acte_ngap.tpl"}}
        </div>
      </div>
      
    <!-- Cinquieme onglet => Dossier Medical -->
	    {{assign var="dossier_medical" value=$selOp->_ref_sejour->_ref_dossier_medical}}
      <div id="five" style="display:none">
        <div class="text">
         {{include file="inc_vw_dossier.tpl"}}
        </div>
      </div>
    </td>
  </tr> 
  
  {{else}}
  <tr>
    <td>
      <div class="big-info">
      	Veuillez sélectionner une opération dans la liste pour pouvoir :
      	<ul>
      	  <li>coder les actes</li>
      	  <li>coder les diagnostics</li>
      	  <li>effectuer l'horodatage</li>
      	  <li>sélectionner le personnel en salle</li>
      	  <li>consulter le dossier</li>
      	</ul>
      </div>
    </td>
  </tr>
  {{/if}}
       
</table>

<script type="text/javascript">      
  PairEffect.initGroup("operationEffect", { 
    bStoreInCookie: true
  });
</script>
  