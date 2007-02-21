<script type="text/javascript">

var cim10url = new Url;

function selectCim10(code) {
  cim10url.setModuleAction("dPcim10", "code_finder");
  cim10url.addParam("code", code);
  cim10url.popup(800, 500, "CIM10");
}

function popCode() {
  cim10url.setModuleAction("dPcim10", "vw_find_code");
  cim10url.popup(700, 500, "CIM10");
}

function setCode(sCode, type, sFullCode) {
  oCimField.add(sCode);
  oCimAnesthField.add(sCode);
  updateTokenCim10();
}

function updateTokenCim10() {
  var oForm = document.editDiagFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAntecedents });
}

function updateTokenCim10Anesth(){
  var oForm = document.editTabacFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAntecedentsAnesth });
}

function dateAntecedent(){
  var oForm = document.editAntFrm;
  var oEnCours = oForm._date_ant;
  var oHiddenField = oForm.date;
  oForm._helpers_rques.value = "";
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

function reloadAntecedents() {
  var antUrl = new Url;
  antUrl.setModuleAction("dPcabinet", "httpreq_vw_list_antecedents");
  antUrl.addParam("patient_id", document.editDiagFrm.patient_id.value);
  antUrl.addParam("_is_anesth", "{{$_is_anesth}}");
  antUrl.requestUpdate('listAnt', { waitingText : null, onComplete : closeCIM10 });
}

function submitAnt(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAntecedents });
}

function incAntecedantsMain() {
  PairEffect.initGroup("effectCategory");
}
{{if $_is_anesth}}
function reloadAntecedentsAnesth() {
  var antUrl = new Url;
  antUrl.setModuleAction("dPcabinet", "httpreq_vw_list_antecedents_anesth");
  antUrl.addParam("consultation_anesth_id", "{{$consult_anesth->consultation_anesth_id}}");
  antUrl.requestUpdate('listAntCAnesth', { waitingText : null });
}

function copyAntecedent(antecedent_id){
 var oForm = document.frmCopyAntecedent;
 oForm.antecedent_id.value = antecedent_id;
 oForm.object_class.value  = "CConsultAnesth";
 oForm.object_id.value     = "{{$consult_anesth->consultation_anesth_id}}";
 submitFormAjax(oForm, 'systemMsg', { waitingText : null, onComplete : reloadAntecedentsAnesth });
}
function copyTraitement(traitement_id){
 var oForm = document.frmCopyTraitement;
 oForm.traitement_id.value = traitement_id;
 oForm.object_class.value  = "CConsultAnesth";
 oForm.object_id.value     = "{{$consult_anesth->consultation_anesth_id}}";
 submitFormAjax(oForm, 'systemMsg', { waitingText : null, onComplete : reloadAntecedentsAnesth });
}
{{/if}}

</script>

<table class="form">
  <tr>
    <td class="text">
      {{if $_is_anesth}}
        {{if $dPconfig.dPcabinet.addictions}}
          {{include file="inc_consult_anesth/inc_addictions.tpl}}
        {{else}}
          {{include file="inc_consult_anesth/inc_tabac_oenolisme.tpl}}      
        {{/if}}
      {{/if}}
      <hr />
      
      <form name="editAntFrm" action="?m=dPcabinet" method="post">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_antecedent_aed" />
      <input type="hidden" name="object_id" value="{{$patient->patient_id}}" />
      <input type="hidden" name="object_class" value="CPatient" />      
      {{if $_is_anesth}}
      {{mb_field object=$consult_anesth field="consultation_anesth_id" type="hidden" spec=""}}
      {{/if}}

      <table class="form">
        <tr>
          <td colspan="2"><strong>Ajouter un antécédent</strong></td>
          <td>
            {{mb_label object=$antecedent field="rques"}}
            <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this)">
              <option value="">&mdash; Choisir une aide</option>
              {{html_options options=$antecedent->_aides.rques}}
            </select>
            <input type="hidden" name="_hidden_rques" value="" />
            <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CAntecedent', this.form._hidden_rques, 'rques')"/>
          </td>

        </tr>
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
          <td rowspan="2">
            <textarea name="rques" onblur="if(verifNonEmpty(this)){submitAnt(this.form);dateAntecedent();}"></textarea>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$antecedent field="type"}}</th>
          <td>
            {{mb_field object=$antecedent field="type"}}
          </td>
        </tr>
        <tr>
          <td class="button" colspan="3">
            <button class="submit" type="button" onclick="if(verifNonEmpty(this.form.rques)){submitAnt(this.form);dateAntecedent();}">Ajouter</button>
          </td>
        </tr>
      </table>
      </form>
      
      <hr />

      <form name="editTrmtFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_traitement_aed" />
      <input type="hidden" name="object_id" value="{{$patient->patient_id}}" />
      <input type="hidden" name="object_class" value="CPatient" />
      {{if $_is_anesth}}
      {{mb_field object=$consult_anesth field="consultation_anesth_id" type="hidden" spec=""}}
      {{/if}}
      
      <table class="form">
        <tr>
          <td colspan="2"><strong>Ajouter un traitement</strong></td>
          <td>
            {{mb_label object=$traitement field="traitement"}}
            <select name="_helpers_traitement" size="1" onchange="pasteHelperContent(this)">
              <option value="">&mdash; Choisir une aide</option>
              {{html_options options=$traitement->_aides.traitement}}
            </select>
            <input type="hidden" name="_hidden_traitement" value="" />
            <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CTraitement', this.form._hidden_traitement, 'traitement')"/>
          </td>
        </tr>
        <tr>
          <th>
            <input type="checkbox" name="_datetrmt" onclick="dateTrmt()" />
            {{mb_label object=$traitement field="debut"}}
          </th>
          <td class="date">
            <div id="editTrmtFrm_debut_da"></div>
            <input type="hidden" name="debut" title="{{$traitement->_props.debut}}" value="" />
            <img id="editTrmtFrm_debut_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de début" style="display:none;" />
          </td>
          <td rowspan="2">
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
            <input type="hidden" name="fin" title="{{$traitement->_props.fin}}" value="" />
            <img id="editTrmtFrm_fin_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de fin" style="display:none;" />
          </td>
        </tr>
        <tr>
          <td class="button" colspan="3">
            <button class="submit" type="button" onclick="if(verifNonEmpty(this.form.traitement)){submitAnt(this.form);finTrmt();}">Ajouter</button>
          </td>
        </tr>
      </table>
      </form>
      
      <hr />
      <strong>Ajouter un diagnostic</strong>
      
      <button class="search" onclick="popCode()">Chercher un diagnostic</button>
      <form name="editDiagFrm" action="?m={{$m}}" method="post">

      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="tab" value="edit_consultation" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_patients_aed" />
      {{mb_field object=$patient field="patient_id" type="hidden" spec=""}}
      {{mb_field object=$patient field="listCim10" type="hidden" spec=""}}
      
      <table style="width: 100%">
      {{foreach from=$patient->_static_cim10 key=cat item=curr_cat}}
        <tr id="category{{$cat}}-trigger">
          <td>{{$cat}}</td>
        </tr>
        <tbody class="effectCategory" id="category{{$cat}}">
          {{foreach from=$curr_cat item=curr_code}}
          <tr>
            <td class="text">
              <button class="tick notext" type="button" onclick="oCimField.add('{{$curr_code->code}}');oCimAnesthField.add('{{$curr_code->code}}');"></button>
              <button class="down notext" type="button" onclick="selectCim10('{{$curr_code->code}}')"></button>
              {{$curr_code->code}}: {{$curr_code->libelle}}
            </td>
          </tr>
           {{/foreach}}
        </tbody>
      {{/foreach}}
      </table>
      </form>      
    </td>

    <td>
      <table class="form">
        {{if $dPconfig.dPcabinet.addictions}}
        <tr>
          <th class="category">
            Addictions
          </th>
        </tr>
        <tr>
          <td class="text" id="listAddict">
            {{include file="inc_consult_anesth/inc_list_addiction.tpl"}}
          </td>
        </tr>
        {{/if}}
        <tr>
          <th class="category">
            Dossier patient
          </th>
        </tr>
        <tr>
          <td class="text" id="listAnt">
            {{include file="inc_list_ant.tpl"}}
          </td>
        </tr>
        {{if $_is_anesth}}
        <tr>
          <th class="category">
            Eléments significatifs pour la prise en charge du séjour
          </th>
        </tr>
        <tr>
          <td class="text" id="listAntCAnesth">
            {{include file="inc_list_ant_anesth.tpl"}}
          </td>
        </tr>
        {{/if}}
      </table>
    </td>
  </tr>

</table>