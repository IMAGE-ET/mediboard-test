<!-- $Id$ -->

{{mb_include_script module="hprim21" script="pat_hprim_selector"}}
{{mb_include_script module="hprim21" script="sejour_hprim_selector"}}

<script type="text/javascript">

function showLegend() {
  url = new Url;
  url.setModuleAction("dPadmissions", "vw_legende");
  url.popup(300, 170, "Legende");
}

function printPlanning() {
  var oForm = document.selType;
  url = new Url;
  url.setModuleAction("dPadmissions", "print_entrees");
  url.addParam("date", "{{$date}}");
  url.addParam("type", oForm._type_admission.value);
  url.popup(700, 550, "Entrees");
}

function printAdmission(id) {
  var url = new Url;
  url.setModuleAction("dPadmissions", "print_admission");
  url.addParam("id", id);
  url.popup(700, 550, "Patient");
}

function printDepassement(id) {
  var url = new Url;
  url.setModuleAction("dPadmissions", "print_depassement");
  url.addParam("id", id);
  url.popup(700, 550, "Depassement");
}

function reloadAdmission(type) {
  var admUrl = new Url;
  admUrl.setModuleAction("dPadmissions", "httpreq_vw_admissions");
  admUrl.addParam("selAdmis", "{{$selAdmis}}");
  admUrl.addParam("selSaisis", "{{$selSaisis}}");
  admUrl.addParam("date", "{{$date}}");
  admUrl.addParam("type", type);
  admUrl.requestUpdate('listAdmissions', { waitingText : null });
}

function confirmation(oForm){
   if(confirm('La date enregistrée d\'admission est différente de la date prévue, souhaitez vous confimer l\'admission du patient ?')){
     submitAdmission(oForm);
   }
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
}


function submitAdmission(oForm) {
  {{if $modules.hprim21}}
    var oIPPForm = document.forms["editIPP" + oForm.patient_id.value];
    var oNumDosForm = document.forms["editNumdos" + oForm.sejour_id.value];
    if(oIPPForm && oNumDosForm && (!$V(oIPPForm.id400) || !$V(oNumDosForm.id400)) ) {
      setExternalIds(oForm);
    } else {
      submitFormAjax(oForm, 'systemMsg', { onComplete : function() { reloadAdmission() } });
    }
  {{else}}
    submitFormAjax(oForm, 'systemMsg', { onComplete : function() { reloadAdmission() } });
  {{/if}}
}

function setExternalIds(oForm) {
  SejourHprimSelector["init"+oForm.sejour_id.value]();
}

PatHprimSelector.doSet = function(){
  var oForm = document[PatHprimSelector.sForm];
  $V(oForm[PatHprimSelector.sId], PatHprimSelector.prepared.id);
  ExtRefManager.submitIPPForm(oForm.patient_id.value);
}

SejourHprimSelector.doSet = function(){
  var oFormSejour = document[SejourHprimSelector.sForm];
  $V(oFormSejour[SejourHprimSelector.sId]  , SejourHprimSelector.prepared.id);
  ExtRefManager.submitNumdosForm(oFormSejour.object_id.value);
  if(SejourHprimSelector.prepared.IPPid) {
    var oFormIPP = document[SejourHprimSelector.sIPPForm];
    $V(oFormIPP[SejourHprimSelector.sIPPId]  , SejourHprimSelector.prepared.IPPid);
    ExtRefManager.submitIPPForm(oFormIPP.object_id.value);
  }
}

Main.add(function () {
  var totalUpdater = new Url;
  totalUpdater.setModuleAction("dPadmissions", "httpreq_vw_all_admissions");
  totalUpdater.addParam("date", "{{$date}}");
  totalUpdater.periodicalUpdate('allAdmissions', { frequency: 120 });
  
  var listUpdater = new Url;
  listUpdater.setModuleAction("dPadmissions", "httpreq_vw_admissions");
  listUpdater.addParam("selAdmis", "{{$selAdmis}}");
  listUpdater.addParam("selSaisis", "{{$selSaisis}}");
  listUpdater.addParam("date", "{{$date}}");
  listUpdater.periodicalUpdate('listAdmissions', { frequency: 120 });
});

</script>




<table class="main">
<tr>
  <td>
    <a href="#" onclick="showLegend()" class="buttonsearch">Légende</a>
  </td>
  <td style="float: right">
    <form action="?" name="selType">
      {{mb_field object=$sejour field="_type_admission" defaultOption="&mdash; Toutes les admissions" onchange="reloadAdmission(this.value)"}}
    </form>
    <a href="#" onclick="printPlanning()" class="buttonprint">Imprimer</a>
  </td>
</tr>
  <tr>
    <td id="allAdmissions" style="width: 250px">
    </td>
    <td id="listAdmissions" style="width: 100%">
    </td>
  </tr>
</table>