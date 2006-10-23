<script type="text/javascript">

function editDocument(compte_rendu_id) {
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("compte_rendu_id", compte_rendu_id);
  url.popup(700, 700, "Document");
}

function createDocument(modele_id, operation_id) {
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("modele_id", modele_id);
  url.addParam("object_id", operation_id);
  url.popup(700, 700, "Document");
}

function reloadAfterSaveDoc(){
  updateListOperations();
  updateListHospi("sortie");
}

function affNaissance() {
  var oForm      = document.find;
  var oCheckNaissance = oForm.check_naissance;
  var oNaissance = oForm.naissance;
  var oDay       = oForm.Date_Day;
  var oMonth     = oForm.Date_Month;
  var oYear      = oForm.Date_Year;
  if (oCheckNaissance.checked) {
    oDay.style.display   = "inline";
    oMonth.style.display = "inline";
    oYear.style.display  = "inline";
    oNaissance.value     = "on";
  } else {
    oDay.style.display   = "none";
    oMonth.style.display = "none";
    oYear.style.display  = "none";
    oNaissance.value     = "off";
  }
}

function hideIcon(frame) {
  $("icon-" + frame).hide();
}

function showIcon(frame) {
  $("icon-" + frame).show();
}

function updateListConsults() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_list_consult");

  url.addParam("chirSel"   , "{{$app->user_id}}");
  url.addParam("date"      , "{{$date}}");
  url.addParam("vue2"      , "{{$vue}}");
  url.addParam("selConsult", "");
  url.addParam("board"     , "1");

  url.requestUpdate("consultations");
}

function updateListOperations() {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "httpreq_vw_list_operations");

  url.addParam("chirSel" , "{{$app->user_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("urgences", "0");
  url.addParam("board"   , "1");

  url.requestUpdate("operations");
}

function updateListPatients() {
  var url = new Url;
  url.setModuleAction("dPpatients", "httpreq_list_patients");
  
  var oForm = document.find;
  if(oForm) {
    url.addElement(oForm.nom);
    url.addElement(oForm.prenom);
    url.addElement(oForm.naissance);
    url.addElement(oForm.Date_Day);
    url.addElement(oForm.Date_Month);
    url.addElement(oForm.Date_Year);
  }
  url.addParam("board"   , "1");

  url.requestUpdate("patients");
}

function updateListHospi(typeHospi) {
  var url = new Url;
  url.setModuleAction("dPboard", "httpreq_vw_hospi");

  url.addParam("chirSel" , "{{$app->user_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("typeHospi", typeHospi);
  url.addParam("board"   , "1");

  url.requestUpdate(typeHospi);
}

function pageMain() {
  hideIcon("consultations");
  hideIcon("operations");
  hideIcon("hospi");
  hideIcon("patients");
  updateListConsults();
  updateListOperations();
  updateListPatients();
  updateListHospi("entree");
  updateListHospi("sortie");
  regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<table class="main">
  <tr>
    <th colspan="2">
      <form name="editFrmPratDate" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      {{$date|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
      </form>
    </th>
  </tr>
  <tr>
    <td class="halfPane" onmouseover="showIcon('consultations')" onmouseout="hideIcon('consultations')">
      <div style="position:absolute" id="icon-consultations">
        <a href="index.php?m=dPcabinet&amp;tab=edit_consultation&amp;date={{$date}}">
          <img src="modules/dPcabinet/images/dPcabinet.png" height="24px" width="24px" />
        </a>
      </div>
      <div style="overflow: auto; height: 250px;" id="consultations">
      </div>
    </td>
    <td class="halfPane" onmouseover="showIcon('operations')" onmouseout="hideIcon('operations')">
      <div style="position:absolute" id="icon-operations">
        <a href="index.php?m=dPplanningOp&amp;tab=vw_idx_patients">
          <img src="modules/dPplanningOp/images/dPplanningOp.png" height="24px" width="24px" />
        </a>
      </div>
      <div style="overflow: auto; height: 250px;" id="operations">
      </div>
    </td>
  </tr>
  <tr>
    <td onmouseover="showIcon('patients')" onmouseout="hideIcon('patients')">
      <div style="position:absolute" id="icon-patients">
        <a href="index.php?m=dPpatients&amp;tab=vw_idx_planning&amp;date={{$date}}">
          <img src="modules/dPpatients/images/dPpatients.png" height="24px" width="24px" />
        </a>
      </div>
      <div style="overflow: auto; height: 250px;" id="patients">
      </div>
    </td>
    <td onmouseover="showIcon('hospi')" onmouseout="hideIcon('hospi')">
      <div style="position:absolute" id="icon-hospi">
        <img src="modules/dPhospi/images/dPhospi.png" height="24px" width="24px" />
      </div>
      <div style="overflow: auto; height: 250px;">
        <div id="sortie">
        </div>
        <div id="entree">
        </div>
      </div>
    </td>
  </tr>
</table>