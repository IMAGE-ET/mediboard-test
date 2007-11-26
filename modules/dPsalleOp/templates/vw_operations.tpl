{{if $op}}
{{assign var="chir_id" value=$selOp->_ref_chir->_id}}
{{/if}}
{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="module" value="dPsalleOp"}}
{{assign var="object" value=$selOp}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}

<script type="text/javascript">

function submitTiming(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : function() { reloadTiming(oForm.operation_id.value) } });
}

function reloadTiming(operation_id){
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_vw_timing");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("timing", "systemMsg");
}

function submitAnesth(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : function() { reloadAnesth(oForm.operation_id.value) } });
}

function reloadAnesth(operation_id){
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_vw_anesth");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("anesth", "systemMsg", { onComplete: ActesCCAM.refreshList(operation_id,"{{$selOp->chir_id}}") });
}

function reloadDiagnostic(sejour_id) {
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_diagnostic_principal");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate("cim", { onComplete: CIM10Selector.close });
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
  updateListDocuments();
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

function updateListDocuments() {
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
          </th>
        </tr>
        <tr>
          <td colspan="2">
            <ul id="main_tab_group" class="control_tabs">
              <li><a href="#one">Timmings</a></li>
              <li><a href="#two">Anesthésie</a></li>
              <li><a href="#three">Codage</a></li>
              <li><a href="#four">Dossier</a></li>
            </ul>
          </td>
        </tr>
          
        {{include file="inc_timings_anesth.tpl"}}
      
        <!-- <tbody id = "ccam"> -->
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
        <!-- </tbody> -->
        
        {{assign var="dossier_medical" value=$selOp->_ref_sejour->_ref_dossier_medical}}
        <tr id="four">
         <th class="category" style="vertical-align: middle">
           Séjour
         </th>
         <td class="text">
          
          <table>
           <tr>
           <td>
            <strong>Addictions significatifs</strong>

						<ul>
						{{if $dossier_medical->_ref_addictions}}
						  {{foreach from=$dossier_medical->_ref_types_addiction key=curr_type item=list_addiction}}
						  {{if $list_addiction|@count}}
						  <li>
						    {{tr}}CAddiction.type.{{$curr_type}}{{/tr}}
						    {{foreach from=$list_addiction item=curr_addiction}}
						    <ul>
						      <li>
						        {{mb_field object=$curr_addiction field="addiction_id" hidden=1 prop=""}}
						        {{$curr_addiction->addiction}}
						      </li>
						    </ul>
						    {{/foreach}}
						  </li>
						  {{/if}}
						  {{/foreach}}
						{{else}}
						  <li><em>Pas d'addictions</em></li>
						{{/if}}
						</ul>

						<strong>Antécédents significatifs</strong>
						<ul>
						  {{foreach from=$dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
						  {{if $list_antecedent|@count}}
						  <li>
						    {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
						    {{foreach from=$list_antecedent item=curr_antecedent}}
						    <ul>
						      <li>
						      {{if $curr_antecedent->date}}
						          {{$curr_antecedent->date|date_format:"%d/%m/%Y"}} :
						        {{/if}}
						        {{$curr_antecedent->rques}}
						      </li>
						    </ul>
						    {{/foreach}}
						  </li>
						  {{/if}}
						  {{foreachelse}}
						  <li><em>Pas d'antécédents</em></li>
						  {{/foreach}}
						</ul>
			  
						<strong>Traitements significatifs</strong>
						<ul>
						  {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
						  <li>
						    {{if $curr_trmt->fin}}
						      Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}} :
						    {{elseif $curr_trmt->debut}}
						      Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} :
						    {{/if}}
						    {{$curr_trmt->traitement}}
						  </li>
						  {{foreachelse}}
						  <li><em>Pas de traitements</em></li>
						  {{/foreach}}
						</ul>
			
						<strong>Diagnostics significatifs de l'opération</strong>
						<ul>
						  {{foreach from=$dossier_medical->_ext_codes_cim item=curr_code}}
						  <li>
						    {{$curr_code->code}}: {{$curr_code->libelle}}
						  </li>
						  {{foreachelse}}
						  <li><em>Pas de diagnostic</em></li>
						  {{/foreach}}
						</ul>
       
			     </td>
			     <td>
			     <form name="newDocumentFrm" action="?m={{$m}}" method="post">
            <table>
            <tr>
			     <td>
				     <select name="_choix_modele" onchange="if (this.value) createDocument(this.value, {{$selOp->_id}})">
	             <option value="">&mdash; Choisir un modèle</option>
	             <optgroup label="Opération">
	             {{foreach from=$crList item=curr_cr}}
	             <option value="{{$curr_cr->compte_rendu_id}}">{{$curr_cr->nom}}</option>
	             {{/foreach}}
	             </optgroup>
	             <optgroup label="Hospitalisation">
	             {{foreach from=$hospiList item=curr_hospi}}
	             <option value="{{$curr_hospi->compte_rendu_id}}">{{$curr_hospi->nom}}</option>
	             {{/foreach}}
	             </optgroup>
	           </select>
	           <br />
	           <select name="_choix_pack" onchange="if (this.value) createPack(this.value, {{$selOp->_id}})">
	             <option value="">&mdash; {{tr}}pack-choice{{/tr}}</option>
	             {{foreach from=$packList item=curr_pack}}
	               <option value="{{$curr_pack->pack_id}}">{{$curr_pack->nom}}</option>
	             {{foreachelse}}
	               <option value="">{{tr}}pack-none{{/tr}}</option>
	             {{/foreach}}
	           </select>
	         </td>
         </tr>
        </table>
       </form>
       
       <!-- Affichage de la liste des documents de l'operation -->
       <div id="documents">
       
       </div>
       </td>
			     
			</tr>
	   </table>  
     </tr>
        
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
  