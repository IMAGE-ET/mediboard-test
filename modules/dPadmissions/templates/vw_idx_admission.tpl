<!-- $Id$ -->

<script type="text/javascript">
{literal}

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
  {/literal}
  admUrl.addParam("selAdmis", "{$selAdmis}");
  admUrl.addParam("selSaisis", "{$selSaisis}");
  admUrl.addParam("date", "{$date}");
  {literal}
  admUrl.requestUpdate('listAdmissions', { waitingText : null });
}

function submitAdmission(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAdmission });
}

function pageMain() {
  
  var totalUpdater = new Url;
  totalUpdater.setModuleAction("dPadmissions", "httpreq_vw_all_admissions");
  {/literal}
  totalUpdater.addParam("date", "{$date}");
  {literal}
  totalUpdater.periodicalUpdate('allAdmissions', { frequency: 60 });
  
  var listUpdater = new Url;
  listUpdater.setModuleAction("dPadmissions", "httpreq_vw_admissions");
  {/literal}
  listUpdater.addParam("selAdmis", "{$selAdmis}");
  listUpdater.addParam("selSaisis", "{$selSaisis}");
  listUpdater.addParam("date", "{$date}");
  {literal}
  listUpdater.periodicalUpdate('listAdmissions', { frequency: 60 });

}

{/literal}
</script>

<table class="main">
  <tr>
    <td id="allAdmissions" class="halfPane">
    </td>
    <td id="listAdmissions" class="halfPane">
    </td>
  </tr>
</table>