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
  url.setModuleAction("dPsalleOp", "httpreq_vw_timing");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("timing", { waitingText: null } );
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
  PairEffect.initGroup("acteEffect");
  
  var url = new Url;
  url.setModuleAction("dPsalleOp", "httpreq_liste_plages");
  url.addParam("date", "{{$date}}");
  url.addParam("operation_id", "{{$selOp->_id}}");
  url.periodicalUpdate('listplages', { frequency: 90 });
  
  {{if $selOp->_id}}
  // Initialisation des onglets
  new Control.Tabs('main_tab_group');
  
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
    <td style="width: 200px;" id="listplages"></td>
    <td class="greedyPane">
      <table class="form">
        {{if $selOp->operation_id}}
        {{assign var=patient value=$selOp->_ref_sejour->_ref_patient}}
        <tr>
          <th class="title" colspan="2">
					  <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
					    <img src="images/icons/edit.png" alt="modifier" />
					  </a>
            {{$patient->_view}} 
            ({{$patient->_age}} ans 
            {{if $patient->_age != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
            &mdash; Dr. {{$selOp->_ref_chir->_view}}
            {{if $selOp->libelle}}
              <br />
              {{$selOp->libelle}}
            {{/if}}
          </th>
        </tr>
        <tr>
          <td colspan="2">
            <ul id="main_tab_group" class="control_tabs">
              <li><a href="#one">Timings</a></li>
              <li><a href="#two">Anesthésie</a></li>
              <li><a href="#three">CCAM</a></li>
              <li><a href="#four">NGAP</a></li>
              <li><a href="#five">Dossier</a></li>
            </ul>
          </td>
        </tr>
          
      
        <!-- Premier onglet => Timings -->
      	<tbody id="one">
          <tr>
            <th class="category" style="vertical-align: middle">Timming</th>
            <td>
              <div id="timing">
                {{include file="inc_vw_timing.tpl"}}
              </div>
            </td>
          </tr>
          <tr>
            <th class="category">Personnel</th>
            <td>
              <div id="listPersonnel">        
              {{include file="inc_vw_personnel.tpl"}}
              </div>
            </td>
          </tr>
        </tbody>
        <!-- Fin du premier onglet -->
     
     
        <!-- Deuxieme onglet => Anesthesie -->
        <tbody id="two">
          <tr>
            <th rowspan="20" class="category" style="vertical-align: middle">Anesthésie</th>
            <td>
              <div id="anesth">
    			      {{include file="inc_vw_anesth.tpl"}}
    			    </div>
			      </td>
			   </tr>
			  {{include file="inc_vw_info_anesth.tpl"}}
			  </tbody>
			  <!-- Fin du deuxieme onglet -->
			 
			 
			 
        <!-- Troisieme onglet: codage acte ccam -->
        <tr id="three">
          <th class="category" style="vertical-align: middle">
            Actes<br /><br />
            {{tr}}{{$selOp->_class_name}}{{/tr}}
            {{if ($module=="dPplanningOp") || ($module=="dPsalleOp")}}
              <br />
              Côté {{tr}}COperation.cote.{{$selOp->cote}}{{/tr}}
              <br />
              ({{$selOp->temp_operation|date_format:"%Hh%M"}})
            {{/if}}
          </th>  
          <td>
            <div id="cim">
              {{assign var="sejour" value=$selOp->_ref_sejour}}
              {{include file="inc_diagnostic_principal.tpl"}}
            </div>
            <div id="ccam">
              {{assign var="subject" value=$selOp}}
              {{include file="inc_gestion_ccam.tpl"}}
            </div>
          </td>
        </tr>
        <!-- Fin du troisieme onglet -->
        
        
        
        <!-- Quatrième onglet => Codage acte NGAP -->
        <tr id="four">
          <th class="category" style="vertical-align: middle">
            Actes NGAP
          </th>
          <td id="listActesNGAP">
            {{assign var="object" value=$selOp}}
            {{include file="../../dPcabinet/templates/inc_acte_ngap.tpl"}}
          </td>
        </tr>
        <!-- Fin du quatrieme onglet -->
        
        
        
        
		    <!-- Cinquieme onglet => Dossier Medical -->
			  {{assign var="dossier_medical" value=$selOp->_ref_sejour->_ref_dossier_medical}}
        <tr id="five">
          <th class="category" style="vertical-align: middle">
            Séjour
          </th>
          <td class="text">
           {{include file="inc_vw_dossier.tpl"}}
          </td>
        </tr>
        <!-- Fin du cinquieme onglet -->
        
        
        
     
			
		
        {{if $selOp->materiel}}
        <tr>
          <th class="category">Matériel</th>
          <td><strong>{{$selOp->materiel|nl2br}}</strong></td>
        </tr>
        {{/if}}
        {{if $selOp->rques}}
        <tr>
          <th class="category">Remarques</th>
          <td>{{$selOp->rques|nl2br}}</td>
        </tr>
        {{/if}}
        {{else}}
        <tr>
          <th class="title">
            Sélectionnez une opération
          </th>
        </tr>
        {{/if}}
      </table>
    </td>
  </tr>      
</table>

<script type="text/javascript">      
  PairEffect.initGroup("operationEffect", { 
    bStoreInCookie: true
  });
</script>
  