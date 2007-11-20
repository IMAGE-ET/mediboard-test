{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<script type="text/javascript">

var cim10url = new Url;

function selectCim10(code) {
  cim10url.setModuleAction("dPcim10", "code_finder");
  cim10url.addParam("code", code);
  cim10url.popup(800, 500, "CIM10");
}

function reloadCim10(sCode){
  var oForm = document.addDiagFrm;
  
  oCimField.add(sCode);
 
  {{if $_is_anesth}}
  if (document.addOpFrm && document.addOpFrm.operation_id.value != '') {
    oCimAnesthField.add(sCode);
  }
  {{/if}}
  updateTokenCim10();
  oForm.code_diag.value="";
}

function updateTokenCim10() {
  var oForm = document.editDiagFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadDossierMedicalPatient });
}

function updateTokenCim10Anesth(){
  var oForm = document.editDiagAnesthFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadDossierMedicalSejour });
}

function dateAntecedent(){
  var oForm = document.editAntFrm;
  var oEnCours = oForm._date_ant;
  var oHiddenField = oForm.date;
  oForm._hidden_rques.value = oForm.rques.value;
  oForm.rques.value = "";
  var oViewField = document.getElementById('editAntFrm_date_da');
  var oTriggerField = document.getElementById('editAntFrm_date_trigger');
  if (oEnCours.checked) {
    oHiddenField.value = "{{$today}}";
    oViewField.innerHTML = "{{$today|date_format:"%d/%m/%Y"}}";
    oTriggerField.style.display = "inline";
  }else{
    oHiddenField.value = "";
    oViewField.innerHTML = "";
    oTriggerField.style.display = "none";
  }   
}

function dateTrmt(){
  var oForm = document.editTrmtFrm;
  var oDateTrmt = oForm._datetrmt
  var oHiddenDebutField = oForm.debut;
  var oViewDebutField = document.getElementById('editTrmtFrm_debut_da');
  var oTriggerDebutField = document.getElementById('editTrmtFrm_debut_trigger');
  var oHiddenFinField = oForm.fin;
  var oViewFinField = document.getElementById('editTrmtFrm_fin_da');
  var oTriggerFinField = document.getElementById('editTrmtFrm_fin_trigger');
  if (oDateTrmt.checked) {
    oHiddenDebutField.value = "{{$today}}";
    oViewDebutField.innerHTML = "{{$today|date_format:"%d/%m/%Y"}}";
    oTriggerDebutField.style.display = "inline";
    oForm._en_cours.disabled = false;
    dateFinTrmt();
  }else{
    oHiddenDebutField.value = "";
    oViewDebutField.innerHTML = "";
    oTriggerDebutField.style.display = "none";
    oHiddenFinField.value = "";
    oViewFinField.innerHTML = "";
    oTriggerFinField.style.display = "none";
    oForm._en_cours.disabled = true;
  }
}

function dateFinTrmt(){
  var oForm = document.editTrmtFrm;
  var oEnCours = oForm._en_cours;
  var oHiddenField = oForm.fin;
  var oViewField = document.getElementById('editTrmtFrm_fin_da');
  var oTriggerField = document.getElementById('editTrmtFrm_fin_trigger');
  if (oEnCours.checked) {
    oHiddenField.value = "{{$today}}";
    oViewField.innerHTML = "{{$today|date_format:"%d/%m/%Y"}}";
    oTriggerField.style.display = "inline";

  } else {
    oHiddenField.value = "";
    oViewField.innerHTML = "En cours";
    oTriggerField.style.display = "none";
  }
}

function finTrmt() {
  var oForm = document.editTrmtFrm;
  oForm._hidden_traitement.value = oForm.traitement.value;
  oForm.traitement.value = "";
  oForm._helpers_traitement.value = "";
}

function closeCIM10() {
  cim10url.close();
}

function reloadDossierMedicalPatient(){
  var antUrl = new Url;
  antUrl.setModuleAction("dPcabinet", "httpreq_vw_list_antecedents");
  antUrl.addParam("patient_id", "{{$patient->_id}}");
  antUrl.addParam("_is_anesth", "{{$_is_anesth}}");
  {{if $_is_anesth}}
  antUrl.addParam("sejour_id", tabSejour[document.addOpFrm.operation_id.value]);
  {{/if}}
  antUrl.requestUpdate('listAnt', { waitingText : null, onComplete : closeCIM10 });
 
}

function reloadDossiersMedicaux(){
  reloadDossierMedicalPatient();

  {{if $_is_anesth}}
  if (document.addOpFrm.operation_id.value != ""){
    reloadDossierMedicalSejour();
  }
  {{/if}}
}

function submitAnt(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadDossiersMedicaux });
}


{{if $_is_anesth}}
function reloadDossierMedicalSejour() {
  var antUrl = new Url;
  antUrl.setModuleAction("dPcabinet", "httpreq_vw_list_antecedents_anesth");  
  antUrl.addParam("sejour_id", tabSejour[document.addOpFrm.operation_id.value]);
  antUrl.requestUpdate('listAntCAnesth', { waitingText : null });
}

function copyAntecedent(antecedent_id){
 var oForm = document.frmCopyAntecedent;
 oForm.antecedent_id.value = antecedent_id;
 submitFormAjax(oForm, 'systemMsg', { waitingText : null, onComplete : reloadDossierMedicalSejour });
}
function copyTraitement(traitement_id){
 var oForm = document.frmCopyTraitement;
 oForm.traitement_id.value = traitement_id;
 submitFormAjax(oForm, 'systemMsg', { waitingText : null, onComplete : reloadDossierMedicalSejour });
}
{{/if}}

</script>

<table class="form">
  <tr>
    <td class="text">
     {{include file="inc_consult_anesth/inc_addictions.tpl}}
      <hr />
      <form name="editAntFrm" action="?m=dPcabinet" method="post">
      
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_antecedent_aed" />
      <input type="hidden" name="_patient_id" value="{{$patient->patient_id}}" />
      
      <!-- dossier_medical_id du sejour si c'est une consultation_anesth -->
      {{if $_is_anesth}}
      <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
      <input type="hidden" name="_sejour_id" value="{{$consult->_ref_consult_anesth->_ref_operation->_ref_sejour->_id}}" />
      {{/if}}

      <table class="form">
        <tr>
          <th>
            <input type="checkbox" name="_date_ant" onclick="dateAntecedent()" />
            {{mb_label object=$antecedent field="date"}}
          </th>
          <td class="date">
            <div id="editAntFrm_date_da"></div>
            <input type="hidden" name="date" value="" />
            <img id="editAntFrm_date_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de début" style="display:none;" />
          </td>
          <td id="listAides_Antecedent_rques">
            {{mb_label object=$antecedent field="rques"}}
            {{foreach from=$antecedent->_aides.rques item=_helpers key=dependsOn}}
            <select name="_helpers_rques-{{$dependsOn}}" size="1" onchange="pasteHelperContent(this)" style="display:none;">
                <option value="">&mdash; Choisir une aide</option>
                {{foreach from=$_helpers item=list_aides key=sTitleOpt}}
                  <optgroup label="{{$sTitleOpt}}">
                    {{html_options options=$list_aides}}
                  </optgroup>
                {{/foreach}}
              </select>
            {{/foreach}}
            <input type="hidden" name="_hidden_rques" value="" />
            <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CAntecedent', this.form._hidden_rques, 'rques')">
              Nouveau
            </button>
          </td>

        </tr>
        <tr>
          <th />
          <td />
          <td rowspan="3">
            <textarea name="rques" onblur="if(verifNonEmpty(this)){submitAnt(this.form);dateAntecedent();}"></textarea>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$antecedent field="type"}}</th>
          <td>
            {{mb_field object=$antecedent field="type" onchange="putHelperContent(this,'rques')"}}
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="tick" type="button" onclick="if(verifNonEmpty(this.form.rques)){submitAnt(this.form);dateAntecedent();}">
              {{tr}}Add{{/tr}} un antécédent
            </button>
          </td>
        </tr>
      </table>
      </form>
      
      <hr />

      <form name="editTrmtFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_traitement_aed" />
      <input type="hidden" name="_patient_id" value="{{$patient->patient_id}}" />
      
      {{if $_is_anesth}}
      <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
      <input type="hidden" name="_sejour_id" value="{{$consult->_ref_consult_anesth->_ref_operation->_ref_sejour->_id}}" />
      {{/if}}
      
      <table class="form">
        <tr>
          <th>
            <input type="checkbox" name="_datetrmt" onclick="dateTrmt()" />
            {{mb_label object=$traitement field="debut"}}
          </th>
          <td class="date">
            <div id="editTrmtFrm_debut_da"></div>
            <input type="hidden" name="debut" class="{{$traitement->_props.debut}}" value="" />
            <img id="editTrmtFrm_debut_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de début" style="display:none;" />
          </td>
          <td>
            {{mb_label object=$traitement field="traitement"}}
            <select name="_helpers_traitement" size="1" onchange="pasteHelperContent(this)">
              <option value="">&mdash; Choisir une aide</option>
              {{html_options options=$traitement->_aides.traitement.no_enum}}
            </select>
            <input type="hidden" name="_hidden_traitement" value="" />
            <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CTraitement', this.form._hidden_traitement, 'traitement')">
              Nouveau
            </button>
          </td>
        </tr>
        <tr>
          <th />
          <td />
          <td rowspan="3">
            <textarea name="traitement" onblur="if(verifNonEmpty(this)){submitAnt(this.form);finTrmt();}"></textarea>
          </td>
        </tr>
        <tr>
          <th>
            <input type="checkbox" name="_en_cours" disabled="disabled" onclick="dateFinTrmt()" />
            {{mb_label object=$traitement field="fin"}}
          </th>
          <td class="date">
            <div id="editTrmtFrm_fin_da"></div>
            <input type="hidden" name="fin" class="{{$traitement->_props.fin}}" value="" />
            <img id="editTrmtFrm_fin_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de fin" style="display:none;" />
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="tick" type="button" onclick="if(verifNonEmpty(this.form.traitement)){submitAnt(this.form);finTrmt();}">
              {{tr}}Add{{/tr}} un traitement
            </button>
          </td>
        </tr>
      </table>
      </form>
      
      <hr />
      
      <form name="addDiagFrm" action="?m=dPcabinet" method="post">
        <strong>Ajouter un diagnostic</strong>
        <input type="hidden" name="chir" value="{{$userSel->_id}}" />
        <button class="search" type="button" onclick="CIM10Selector.init()">Chercher un diagnostic</button>
        <input type="text" name="code_diag" size="5"/>
        <button class="tick notext" type="button" onclick="reloadCim10(code_diag.value)" >Valider</button>
        <script type="text/javascript">   
          CIM10Selector.init = function(){
            this.sForm = "addDiagFrm";
            this.sView = "code_diag";
            this.sChir = "chir";
            this.pop();
          }
        </script> 
      </form>
      
      <table style="width: 100%">
      {{foreach from=$patient->_static_cim10 key=cat item=curr_cat}}
        <tr id="category{{$cat}}-trigger">
          <td>{{$cat}}</td>
        </tr>
        <tbody id="category{{$cat}}">
          <tr class="script"><td><script type="text/javascript">new PairEffect("category{{$cat}}");</script></td></tr>
          {{foreach from=$curr_cat item=curr_code}}
          <tr>
            <td class="text">
              <button class="tick notext" type="button" onclick="oCimField.add('{{$curr_code->code}}'); if(document.addOpFrm &amp;&amp; document.addOpFrm.operation_id.value != '') { {{if $_is_anesth}}oCimAnesthField.add('{{$curr_code->code}}');{{/if}} }">
                Ajouter
              </button>
              <button class="down notext" type="button" onclick="selectCim10('{{$curr_code->code}}')">
                Parcourir
              </button>
              {{$curr_code->code}}: {{$curr_code->libelle}}
            </td>
          </tr>
           {{/foreach}}
        </tbody>
      {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      <table class="form">
        <tr>
          <th class="category">
            Dossier patient
          </th>
        </tr>
        <tr>
          <td class="text" id="listAnt">       
          </td>
        </tr> 
        {{if $_is_anesth}}
        <tr>
          <th class="category">
            Eléments significatifs pour le séjour
          </th>
        </tr>
        <tr>
          <td class="text" id="listAntCAnesth">
          </td>
        </tr>
        {{/if}}
      </table>
    </td>
  </tr>
</table>