{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

<script type="text/javascript">

Object.extend(Intermax.ResultHandler, {
  "Lire Vitale": function() {
    var oVitale = Intermax.oContent.VITALE;
    
    var msg = {{$patient->_id_vitale|json}} ?
    	"Vous êtes sur le point de mettre à jour le patient" :
    	"Vous êtes sur le point d'associer le patient";
    msg += printf("\n\t%s %s (%s)", 
    	'{{$patient->nom|smarty:nodefaults|JSAttribute}}', 
    	'{{$patient->prenom|smarty:nodefaults|JSAttribute}}', 
    	'{{mb_value object=$patient field=naissance}}');
    msg += "\nAvec le bénéficiaire Vitale";
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
    	"Vous êtes sur le point de mettre à jour le praticien" :
    	"Vous êtes sur le point d'associer le pratcien";
    msg += printf("\n\t%s %s (%s)", 
    	'{{$praticien->_user_first_name|smarty:nodefaults|JSAttribute}}', 
    	'{{$praticien->_user_last_name|smarty:nodefaults|JSAttribute}}', 
    	'{{mb_value object=$praticien field=adeli}}');
    msg += "\nAvec la Carte Professionnelle de Santé de";
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
  
  oForm.patient_regle.value = "0";
  
  oForm.date_paiement.value = "";  
  submitFdr(oForm);
}


function validTarif(){
  var oForm = document.tarifFrm;
  
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
  oForm.a_regler.value = oForm._somme.value; 
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
  oForm.a_regler.value = somme;
  oForm.secteur2.value = parseFloat(somme) - parseFloat(secteur1); 
  oForm.secteur2.value = Math.round(oForm.secteur2.value*100)/100;
}

function effectuerReglement() {
  // passage de patient_regle a 1
  var oForm = document.tarifFrm;
  oForm.patient_regle.value = "1";
  oForm.date_paiement.value = new Date().toDATE();
  /*
  var secteur1 = oForm.secteur1.value;
  var secteur2 = oForm.secteur2.value;
  var somme = parseFloat(secteur1) + parseFloat(secteur2);
  var a_regler = oForm.a_regler.value;
  */
  // Si le total de la facture est egal au reglement patient, facture acquitte
  /*
  if(somme == a_regler){
    oForm.facture_acquittee.value = "1";
  }
  */
  submitFdr(oForm);
}

function putTiers() {
  var form = document.tarifFrm;
  form.mode_reglement.value = "tiers";
}

function editDocument(compte_rendu_id) {
  var url = new Url;
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("compte_rendu_id", compte_rendu_id);
  url.popup(700, 700, "Document");
}

function loadExam(sValue){
  var oForm = document.newExamen;
  oForm.type_examen.value = sValue;
  oForm.type_examen.onchange();
}

function newExam(oSelect, consultation_id) {
  if (sAction = oSelect.value) {
    var url = new Url;
    url.setModuleAction("dPcabinet", sAction);
    url.addParam("consultation_id", consultation_id);
    url.popup(900, 600, "Examen"); 
  }

  oSelect.value = ""; 
}

function reloadFdr() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_fdr_consult");

  {{if $noReglement}}
  url.addParam("noReglement", "1"); 
  {{/if}}

  url.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  url.requestUpdate('fdrConsultContent', { waitingText : null });
  
  {{if $app->user_prefs.ccam}} 
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
    <th class="category">Fichiers liés</th>
    <th class="category">Documents</th>
  </tr>
  <tr>

    <!-- Files -->

    <td class="text">
      <form name="newExamen" action="?m=dPcabinet">

      <label for="type_examen" title="Type d'examen complémentaire à effectuer"><strong>Examens complémentaires</strong></label>
      <select name="type_examen" onchange="newExam(this, {{$consult->consultation_id}})">
        <option value="">&mdash; Choisir un type d'examen</option>
        {{if $_is_anesth}}
          <option value="exam_possum">Score Possum</option>
          <option value="exam_nyha">Classification NYHA</option>
        {{else}}
          <option value="exam_audio">Audiogramme</option>          
        {{/if}}
      </select>

      </form>

      <ul>
        {{if !$consult->_ref_examaudio->_id && !$consult->_ref_examnyha->_id && !$consult->_ref_exampossum->_id}}
        <li>
          Aucun examen
        </li>
        {{/if}}
        {{if $consult->_ref_examaudio->_id}}
        <li>    
          <a href="#nothing" onclick="loadExam('exam_audio');">Audiogramme</a>
          <form name="delFrm{{$consult->_ref_examaudio->_id}}" action="?m=dPcabinet" enctype="multipart/form-data" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="dosql" value="do_exam_audio_aed" />
            <input type="hidden" name="del" value="1" />
            {{mb_field object=$consult->_ref_examaudio field="_view" hidden=1 prop=""}}
            {{mb_field object=$consult->_ref_examaudio field="examaudio_id" hidden=1 prop=""}}
            <input type="hidden" name="_conduction" value="" />
            <input type="hidden" name="_oreille" value="" />
            <button class="trash notext" type="button" onclick="confirmFileDeletion(this)">{{tr}}Delete{{/tr}}</button>
          </form>
        </li>
        {{/if}}
        {{if $consult->_ref_exampossum->_id}}
        <li>
          <a href="#nothing" onclick="loadExam('exam_possum');">
            {{$consult->_ref_exampossum->_view}}
          </a>
        </li>
        {{/if}}
        {{if $consult->_ref_examnyha->_id}}
        <li>
          <a href="#nothing" onclick="loadExam('exam_nyha');">
            {{$consult->_ref_examnyha->_view}}
          </a>
        </li>
        {{/if}}
      </ul>

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
    <table class="form">
      {{foreach from=$consult->_ref_documents item=document}}
      <tr>
        <th>{{$document->nom}}</th>
        <td class="button">
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
        </td>
      </tr>
      {{/foreach}}
    </table>
    
    <form name="newDocumentFrm" action="?m={{$m}}" method="post">
    <table class="form">
      <tr>
        <td>
          {{if $consult->_ref_consult_anesth->consultation_anesth_id}}
          <select name="_choix_modele" onchange="createDocument(this, {{$consult->_ref_consult_anesth->consultation_anesth_id}})">
          {{else}}
          <select name="_choix_modele" onchange="createDocument(this, {{$consult->consultation_id}})">
          {{/if}}           
            <option value="">&mdash; Choisir un modèle</option>
            {{if $listModelePrat|@count}}
            <optgroup label="Modèles du praticien">
              {{foreach from=$listModelePrat item=curr_modele}}
              <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
              {{/foreach}}
            </optgroup>
            {{/if}}
            {{if $listModeleFunc|@count}}
            <optgroup label="Modèles du cabinet">
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
    
    </td>
	</tr>

  {{if !$noReglement}}
  {{assign var=gestionFSE value=$app->user_prefs.GestionFSE}}
	<tr>
	  {{if $gestionFSE}}
    <th class="category">Feuille de Soins</th>
	  {{/if}}
    <th {{if !$gestionFSE}}colspan="2"{{/if}} class="category">Règlement</th>
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
			        Professionnel de Santé ou Bénéficiaire Vitale non identifié
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
				
				<!-- Les FSE déjà associées -->
        {{foreach from=$consult->_ext_fses key=_id_fse item=_ext_fse}}
				<tr>
				  <td>
				  	<label onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CLmFSE', object_id: '{{$_id_fse}}' } })">
				  	  {{$_ext_fse->_view}}
				  	</label>
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
				    <em>Aucune FSE associée</em>
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
			        Mettre à jour FSE
			      </button>
          </td>
          {{/if}}
        </tr>
      </table>
    </td>
	  {{/if}}

    <!-- Règlements -->  
    <td {{if !$gestionFSE}}colspan="2"{{/if}}>
      
      
      <!-- Formulaire de selection de tarif -->
      <form name="selectionTarif" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

	      <input type="hidden" name="m" value="{{$m}}" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="dosql" value="do_consultation_aed" />
        <input type="hidden" name="_delete_actes" value="1" />
	      <input type="hidden" name="_bind_tarif" value="1" />
	      {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
	     
	      <table class="form">
	        {{if !$consult->tarif}}
	        <tr>
	          <th><label for="choix" title="Type de tarif pour la consultation. Obligatoire.">Choix du tarif</label></th>
	          <td>
	            <select name="_tarif_id"  class="notNull str" onchange="submitFormAjax(this.form, 'systemMsg', { onComplete : reloadFdr } );">
	              <option value="" selected="selected">&mdash; Choix du tarif</option>
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

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
     {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
      
      <table width="100%">  
        {{if $consult->patient_regle == "0"}}
       
        <tr>          
          <th>{{mb_label object=$consult field="_somme"}}</th>
          <td>
            {{mb_field object=$consult field="tarif" hidden=1 prop=""}}
            <input type="hidden" name="patient_regle" value="0" />
            <input type="hidden" name="date_paiement" value="" />
       
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
        </tr>
        {{else}}
        <tr>
          <td colspan="2" class="button">
            {{mb_field object=$consult field="secteur1" hidden=1 prop=""}}
            {{mb_field object=$consult field="secteur2" hidden=1 prop=""}}
            {{mb_field object=$consult field="tarif" hidden=1 prop=""}}
            {{mb_field object=$consult field="a_regler" hidden=1 prop=""}}
            {{mb_field object=$consult field="patient_regle" hidden=1 prop=""}}
            {{mb_field object=$consult field="date_paiement" hidden=1 prop=""}}
            <strong>{{$consult->a_regler}} &euro; ont été réglés par le patient: {{mb_value object=$consult field="mode_reglement"}}</strong>
          </td>
        </tr>
        <tr>
          <!-- Suppression des actes associées a la consultation -->
          <td colspan="2" class="button">
            <input type="hidden" name="tarif" value="{{$consult->tarif}}" />
            <input type="hidden" name="facture_acquittee" value="0" />
            <button class="cancel" type="button" onclick="cancelTarif()">Annuler le réglement</button>
          </td>
        </tr>
        {{/if}}
        
        {{if $consult->tarif && $consult->patient_regle == "0" && $consult->valide == "1"}}
        <tr>
          <th>
            {{mb_label object=$consult field="mode_reglement"}}
          </th>
          <td>
            {{mb_field object=$consult field="mode_reglement"}}
          </td>
        </tr>
        <tr>
          <th>
            {{mb_label object=$consult field="a_regler"}}
          </th>
          <td>
            {{mb_value object=$consult field="a_regler"}}
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
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="valide" value="1" />
            <input type="hidden" name="secteur1" value="{{$consult->secteur1}}" />
            <input type="hidden" name="secteur2" value="{{$consult->secteur2}}" />
            <input type="hidden" name="a_regler" value="{{$consult->a_regler}}" />
            <!-- 
            <input type="hidden" name="facture_acquittee" value="" />
             -->
            <button class="submit" type="button" onclick="effectuerReglement()">Règlement effectué</button>
            {{if !$consult->_current_fse}}
            <button class="cancel" type="button" onclick="cancelTarif()">Annuler la validation</button>
            {{/if}}
          </td>
        </tr>
        {{elseif $consult->patient_regle == "0"}}
        <tr>
          <th>{{mb_label object=$consult field="a_regler"}}</th>
          <td>
            {{mb_field object=$consult field="a_regler"}}
            <button type="button" class="tick" onclick="putTiers(); this.form.a_regler.value = 0">Tiers-payant total</button>   
            <input type="hidden" name="mode_reglement" value="" />
            <input type="hidden" name="valide" value="1" />
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
          <input type="hidden" name="_delete_actes" value="0" />
            <button class="submit" type="button" onclick="validTarif();">Valider ce tarif</button>
            <button class="cancel" type="button" onclick="cancelTarif('delActes')">Annuler le tarif</button>
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
        <button class="submit" type="submit">Créer un nouveau tarif</button>
      </form>
      {{/if}}
      
    </td>
  </tr>
  

  {{if $gestionFSE}}
  <!-- Patient Vitale et Professionnel de Santé -->
  <tr>
    <th class="category">Professionnel de santé</th>
    <th class="category">Patient Vitale</th>
  </tr>
  
  <tr>

    <!-- Professionnel de santé -->
    <td class="text">
      <form name="BindCPS" action="?m={{$m}}" method="post">

      <input type="hidden" name="m" value="mediusers" />
      <input type="hidden" name="dosql" value="do_mediusers_aed" />
      <input type="hidden" name="_bind_cps" value="1" />
      {{mb_field object=$praticien field="user_id" hidden="1"}}
      
      </form>
    
      {{if !$praticien->_id_cps}}
      <div class="warning">
        Praticien non associé à une CPS. 
        <br/>
        Merci d'effectuer une lecture de la CPS pour permettre le formatage d'une FSE. 
      </div>
      {{else}}
      <div class="message">
        Praticien correctement associé à une CPS. 
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
        Patient non associé à un bénéficiaire Vitale. 
        <br/>
        Merci d'éffectuer une lecture de la carte pour permettre le formatage d'une FSE. 
      </div>
      {{else}}
      <div class="message">
        Patient correctement associé à un bénéficiaire Vitale. 
        <br/>
        Formatage des FSE disponible pour ce patient.
      </div>
      {{/if}}
    </td>
    
  </tr> 
  
  <tr>

    <!-- Professionnel de santé -->
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
          
