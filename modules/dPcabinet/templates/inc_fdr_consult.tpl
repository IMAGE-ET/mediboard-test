{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

<script type="text/javascript">

Object.extend(Intermax.ResultHandler, {
  "Lire Vitale": function() {
    var oVitale = Intermax.oContent.VITALE;
    
    var msg = {{$patient->_id_vitale|json}} ?
    	"Vous �tes sur le point de mettre � jour le patient" :
    	"Vous �tes sur le point d'associer le patient";
    msg += printf("\n\t%s %s (%s)",
    	'{{$patient->nom|smarty:nodefaults|JSAttribute}}', 
    	'{{$patient->prenom|smarty:nodefaults|JSAttribute}}', 
    	'{{mb_value object=$patient field=naissance}}');
    msg += "\nAvec le b�n�ficiaire Vitale";
    msg += printf("\n\t%s %s (%s)", 
    	oVitale.VIT_NOM, 
    	oVitale.VIT_PRENOM, 
    	oVitale.VIT_DATE_NAISSANCE);
    msg += "\n\nVoulez-vous continuer ?";
        
    if (confirm(msg)) {
      submitFdr(document.BindVitale);
    }
  },
  
  "Lire CPS": function() {
    var oCPS = Intermax.oContent.CPS;
    
    var msg = {{$praticien->_id_cps|json}} ?
    	"Vous �tes sur le point de mettre � jour le praticien" :
    	"Vous �tes sur le point d'associer le pratcien";
    msg += printf("\n\t%s %s (%s)", 
    	'{{$praticien->_user_first_name|smarty:nodefaults|JSAttribute}}', 
    	'{{$praticien->_user_last_name|smarty:nodefaults|JSAttribute}}', 
    	'{{mb_value object=$praticien field=adeli}}');
    msg += "\nAvec la Carte Professionnelle de Sant� de";
    msg += printf("\n\t%s %s (%s)", 
    	oCPS.CPS_PRENOM,
    	oCPS.CPS_NOM,
    	oCPS.CPS_ADELI_NUMERO_CPS);
    msg += "\n\nVoulez-vous continuer ?";

    if (confirm(msg)) {
      submitFdr(document.BindCPS);
    }
  },

  "Formater FSE": function() {
    submitFdr(document.BindFSE);
  },

  "Annuler FSE": function() {
    reloadFdr();
  }  
} );

Intermax.ResultHandler["Consulter Vitale"] = Intermax.ResultHandler["Lire Vitale"];
Intermax.ResultHandler["Consulter FSE"] = Intermax.ResultHandler["Formater FSE"];

// Use single quotes or fails ?!!
Intermax.Triggers['Formater FSE'].aActes = {{$consult->_fse_intermax|@json}};





function cancelTarif(action) {
  var oForm = document.tarifFrm;
  
  if(action == "delActes"){
    oForm._delete_actes.value = 1; 
    oForm.tarif.value = "";
  }
  
  if(oForm.valide){
    oForm.valide.value = 0;
  }
  
  if(oForm._somme){
    oForm._somme.value = 0;
  }
  
  // On met � 0 les valeurs de tiers 
  if(oForm.tiers_date_reglement){
    oForm.tiers_date_reglement.value = "";
    oForm.tiers_mode_reglement.value = "";
  }
 
  if(oForm.patient_mode_reglement){
    oForm.patient_mode_reglement.value = "";
  }
  oForm.patient_date_reglement.value = "";
  
  submitFdr(oForm);
}


function validTarif(){
  var oForm = document.tarifFrm;
  
  if(oForm.du_tiers){
    oForm.du_tiers.value = oForm._somme.value - oForm.du_patient.value;
  }
  
  if(oForm.tarif.value == ""){
    oForm.tarif.value = "manuel";
    if(oForm._tokens_ccam.value){
      oForm.tarif.value += " / "+oForm._tokens_ccam.value;
    }
    if(oForm._tokens_ngap.value){
      oForm.tarif.value += " / "+oForm._tokens_ngap.value;
    }
  }
  submitFdr(oForm);
}



function popFile(objectClass, objectId, elementClass, elementId) {
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, 0);
}

function modifTotal(){
  var oForm = document.tarifFrm;
  var secteur1 = oForm.secteur1.value;
  var secteur2 = oForm.secteur2.value;
  if(secteur1 == ""){
    secteur1 = 0;
  }
  if(secteur2 == ""){
    secteur2 = 0;
  }
  oForm._somme.value = parseFloat(secteur1) + parseFloat(secteur2);
  oForm._somme.value = Math.round(oForm._somme.value*100)/100;
  oForm.du_patient.value = oForm._somme.value; 
}


function modifSecteur2(){
  var oForm = document.tarifFrm;
  var secteur1 = oForm.secteur1.value;
  var somme = oForm._somme.value;
  
  if(somme == ""){
    somme = 0;
  }
  if(secteur1 == ""){
    secteur = 0;
  }
  oForm.du_patient.value = somme;
  oForm.secteur2.value = parseFloat(somme) - parseFloat(secteur1); 
  oForm.secteur2.value = Math.round(oForm.secteur2.value*100)/100;
}

function effectuerReglement() {
  var oForm = document.tarifFrm;
  oForm.patient_date_reglement.value = new Date().toDATE();
  submitFdr(oForm);
}

function putTiers() {
  var oForm = document.tarifFrm;
  oForm.du_patient.value = 0;
}

function editDocument(compte_rendu_id) {
  var url = new Url;
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("compte_rendu_id", compte_rendu_id);
  url.popup(700, 700, "Document");
}


function reloadFdr() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_fdr_consult");

  {{if $noReglement}}
  url.addParam("noReglement", "1"); 
  {{/if}}

  url.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  url.requestUpdate('fdrConsultContent', { waitingText : null });
  
  {{if $app->user_prefs.ccam_consultation}} 
  // rafraichissement de la div ccam
  ActesCCAM.refreshList({{$consult->_id}}, {{$userSel->_id}});
  ActesNGAP.refreshList();
  {{/if}} 
}

function reloadAfterSaveDoc(){
  reloadFdr();
}

function reloadAfterUploadFile(){
  reloadFdr();
}

function confirmFileDeletion(oButton) {
  oOptions = {
    typeName: 'le fichier',
    objName: oButton.form._view.value,
    ajax: 1,
    target: 'systemMsg'
  };
  
  oAjaxOptions = {
    onComplete: reloadFdr
  }
  
  confirmDeletion(oButton.form, oOptions, oAjaxOptions);
}

function submitFdr(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadFdr });
}



</script>


<table class="form">
  <tr>
    <th class="category">Fichiers li�s</th>
    <th class="category">Documents</th>
  </tr>
  <tr>

    <!-- Files -->

    <td class="text">
      <div id="exam_comp">
      {{include file="../../dPcabinet/templates/inc_examens_comp.tpl"}}
      </div>
      
      <strong>Fichiers</strong>
			<ul>
			  {{foreach from=$consult->_ref_files item=curr_file}}
			  <li>
			    <form name="delFrm{{$curr_file->file_id}}" action="?m=dPcabinet" enctype="multipart/form-data" method="post" onsubmit="return checkForm(this)">
			      <a href="#" onclick="popFile('{{$consult->_class_name}}','{{$consult->_id}}','{{$curr_file->_class_name}}','{{$curr_file->_id}}');">{{$curr_file->file_name}}</a>
			      ({{$curr_file->_file_size}})
			      <input type="hidden" name="m" value="dPfiles" />
			      <input type="hidden" name="dosql" value="do_file_aed" />
			      <input type="hidden" name="del" value="1" />
			      {{mb_field object=$curr_file field="file_id" hidden=1 prop=""}}
			      {{mb_field object=$curr_file field="_view" hidden=1 prop=""}}
			      <button class="trash notext" type="button" onclick="confirmFileDeletion(this)">{{tr}}Delete{{/tr}}</button>
			    </form>
			  </li>
			  {{foreachelse}}
			    <li>Aucun fichier disponible</li>
			  {{/foreach}}
			</ul>    
      <button class="new" type="button" onclick="uploadFile('CConsultation', {{$consult->consultation_id}}, '')">
        Ajouter un fichier
      </button>
    </td>

    <!-- Documents -->

    <td>
    
    <form name="newDocumentFrm" action="?m={{$m}}" method="post">
    <table class="form">
      <tr>
        <td>
          {{if $consult->_ref_consult_anesth->consultation_anesth_id}}
          <select name="_choix_modele" onchange="createDocument(this, {{$consult->_ref_consult_anesth->consultation_anesth_id}})">
          {{else}}
          <select name="_choix_modele" onchange="createDocument(this, {{$consult->consultation_id}})">
          {{/if}}           
            <option value="">&mdash; Choisir un mod�le</option>
            {{if $listModelePrat|@count}}
            <optgroup label="Mod�les du praticien">
              {{foreach from=$listModelePrat item=curr_modele}}
              <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
              {{/foreach}}
            </optgroup>
            {{/if}}
            {{if $listModeleFunc|@count}}
            <optgroup label="Mod�les du cabinet">
              {{foreach from=$listModeleFunc item=curr_modele}}
              <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
              {{/foreach}}
            </optgroup>
            {{/if}}
          </select>
        </td>
      </tr>
    </table>
    </form>
    
    <ul>
      {{foreach from=$consult->_ref_documents item=document}}
      <li>
        <form name="editDocumentFrm{{$document->compte_rendu_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPcompteRendu" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_modele_aed" />
        {{if $consult->_ref_consult_anesth->consultation_anesth_id}}
        <input type="hidden" name="object_id" value="{{$consult->_ref_consult_anesth->consultation_anesth_id}}" />
        {{else}}
        <input type="hidden" name="object_id" value="{{$consult->consultation_id}}" />
        {{/if}}          
        {{mb_field object=$document field="compte_rendu_id" hidden=1 prop=""}}
        <button class="edit notext" type="button" onclick="editDocument({{$document->compte_rendu_id}})">{{tr}}Edit{{/tr}}</button>
        <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'le document',objName:'{{$document->nom|smarty:nodefaults|JSAttribute}}',ajax:1,target:'systemMsg'},{onComplete:reloadFdr})" >{{tr}}Delete{{/tr}}</button>
        </form>
        <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectViewHistory', params: { object_class: 'CCompteRendu', object_id: {{$document->_id}} } })">
          {{$document->nom}}
        </span>
      </li>
      {{/foreach}}
    </ul>
    
    </td>
	</tr>

  {{if !$noReglement}}
  {{assign var=gestionFSE value=$app->user_prefs.GestionFSE}}
	<tr>
	  {{if $gestionFSE}}
    <th class="category">Feuille de Soins</th>
	  {{/if}}
    <th {{if !$gestionFSE}}colspan="2"{{/if}} class="category">R�glement</th>
  </tr>
  
	<tr>	
	
	  {{if $gestionFSE}}
    <!-- Feuille de soins -->
    <td class="text">
      <table class="form">
        <tr>
          <td class="text">
			      {{if !$patient->_id_vitale || !$praticien->_id_cps}}
			      <div class="warning">
			        Professionnel de Sant� ou B�n�ficiaire Vitale non identifi�
			        <br/>
			        Merci d'associer la CPS et la carte Vitale pour permettre le formatage d'une FSE. 
			      </div>
			      {{else}}
			        
			      <form name="BindFSE" action="?m={{$m}}" method="post">
			
			      <input type="hidden" name="m" value="dPcabinet" />
			      <input type="hidden" name="dosql" value="do_consultation_aed" />
			      <input type="hidden" name="_delete_actes" value="1" />
			      <input type="hidden" name="_bind_fse" value="1" />
			      {{mb_field object=$consult field="consultation_id" hidden="1"}}
		      
			      </form>
			      {{/if}}
				  </td>
				</tr>
				
				<!-- Les FSE d�j� associ�es -->
        {{foreach from=$consult->_ext_fses key=_id_fse item=_ext_fse}}
				<tr>
				  <td>
				  	<span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CLmFSE', object_id: '{{$_id_fse}}' } })">
				  	  {{$_ext_fse->_view}}
				  	</span>
		      </td>
	        {{if $_ext_fse->_annulee}}
	        <td class="cancelled">
	          {{mb_value object=$_ext_fse field=S_FSE_ETAT}}
	        </td>
		      {{else}}
		      <td class="button">
			      <button class="search" type="button" onclick="Intermax.Triggers['Consulter FSE']('{{$_id_fse}}');">
			        Consulter 
			      </button>
			      <button class="print" type="button" onclick="Intermax.Triggers['Editer FSE']('{{$_id_fse}}');">
			        Imprimer
			      </button>
			      <button class="cancel" type="button" onclick="Intermax.Triggers['Annuler FSE']('{{$_id_fse}}');">
			        Annuler
			      </button>
		      </td>
		      {{/if}}
		    </tr>
        {{foreachelse}}
				<tr>
				  <td colspan="2">
				    <em>Aucune FSE associ�e</em>
		      </td>
		    </tr>
        {{/foreach}}

        <tr>
		      {{if $patient->_id_vitale && $praticien->_id_cps}}
          <td colspan="2" class="button">
            {{if !$consult->_current_fse}}
			      <button class="new" type="button" onclick="Intermax.Triggers['Formater FSE']('{{$praticien->_id_cps}}', '{{$patient->_id_vitale}}');">
			        Formater FSE
			      </button>
			      {{/if}}
			      <button class="change" type="button" onclick="Intermax.result(['Formater FSE', 'Consulter FSE', 'Annuler FSE']);">
			        Mettre � jour FSE
			      </button>
          </td>
          {{/if}}
        </tr>
      </table>
    </td>
	  {{/if}}

    <!-- R�glements -->  
    <td {{if !$gestionFSE}}colspan="2"{{/if}}>
      
      
      <!-- Formulaire de selection de tarif -->
      <form name="selectionTarif" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

	      <input type="hidden" name="m" value="dPcabinet" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="dosql" value="do_consultation_aed" />
        <input type="hidden" name="_delete_actes" value="1" />
	      <input type="hidden" name="_bind_tarif" value="1" />
	      {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
	     
	      <table class="form">
	        {{if !$consult->tarif}}
	        <tr>
	          <th><label for="choix" title="Type de cotation pour la consultation. Obligatoire.">Cotation</label></th>
	          <td>
	            <select name="_tarif_id"  class="notNull str" onchange="submitFormAjax(this.form, 'systemMsg', { onComplete : reloadFdr } );">
	              <option value="" selected="selected">&mdash; Choisir la cotation</option>
	              {{if $tarifsChir|@count}}
	              <optgroup label="Tarifs praticien">
	              {{foreach from=$tarifsChir item=curr_tarif}}
	                <option value="{{$curr_tarif->_id}}">{{$curr_tarif->_view}}</option>
	              {{/foreach}}
	              </optgroup>
	              {{/if}}
	              {{if $tarifsCab|@count}}
	              <optgroup label="Tarifs cabinet">
	              {{foreach from=$tarifsCab item=curr_tarif}}
	                <option value="{{$curr_tarif->_id}}">{{$curr_tarif->_view}}</option>
	              {{/foreach}}
	              </optgroup>
	              {{/if}}
	            </select>
	          </td>
	        </tr>
	        {{else}}
	        <tr>
	          <th>{{mb_label object=$consult field=tarif}}</th>
	          <td>{{mb_value object=$consult field=tarif}}</td>    
	        </tr>
	        {{/if}}
	      </table>
      </form>
      <hr />
      
      <form name="tarifFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
     {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
      
      
      <!-- Formulaire de reglement -->
      <table width="100%">  
        {{if !$consult->patient_date_reglement}}   
        <tr>          
          <th>{{mb_label object=$consult field="_somme"}}</th>
          <td>
            {{mb_field object=$consult field="tarif" hidden=1 prop=""}}
            <input type="hidden" name="patient_date_reglement" value="" />
            {{if $consult->valide}}
	          {{mb_value object=$consult field="secteur1"}} (S1) +
	          
	          {{mb_value object=$consult field="secteur2"}} (S2) =
 			      {{mb_value object=$consult field="_somme" value=$consult->secteur1+$consult->secteur2 onchange="modifSecteur2()"}}
            {{else}}
            {{mb_label object=$consult field="secteur1"}}
	          {{mb_field object=$consult field="secteur1" onchange="modifTotal()"}} +
	          {{mb_label object=$consult field="secteur2"}}
	          {{mb_field object=$consult field="secteur2" onchange="modifTotal()"}} =
 			      <input type="text" size="6" name="_somme" class="notNull currency" value="{{$consult->secteur1+$consult->secteur2}}" onchange="modifSecteur2()" /> &euro;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Codes CCAM</th>
          <td>{{mb_field object=$consult field="_tokens_ccam" readonly="readonly" hidden=1 prop=""}}
            {{foreach from=$consult->_ref_actes_ccam item="acte_ccam"}}
              {{$acte_ccam->code_acte}}  
            {{/foreach}}
          </td>
        </tr>
        <tr>
          <th>Codes NGAP</th>
          <td>{{mb_field object=$consult field="_tokens_ngap" readonly="readonly" hidden=1 prop=""}}
          {{foreach from=$consult->_ref_actes_ngap item=acte_ngap}}
            {{$acte_ngap->quantite}}-{{$acte_ngap->code}}-{{$acte_ngap->coefficient}}   
          {{/foreach}}
          </td>
        </tr>
        {{else}}
        <tr>
          <td colspan="2" class="button">
            {{mb_field object=$consult field="secteur1" hidden=1 prop=""}}
            {{mb_field object=$consult field="secteur2" hidden=1 prop=""}}
            {{mb_field object=$consult field="tarif" hidden=1 prop=""}}
            {{mb_field object=$consult field="du_patient" hidden=1 prop=""}}
            {{mb_field object=$consult field="du_tiers" hidden=1 prop=""}}
            
            {{mb_field object=$consult field="patient_date_reglement" hidden=1 prop=""}}
            {{mb_field object=$consult field="patient_mode_reglement" hidden=1 prop=""}}
            <strong>{{$consult->du_patient}} &euro; ont �t� r�gl�s par le patient: {{mb_value object=$consult field="patient_mode_reglement"}}</strong>
          </td>
        </tr>
        <tr>
          <!-- Suppression des actes associ�es a la consultation -->
          <td colspan="2" class="button">
            <input type="hidden" name="tarif" value="{{$consult->tarif}}" />
            <button class="cancel" type="button" onclick="cancelTarif()">Annuler le r�glement</button>
          </td>
        </tr>
        {{/if}}
        <!-- Fin du formulaire de reglement -->
        
        
        
        
        
        {{if $consult->tarif && $consult->patient_date_reglement == "" && $consult->valide == "1"}}
        {{if !$consult->sejour_id}}
        
        {{if $consult->du_patient}}
        <tr>
          <th>
            {{mb_label object=$consult field="patient_mode_reglement"}}
          </th>
          <td>
            {{mb_field object=$consult field="patient_mode_reglement" defaultOption="&mdash;Veuillez Choisir &mdash;"}}
          </td>
        </tr>
        <tr>
          <th>
            {{mb_label object=$consult field="du_patient"}}
          </th>
          <td>
            {{mb_value object=$consult field="du_patient"}}
          </td>
        </tr>
        <tr>
          <th>
           Banque
          </th>
          <td>
           <select name="banque_id">
           <option value="">&mdash; Choix d'une banque</option> 
           {{foreach from=$banques item=banque}}
             <option value="{{$banque->_id}}" {{if $consult->banque_id == $banque->_id}}selected = "selected"{{/if}}>{{$banque->_view}}</option>
           {{/foreach}}
           </select>
          </td>
        </tr>
        {{/if}}
        {{/if}}
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="valide" value="1" />
            <input type="hidden" name="secteur1" value="{{$consult->secteur1}}" />
            <input type="hidden" name="secteur2" value="{{$consult->secteur2}}" />
            <input type="hidden" name="du_patient" value="{{$consult->du_patient}}" />
            <input type="hidden" name="du_tiers" value="{{$consult->du_tiers}}" />
            
            <input type="hidden" name="tiers_date_reglement" value="{{$consult->tiers_date_reglement}}" />
            <input type="hidden" name="tiers_mode_reglement" value="{{$consult->tiers_mode_reglement}}" />
            {{if !$consult->sejour_id && $consult->du_patient}}
            <button class="submit" type="button" onclick="effectuerReglement()">R�glement effectu�</button>
            {{/if}}
            {{if !$consult->_current_fse}}
            <button class="cancel" type="button" onclick="this.form.du_tiers.value = 0; this.form.du_patient.value = 0; cancelTarif()">Annuler la validation</button>
            {{/if}}
          </td>
        </tr>
        {{elseif !$consult->patient_date_reglement}}
        {{if !$consult->sejour_id}}
        <tr>
          <th>{{mb_label object=$consult field="du_patient"}}</th>
          <td>
            {{mb_field object=$consult field="du_patient"}}
            {{mb_field object=$consult field="du_tiers" hidden="1"}}
            <button type="button" class="tick" onclick="putTiers();">Tiers-payant total</button>   
          </td>
        </tr>
        {{/if}}
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="_delete_actes" value="0" />
            <input type="hidden" name="valide" value="1" />
          
            <button class="submit" type="button" onclick="validTarif();">Valider la cotation</button>
            <button class="cancel" type="button" onclick="cancelTarif('delActes')">Annuler la cotation</button>
          </td>
        </tr>
        {{/if}}
      </table>
      
      </form>
      
      {{if $consult->valide}}
      <!-- Creation d'un nouveau tarif avec les actes NGAP de la consultation courante -->
      <form name="creerTarif" action="?m={{$m}}&amp;tab=vw_compta" method="post" style="float: right;">
        <input type="hidden" name="dosql" value="do_tarif_aed" />
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="_tab" value="vw_compta" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="_bind_consult" value="1" />
        <input type="hidden" name="_consult_id" value="{{$consult->_id}}" />
        <button class="submit" type="submit">Cr�er un nouveau tarif</button>
      </form>
      {{/if}}
      
    </td>
  </tr>
  

  {{if $gestionFSE}}
  <!-- Patient Vitale et Professionnel de Sant� -->
  <tr>
    <th class="category">Professionnel de sant�</th>
    <th class="category">Patient Vitale</th>
  </tr>
  
  <tr>

    <!-- Professionnel de sant� -->
    <td class="text">
      <form name="BindCPS" action="?m={{$m}}" method="post">

      <input type="hidden" name="m" value="mediusers" />
      <input type="hidden" name="dosql" value="do_mediusers_aed" />
      <input type="hidden" name="_bind_cps" value="1" />
      {{mb_field object=$praticien field="user_id" hidden="1"}}
      
      </form>
    
      {{if !$praticien->_id_cps}}
      <div class="warning">
        Praticien non associ� � une CPS. 
        <br/>
        Merci d'effectuer une lecture de la CPS pour permettre le formatage d'une FSE. 
      </div>
      {{else}}
      <div class="message">
        Praticien correctement associ� � une CPS. 
        <br/>
        Formatage des FSE disponible pour ce praticien.
      </div>
      {{/if}}
    </td>

    <!-- Patient Vitale -->
    <td class="text">
      <form name="BindVitale" action="?m={{$m}}" method="post">

      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="dosql" value="do_patients_aed" />
      <input type="hidden" name="_bind_vitale" value="1" />
      {{mb_field object=$patient field="patient_id" hidden="1"}}
      
      </form>
            
      {{if !$patient->_id_vitale}}
      <div class="warning">
        Patient non associ� � un b�n�ficiaire Vitale. 
        <br/>
        Merci d'�ffectuer une lecture de la carte pour permettre le formatage d'une FSE. 
      </div>
      {{else}}
      <div class="message">
        Patient correctement associ� � un b�n�ficiaire Vitale. 
        <br/>
        Formatage des FSE disponible pour ce patient.
      </div>
      {{/if}}
    </td>
    
  </tr> 
  
  <tr>

    <!-- Professionnel de sant� -->
    <td class="button">
      <button class="search" type="button" onclick="Intermax.trigger('Lire CPS');">
        Lire CPS
      </button>
      <button class="change" type="button" onclick="Intermax.result('Lire CPS');">
        Associer CPS
      </button>
    </td>

    <!-- Patient Vitale -->
    <td class="button">
      <button class="search" type="button" onclick="Intermax.trigger('Lire Vitale');">
        Lire Vitale
      </button>
      {{if $patient->_id_vitale}}
      <button class="search" type="button" onclick="Intermax.Triggers['Consulter Vitale']({{$patient->_id_vitale}});">
        Consulter Vitale
      </button>
      {{/if}}
      <button class="change" type="button" onclick="Intermax.result();">
        Associer Vitale
      </button>
    </td>

  </tr>
  {{/if}}
  {{/if}}
  
</table>
          
