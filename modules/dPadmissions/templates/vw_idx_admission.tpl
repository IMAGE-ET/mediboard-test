<!-- $Id$ -->

<script type="text/javascript">

function showLegend() {
  url = new Url;
  url.setModuleAction("dPadmissions", "vw_legende");
  url.popup(300, 150, "Legende");
}

function printPlanning() {
  url = new Url;
  url.setModuleAction("dPadmissions", "print_entrees");
  url.addParam("date", "{{$date}}");
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

function reloadAdmission() {
  var admUrl = new Url;
  admUrl.setModuleAction("dPadmissions", "httpreq_vw_admissions");
  admUrl.addParam("selAdmis", "{{$selAdmis}}");
  admUrl.addParam("selSaisis", "{{$selSaisis}}");
  admUrl.addParam("date", "{{$date}}");
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
  <td>
    <a style="float: right;" href="#" onclick="printPlanning()" class="buttonprint">Imprimer</a>
  </td>
</tr>
  <tr>
    <td id="allAdmissions" style="width: 250px">
    </td>
    <td id="listAdmissions" style="width: 100%">
    </td>
  </tr>
</table>