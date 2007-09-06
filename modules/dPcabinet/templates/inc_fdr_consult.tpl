<script type="text/javascript">
function cancelTarif() {
  var oForm = document.tarifFrm;
  oForm.secteur1.value = 0;
  oForm.secteur2.value = 0;
  oForm.tarif.value = "";
  oForm.paye.value = "0";
  oForm.date_paiement.value = "";
 

  submitFdr(oForm);
}

function popFile(objectClass, objectId, elementClass, elementId){
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
  oForm.date_paiement.value = makeDATEFromDate(new Date());
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
  
  // rafraichissement de la div ccam
  loadActes({{$consult->_id}}, {{$userSel->_id}});
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
    {{if !$noReglement}}
    <th colspan="2" class="category">Règlement</th>
    {{/if}}
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


    <!-- Règlements -->	

    {{if !$noReglement}}
    <td>
      <form name="tarifFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
      {{mb_field object=$consult field="_check_premiere" hidden=1 prop=""}}
     
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
    {{/if}}
  </tr>
  
  
  <!-- Module FSE -->
  
  {{if $app->user_prefs.GestionFSE && $consult->tarif}}
  <tr>
    <th class="category" colspan="3">Feuille de Soins Electronique</th>
  </tr>
  <tr>
    <td id="yoplet-container" colspan="3">
    
    
    {{if $app->user_prefs.InterMaxDir}}
    
<!--
    <applet 
      name="intermax-yoplet"
      code="org.lostinthegarden.applet.impl.DefaultFileOperatorImpl.class" 
      archive="includes/applets/yoplet.jar" 
      width="400" 
      height="100"
    >
      <param name="action" value="sleep"/>
      <param name="debug" value="true" />
      <param name="readPath"  value="{{$app->user_prefs.InterMaxDir}}\INTERMAX.txt " />
      <param name="writePath" value="{{$app->user_prefs.InterMaxDir}}\INTERMAX.txt" />
      <param name="watchPath" value="{{$app->user_prefs.InterMaxDir}}\INTERMAX.txt" />
      <param name="content" value="Another content" />
    </applet>

-->
    {{else}}
    <div class="big-warning">
      {{tr}}pref-InterMaxDir{{/tr}} inconnu.
      <br />
      {{tr}}pref-InterMaxDir-undef{{/tr}} (cf. préférences)
    </div>
    {{/if}}
    </td>
  </tr>
  {{/if}}
  
</table>
          
