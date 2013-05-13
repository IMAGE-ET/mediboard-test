{{* $Id: vw_idx_admission.tpl 14901 2012-03-15 09:49:05Z flaviencrochard $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 14901 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=hprim21 script=pat_hprim_selector}}
{{mb_script module=hprim21 script=sejour_hprim_selector}}
{{mb_script module=admissions script=admissions}}
{{mb_script module=patients script=antecedent}}
{{mb_script module=compteRendu script=document}}
{{mb_script module=compteRendu script=modele_selector}}
{{mb_script module=cabinet script=file}}
{{if "web100T"|module_active}}
  {{mb_script module=web100T script=web100T}}
{{/if}}

<script type="text/javascript">
function printPlanning() {
  var oForm = document.selType;
  var url = new Url("dPadmissions", "print_entrees");
  url.addParam("date"      , "{{$date}}");
  url.addParam("type"      , $V(oForm._type_admission));
  url.addParam("service_id", [$V(oForm.service_id)].flatten().join(","));
  url.popup(700, 550, "Entrees");
}

function reloadFullPresents(filterFunction) {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "httpreq_vw_all_presents");
  url.addParam("date"      , "{{$date}}");
  url.addParam("type"      , $V(oForm._type_admission));
  url.addParam("service_id", [$V(oForm.service_id)].flatten().join(","));
  url.addParam("prat_id", $V(oForm.prat_id));
  url.requestUpdate('allPresents');
	reloadPresent(filterFunction);
}

function reloadPresent(filterFunction) {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "httpreq_vw_presents");
  url.addParam("date"      , "{{$date}}");
  url.addParam("type"      , $V(oForm._type_admission));
  url.addParam("service_id", [$V(oForm.service_id)].flatten().join(","));
  url.addParam("prat_id", $V(oForm.prat_id));
	if(!Object.isUndefined(filterFunction)){
	  url.addParam("filterFunction", filterFunction);
	}
  url.requestUpdate('listPresents');
}

function confirmation(oForm){
  if(confirm('La date enregistrée d\'admission est différente de la date prévue, souhaitez vous confimer l\'admission du patient ?')){
    submitAdmission(oForm);
  }
}

function submitCote(oForm) {
  return onSubmitFormAjax(oForm, { onComplete : reloadAdmission });
}

function submitAdmission(oForm, bPassCheck) {
  {{if @$modules.hprim21 && $conf.hprim21.mandatory_num_dos_ipp_adm}}
    var oIPPForm = document.forms["editIPP" + oForm.patient_id.value];
    var oNumDosForm = document.forms["editNumdos" + oForm.sejour_id.value];
    if(!bPassCheck && oIPPForm && oNumDosForm && (!$V(oIPPForm.id400) || !$V(oNumDosForm.id400)) ) {
      setExternalIds(oForm);
    } else {
      return onSubmitFormAjax(oForm, { onComplete : reloadAdmission });
    }
  {{else}}
    return onSubmitFormAjax(oForm, { onComplete : reloadAdmission });
  {{/if}}
}

var ExtRefManager = {
  sejour_id : null,
  patient_id: null,

  submitIPPForm: function(patient_id) {
    ExtRefManager.patient_id = patient_id;
    var oForm = document.forms["editIPP" + patient_id];
    return onSubmitFormAjax(oForm, {onComplete: ExtRefManager.reloadIPPForm});
  },

  reloadIPPForm: function() {
    reloadAdmission();
  },

  submitNumdosForm: function(sejour_id) {
    ExtRefManager.sejour_id = sejour_id;
    var oForm = document.forms["editNumdos" + this.sejour_id];
    return onSubmitFormAjax(oForm, {onComplete: ExtRefManager.reloadNumdosForm});
  },

  reloadNumdosForm: function() {
    reloadAdmission();
  }
};

function setExternalIds(oForm) {
  SejourHprimSelector["init"+oForm.sejour_id.value]();
}

PatHprimSelector.doSet = function(){
  var oForm = document[PatHprimSelector.sForm];
  $V(oForm[PatHprimSelector.sId], PatHprimSelector.prepared.id);
  ExtRefManager.submitIPPForm(oForm.patient_id.value);
};

SejourHprimSelector.doSet = function(){
  var oFormSejour = document[SejourHprimSelector.sForm];
  $V(oFormSejour[SejourHprimSelector.sId]  , SejourHprimSelector.prepared.id);
  ExtRefManager.submitNumdosForm(oFormSejour.object_id.value);
  if(SejourHprimSelector.prepared.IPPid) {
    var oFormIPP = document[SejourHprimSelector.sIPPForm];
    $V(oFormIPP[SejourHprimSelector.sIPPId]  , SejourHprimSelector.prepared.IPPid);
    ExtRefManager.submitIPPForm(oFormIPP.object_id.value);
  }
  //submitAdmission(document["editAdmFrm"+oFormSejour.object_id.value]);
};

Main.add(function () {
  var totalUpdater = new Url("dPadmissions", "httpreq_vw_all_presents");
  totalUpdater.addParam("date", "{{$date}}");
  Admissions.totalUpdater = totalUpdater.periodicalUpdate('allPresents', { frequency: 120 });

  var listUpdater = new Url("dPadmissions", "httpreq_vw_presents");
  listUpdater.addParam("date", "{{$date}}");
  Admissions.listUpdater = listUpdater.periodicalUpdate('listPresents', {
    frequency: 120,
    onCreate: function() {
      WaitingMessage.cover($('listPresents'));
      Admissions.rememberSelection('listPresents');
    } });
});

</script>

<div style="display: none" id="area_prompt_modele">
  {{mb_include module=admissions template=inc_prompt_modele type=admissions}}
</div>

<table class="main">
<tr>
  <td>
    <a href="#legend" onclick="Admissions.showLegend()" class="button search">Légende</a>
    {{if "astreintes"|module_active}}{{mb_include module=astreintes template=inc_button_astreinte_day date=$date}}{{/if}}
  </td>
  <td style="float: right">
    <form action="?" name="selType" method="get">
      {{mb_field object=$sejour field="_type_admission" emptyLabel="CSejour.all" onchange="reloadFullPresents()"}}
      <select name="service_id" onchange="reloadFullPresents();" {{if $sejour->service_id|@count > 1}}size="5" multiple="true"{{/if}}>
        <option value="">&mdash; Tous les services</option>
        {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if in_array($_service->_id, $sejour->service_id)}}selected="selected"{{/if}}}>{{$_service}}</option>
        {{/foreach}}
      </select>
      <input type="checkbox" onclick="Admissions.toggleMultipleServices(this)" {{if $sejour->service_id|@count > 1}}checked="checked"{{/if}}/>
      <select name="prat_id" onchange="reloadFullPresents();">
        <option value="">&mdash; Tous les praticiens</option>
        {{foreach from=$prats item=_prat}}
          <option value="{{$_prat->_id}}"{{if $_prat->_id == $sejour->praticien_id}}selected="selected"{{/if}}>{{$_prat}}</option>
        {{/foreach}}
      </select>
    </form>
    <a href="#" onclick="printPlanning()" class="button print" style="display: none;">Imprimer</a>
    <a href="#" onclick="Admissions.beforePrint(); Modal.open('area_prompt_modele')" class="button print">{{tr}}CCompteRendu-print_for_select{{/tr}}</a>
    {{if "web100T"|module_active}}
      {{mb_include module=web100T template=inc_button_send_all_prestations type=admissions}}
    {{/if}}
  </td>
</tr>
  <tr>
    <td id="allPresents" style="width: 250px">
    </td>
    <td id="listPresents" style="width: 100%">
    </td>
  </tr>
</table>