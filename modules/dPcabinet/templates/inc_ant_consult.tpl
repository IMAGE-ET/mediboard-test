<script type="text/javascript">
{literal}

var cim10url = new Url;

function selectCim10(code) {
  cim10url.setModuleAction("dPcim10", "code_finder");
  cim10url.addParam("code", code);
  cim10url.popup(800, 500, "CIM10");
}

function putCim10(code) {
  var oForm = document.editDiagFrm;
  aCim10 = oForm.listCim10.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCim10.removeByValue("");
  aCim10.push(code);
  aCim10.removeDuplicates();
  oForm.listCim10.value = aCim10.join("|");
  submitAnt(oForm);
}

function delCim10(code) {
  var oForm = document.editDiagFrm;
  var aCim10 = oForm.listCim10.value.split("|");
  aCim10.removeByValue(code);
  oForm.listCim10.value = aCim10.join("|");
  submitAnt(oForm);
}

function finTrmt() {
  var oForm = document.editTrmtFrm;
  var oEnCours = oForm._en_cours;
  var oHiddenField = oForm.fin;
  var oViewField = document.getElementById('editTrmtFrm_fin_da');
  var oTriggerField = document.getElementById('editTrmtFrm_fin_trigger');
  if (oEnCours.checked) {
    {/literal}
    oHiddenField.value = "{$today}";
    oViewField.innerHTML = "{$today|date_format:"%d/%m/%Y"}";
    oTriggerField.style.display = "inline";
    {literal}
  } else {
    oHiddenField.value = "";
    oViewField.innerHTML = "En cours";
    oTriggerField.style.display = "none";
  }
}

function closeCIM10() {
  cim10url.close();
}

function reloadAntecedents() {
  var antUrl = new Url;
  antUrl.setModuleAction("dPcabinet", "httpreq_vw_list_antecedents");
  antUrl.addParam("patient_id", document.editDiagFrm.patient_id.value);
  antUrl.requestUpdate('listAnt', { waitingText : null, onComplete : closeCIM10 });
}

function submitAnt(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAntecedents });
  oForm.reset();
}

function incAntecedantsMain() {
  {/literal}
  {foreach from=$patient->_static_cim10 key=cat item=curr_cat}
  initEffectClass("group{$cat}", "trigger{$cat}");
  {/foreach}
  {literal}
}

{/literal}
</script>

<table class="form">
  <tr><th class="category" colspan="2">Antécédents / Traitements</th></tr>
  <tr>
    <td class="text">
      <strong>Ajouter un diagnostic</strong>
      <form name="editDiagFrm" action="?m={$m}" method="post">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="tab" value="edit_consultation" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_patients_aed" />
      <input type="hidden" name="patient_id" value="{$patient->patient_id}" />
      <input type="hidden" name="listCim10" value="{$patient->listCim10}" />
      <table style="width: 100%">
      {foreach from=$patient->_static_cim10 key=cat item=curr_cat}
        <tr id="trigger{$cat}" class="triggerShow" onclick="flipEffectElement('group{$cat}', 'SlideDown', 'SlideUp', 'trigger{$cat}')">
          <td>{$cat}</td>
        </tr>
        <tbody id="group{$cat}" style="display: none">
          {foreach from=$curr_cat item=curr_code}
          <tr class="{$cat}">
            <td class="text">
              <button type="button" onclick="putCim10('{$curr_code->code}')">
                <img src="modules/dPcabinet/images/tick.png" />
              </button>
              <button type="button" onclick="selectCim10('{$curr_code->code}')">
                <img src="modules/dPcabinet/images/downarrow.png" />
              </button>
              {$curr_code->code}: {$curr_code->libelle}
            </td>
          </tr>
           {/foreach}
        </tbody>
      {/foreach}
      </table>
      </form>
      
      <hr />
      
      <form name="editAntFrm" action="?m=dPcabinet" method="post">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_antecedent_aed" />
      <input type="hidden" name="patient_id" value="{$patient->patient_id}" />
      <table class="form">
        <tr>
          <td colspan="2"><strong>Ajouter un antécédent</strong></td>
          <td>
            <label for="rques" title="Remarques sur l'antécédent">Remarques :</label>
            <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this)">
              <option value="0">&mdash; Choisir une aide</option>
              {html_options options=$antecedent->_aides.rques}
            </select>
          </td>

        </tr>
        <tr>
          <th><label for="date" title="Date de l'antécédent">Date :</label></th>
          <td class="date">
            <div id="editAntFrm_date_da">{$today|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="date" value="{$today}" />
            <img id="editAntFrm_date_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
          </td>
          <td rowspan="2">
            <textarea name="rques"></textarea>
          </td>
        </tr>
        <tr>
          <th><label for="type" title="Type d'antécédent">Type :</label></th>
          <td>
            <select name="type">
              <option value="chir">Chirurgical</option>
              <option value="fam">Familial</option>
              <option value="obst">Obstétrique</option>
              <option value="med">Medical</option>
              <option value="trans">Transfusion</option>
            </select>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="3">
            <button type="button" onclick="submitAnt(this.form)">Ajouter</button>
          </td>
        </tr>
      </table>
      </form>
      
      <hr />

      <form name="editTrmtFrm" action="?m=dPcabinet" method="post">
      
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_traitement_aed" />
      <input type="hidden" name="patient_id" value="{$patient->patient_id}" />
      
      <table class="form">
        <tr>
          <td colspan="2"><strong>Ajouter un traitement</strong></td>
          <td>
            <label for="traitement" title="Traitement">Traitement :</label>
            <select name="_helpers_traitement" size="1" onchange="pasteHelperContent(this)">
              <option value="0">&mdash; Choisir une aide</option>
              {html_options options=$traitement->_aides.traitement}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="debut" title="Début du traitement">Début :</label></th>
          <td class="date">
            <div id="editTrmtFrm_debut_da">{$today|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="debut" value="{$today}" />
            <img id="editTrmtFrm_debut_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
          </td>
          <td rowspan="2">
            <textarea name="traitement"></textarea>
          </td>
        </tr>
        <tr>
          <th>
            <input type="checkbox" checked="checked" name="_en_cours" onclick="finTrmt()" />
            <label for="fin" title="Fin du traitement">Fin :</label>
          </th>
          <td class="date">
            <div id="editTrmtFrm_fin_da">{$today|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="fin" value="{$today}" />
            <img id="editTrmtFrm_fin_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="3">
            <button type="button" onclick="submitAnt(this.form)">Ajouter</button>
          </td>
        </tr>
      </table>
      </form>
      
    </td>
    <td class="text" id="listAnt">
      {include file="inc_list_ant.tpl"}
    </td>
  </tr>
</table>