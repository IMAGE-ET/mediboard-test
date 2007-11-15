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
    	{{$patient->nom|json}}, 
    	{{$patient->prenom|json}}, 
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
    	{{$praticien->_user_first_name|json}}, 
    	{{$praticien->_user_last_name|json}},
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
    var oFSE = Intermax.oContent.FSE;
  	var msg = printf("Vous venez d'associer la FSE %s à cette consultation",
  		oFSE.FSE_NUMERO_FSE);
    submitFdr(document.BindFSE);
  }
} );

Intermax.ResultHandler["Consulter Vitale"] = Intermax.ResultHandler["Lire Vitale"];


function cancelTarif() {
  var oForm = document.tarifFrm;
  oForm.secteur1.value = 0;
  oForm.secteur2.value = 0;
  oForm.tarif.value = "";
  oForm.paye.value = "0";
  oForm.date_paiement.value = "";
 
  submitFdr(oForm);
}

function popFile(objectClass, objectId, elementClass, elementId) {
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, 0);
}


function modifTarif() {
  var oForm = document.tarifFrm;
  var choix = oForm.choix.value;
  
  // tarif_array: secteur1 secteur2 codes_ccam
  if(choix != ''){
    var tarif_array = choix.split(" ");
    var secteur1 = tarif_array[0];
    var secteur2 = tarif_array[1];
    var codes_ccam = tarif_array[2];
  
    oForm.secteur1.value = tarif_array[0];
    oForm.secteur2.value = tarif_array[1];
    oForm._newCode.value = codes_ccam;
  
    oForm._somme.value = parseFloat(tarif_array[0]) + parseFloat(tarif_array[1]); 
    
  
    var aCCAM = oForm.codes_ccam.value.split("|");
    // Si la chaine est vide, il crée un tableau à un élément vide donc :
    aCCAM.removeByValue("");
    if(oForm._newCode.value != ''){
      aCCAM.push(oForm._newCode.value);
    }
    aCCAM.sort();
    oForm.codes_ccam.value = aCCAM.join("|"); 
    
    
    for (i = 0;i < oForm.choix.length;++i)
      if(oForm.choix.options[i].selected == true)
       oForm.tarif.value = oForm.choix.options[i].text;
         
  } 
  else {
    oForm.secteur1.value = 0;
    oForm.secteur2.value = 0; 
    oForm._somme.value = '';
    oForm.tarif.value = '';
  }
}


function effectuerReglement() {
  var oForm = document.tarifFrm;
  oForm.paye.value = "1";
  oForm.date_paiement.value = new Date().toDATE();
  submitFdr(oForm);
}

function putTiers() {
  var form = document.tarifFrm;
  form.type_tarif.value = form._tiers.checked ? "tiers" : "";
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
  loadActes({{$consult->_id}}, {{$userSel->_id}});
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
      </form>
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
    <th class="category">Règlement</th>
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
			      <input type="hidden" name="_bind_fse" value="1" />
			      {{mb_field object=$consult field="consultation_id" hidden="1"}}
		      
			      </form>
						
						<!-- Les FSE déjà associées -->
		        <ul>
		          {{foreach from=$consult->_ids_fse item=_id_fse}}
		          <li>FSE numéro {{$_id_fse}}</li>
		          {{foreachelse}}
		          <li><em>Aucune FSE formatée</em></li>
		          {{/foreach}}
		        </ul>
		      {{/if}}
          </td>
        </tr>

        <tr>
		      {{if $patient->_id_vitale && $praticien->_id_cps}}
          <td class="button">
			      <button class="new" type="button" onclick="Intermax.Triggers['Formater FSE']('{{$praticien->_id_cps}}', '{{$patient->_id_vitale}}');">
			        Formater FSE
			      </button>
			      <button class="tick" type="button" onclick="Intermax.result('Formater FSE');">
			        Récupérer FSE
			      </button>
          </td>
          {{/if}}
        </tr>
      </table>
    </td>
	  {{/if}}

    <!-- Règlements -->  
    <td>
      <form name="tarifFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
     
      <table class="form">
        {{if !$consult->tarif}}
        <tr>
          <th><label for="choix" title="Type de tarif pour la consultation. Obligatoire.">Choix du tarif</label></th>
          <td>
            <select name="choix"  class="notNull str" onchange="modifTarif();">
              <option value="" selected="selected">&mdash; Choix du tarif</option>
              {{if $tarifsChir|@count}}
              <optgroup label="Tarifs praticien">
              {{foreach from=$tarifsChir item=curr_tarif}}
                <option value="{{$curr_tarif->secteur1}} {{$curr_tarif->secteur2}} {{$curr_tarif->codes_ccam}}">{{$curr_tarif->_view}}</option>
                
              {{/foreach}}
              </optgroup>
              {{/if}}
              {{if $tarifsCab|@count}}
              <optgroup label="Tarifs cabinet">
              {{foreach from=$tarifsCab item=curr_tarif}}
                <option value="{{$curr_tarif->secteur1}} {{$curr_tarif->secteur2}} {{$curr_tarif->codes_ccam}}">{{$curr_tarif->_view}}</option>
              {{/foreach}}
              </optgroup>
              {{/if}}
            </select>
          </td>
        </tr>
        {{/if}}
        
        {{if $consult->paye == "0"}}
        <tr>
          <th>{{mb_label object=$consult field="_somme"}}</th>
          <td>
            <input type="text" size="4" name="_somme" class="notNull currency" value="{{$consult->secteur1+$consult->secteur2}}" /> &euro;
            {{mb_field object=$consult field="secteur1" hidden=1 prop=""}}
            {{mb_field object=$consult field="secteur2" hidden=1 prop=""}}
            {{mb_field object=$consult field="tarif" hidden=1 prop=""}}
            <input type="hidden" name="paye" value="0" />
            <input type="hidden" name="date_paiement" value="" />
            
           </td>
        </tr>
        <tr>
          <td>{{mb_field object=$consult field="codes_ccam" hidden=1 prop=""}}</td>
          <td><input type="hidden" name="_newCode" /></td>
        </tr>
        {{else}}
        <tr>
          <td colspan="2" class="button">
            {{mb_field object=$consult field="secteur1" hidden=1 prop=""}}
            {{mb_field object=$consult field="secteur2" hidden=1 prop=""}}
            {{mb_field object=$consult field="tarif" hidden=1 prop=""}}
            {{mb_field object=$consult field="paye" hidden=1 prop=""}}
            {{mb_field object=$consult field="date_paiement" hidden=1 prop=""}}
            <strong>{{$consult->secteur1+$consult->secteur2}} &euro; ont été réglés : {{$consult->type_tarif}}</strong>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button class="cancel" type="button" onclick="cancelTarif()">Annuler</button>
          </td>
        </tr>
        {{/if}}
        
        {{if $consult->tarif && $consult->paye == "0"}}
        <tr>
          <th>
            {{mb_label object=$consult field="type_tarif"}}
          </th>
          <td>
            {{mb_field object=$consult field="type_tarif"}}
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
             <option value="{{$banque->_id}}" {{if $consult->banque_id == $banque->_id}}selectd = "selected"{{/if}}>{{$banque->_view}}</option>
           {{/foreach}}
           </select>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button class="submit" type="button" onclick="effectuerReglement()">Règlement effectué</button>
            <button class="cancel" type="button" onclick="cancelTarif()">Annuler</button>
          </td>
        </tr>
        {{elseif $consult->paye == "0"}}
        <tr>
          <th><label for="_tiers" title="Le règlement s'effectue par tiers-payant">Tiers-payant ?</label></th>
          <td>
            <input type="checkbox" name="_tiers" onchange="putTiers()" />
            <input type="hidden" name="type_tarif" value="" />
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button class="submit" type="button" onclick="submitFdr(this.form)">Valider ce tarif</button>
            <button class="cancel" type="button" onclick="cancelTarif()">Annuler</button>
          </td>
        </tr>
        {{/if}}
      </table>
      
      </form>
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
      <button class="tick" type="button" onclick="Intermax.result('Lire CPS');">
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
      <button class="tick" type="button" onclick="Intermax.result();">
        Associer Vitale
      </button>
    </td>

  </tr>
  {{/if}}
  {{/if}}
  
</table>
          
