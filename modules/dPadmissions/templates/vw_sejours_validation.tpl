{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPadmissions
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_script module=hprim21 script=pat_hprim_selector}}
{{mb_script module=hprim21 script=sejour_hprim_selector}}
{{mb_script module=admissions script=admissions}}

<script type="text/javascript">

function showLegend() {
  new Url('admissions', "vw_legende").requestModal();
}

function printPlanning() {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "print_entrees");
  url.addParam("date"      , "{{$date}}");
  url.addParam("service_id", $V(oForm.service_id));
  url.popup(700, 550, "Entrees");
}

function printDHE(type, object_id) {
  var url = new Url("dPplanningOp", "view_planning");
  url.addParam(type, object_id);
  url.popup(700, 550, "DHE");
}

function reloadFullAdmissions(filterFunction) {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "ajax_vw_all_sejours");
  url.addParam("current_m" , "{{$current_m}}");
  url.addParam("date"      , "{{$date}}");
  url.addParam("service_id", $V(oForm.service_id));
  url.addParam("prat_id"   , $V(oForm.prat_id));
  url.requestUpdate("allAdmissions");
  reloadAdmission(filterFunction);
}

function reloadAdmission(filterFunction) {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "ajax_vw_sejours");
  url.addParam("current_m" , "{{$current_m}}");
  url.addParam("recuse"    , "{{$recuse}}");
  {{if "reservation"|module_active}}
    url.addParam("envoi_mail", "{{$envoi_mail}}");
  {{/if}}
  url.addParam("date"      , "{{$date}}");
  url.addParam("service_id", $V(oForm.service_id));
  url.addParam("prat_id", $V(oForm.prat_id));
  if(!Object.isUndefined(filterFunction)){
    url.addParam("filterFunction", filterFunction);
  }
  url.requestUpdate("listAdmissions");
}

function confirmation(oForm){
  if(confirm('La date enregistrée d\'admission est différente de la date prévue, souhaitez vous confimer l\'admission du patient ?')){
    submitAdmission(oForm);
  }
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
  var totalUpdater = new Url("dPadmissions", "ajax_vw_all_sejours");
  totalUpdater.addParam("current_m", "{{$current_m}}");
  totalUpdater.addParam("date", "{{$date}}");
  totalUpdater.periodicalUpdate('allAdmissions', { frequency: 120 });
  
  var listUpdater = new Url("dPadmissions", "ajax_vw_sejours");
  listUpdater.addParam("recuse", "{{$recuse}}");
  listUpdater.addParam("current_m", "{{$current_m}}");
  listUpdater.addParam("date", "{{$date}}");
  listUpdater.periodicalUpdate('listAdmissions', { frequency: 120 });
});

</script>

<table class="main">
  <tr>
    <td>
      <a href="#legend" onclick="showLegend()" class="button search">Légende</a>
    </td>
    <td>
      {{if $can->edit && "ssr"|module_active}} 
      <a class="button new" style="float: left;" href="?m=ssr&amp;tab=vw_aed_sejour_ssr&amp;sejour_id=0">
        Créer une demande de prise en charge
      </a>
      {{/if}}
      <a href="#" onclick="printPlanning()" class="button print" style="float: right">Imprimer</a>
      <form action="?" name="selType" method="get" style="float: right">
        <select name="service_id" onchange="reloadFullAdmissions();">
          <option value="">&mdash; Tous les services</option>
          {{foreach from=$services item=_service}}
            <option value="{{$_service->_id}}"{{if $_service->_id == $sejour->service_id}}selected="selected"{{/if}}}>{{$_service}}</option>
          {{/foreach}}
        </select>
        <select name="prat_id" onchange="reloadFullAdmissions();">
          <option value="">&mdash; Tous les praticiens</option>
          {{foreach from=$prats item=_prat}}
            <option value="{{$_prat->_id}}"{{if $_prat->_id == $sejour->praticien_id}}selected="selected"{{/if}}>{{$_prat}}</option>
          {{/foreach}}
        </select>
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      {{if $nb_sejours_attente}}
      <div class="small-warning">
        Il y a {{$nb_sejours_attente}} patients à venir en attente de validation
      </div>
      {{else}}
      <div class="small-info">
        Il n'y a  pas de patients à venir en attente de validation
      </div>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td id="allAdmissions" style="width: 250px">
    </td>
    <td id="listAdmissions" style="width: 100%">
    </td>
  </tr>
</table>