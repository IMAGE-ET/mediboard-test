<!-- $Id$ -->

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
  url.addParam("type", oForm.type.value);
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


function submitAdmission(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAdmission });
}

function pageMain() {
  
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

}

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