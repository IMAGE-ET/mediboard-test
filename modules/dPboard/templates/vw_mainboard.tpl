<script type="text/javascript">
var notWhitespace   = /\S/;

function viewItem(class, id, date) {
  
  Dom.cleanWhitespace($('viewTooltip'));
  var oDiv = $('viewTooltip').childNodes;

  $H(oDiv).each(function (pair) {
    if(typeof pair.value == "object"){
      $(pair.value["id"]).hide();
    }
  });

  oElement = $(class+id);
  oElement.show();
  
  if(oElement.alt == "infos - cliquez pour fermer") {
    return;
  }
  
  url = new Url;
  url.addParam("board"     , "1");
  url.addParam("boardItem" , "1");
  
  if(class == "CPlageconsult"){
    url.setModuleAction("dPcabinet", "httpreq_vw_list_consult");
    url.addParam("plageconsult_id", id);
    url.addParam("date"           , date);
    url.addParam("chirSel"        , "{{$app->user_id}}");
    url.addParam("vue2"           , "{{$vue}}");
    url.addParam("selConsult"     , "");
  }else if(class == "CPlageOp"){
    url.setModuleAction("dPplanningOp", "httpreq_vw_list_operations");
    url.addParam("chirSel" , "{{$app->user_id}}");
    url.addParam("date"    , date);
    url.addParam("urgences", "0");
  }else{
    return;
  }
  url.requestUpdate(oElement);
  oElement.alt = "infos - cliquez pour fermer";
}

function hideItem(class, id) {
  oElement = $(class+id);
  oElement.hide();
}

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

function chgSoundex() {
  var oForm      = document.find;
  var oCheckSoundex = oForm.check_soundex;
  var oSoundex = oForm.soundex;
  if (oCheckSoundex.checked) {
    oSoundex.value     = "on";
  } else {
    oSoundex.value     = "off";
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

function updateListHospi() {
  var url = new Url;
  url.setModuleAction("dPboard", "httpreq_vw_hospi");

  url.addParam("chirSel" , "{{$app->user_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("board"   , "1");

  url.requestUpdate("hospi");
}

function updateSemainier() {
  var url = new Url;
  url.setModuleAction("dPboard", "httpreq_semainier");

  url.addParam("chirSel" , "{{$app->user_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("board"   , "1");

  url.requestUpdate("semainier");
}

function pageMain() {
  {{if $view == "day"}}
    hideIcon("consultations");
    hideIcon("operations");
    hideIcon("hospi");
    hideIcon("patients");
    updateListConsults();
    updateListOperations();
    updateListPatients();
    updateListHospi();
  {{/if}}
  {{if $view == "week"}}
    updateSemainier();
  {{/if}}
  regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<table class="main">
  <tr>
    <th>
      <form name="editFrmPratDate" action="?m={{$m}}" method="get">
      <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;date={{$prec}}">&lt;&lt;&lt;</a>
      <input type="hidden" name="m" value="{{$m}}" />
      {{$date|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
      <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;date={{$suiv}}">&gt;&gt;&gt;</a>
      </form>
    </th>
    <th>
      <form name="editFrmView" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <select name="view" onchange="this.form.submit()">
        <option value="day" {{if $view == "day"}}selected="selected"{{/if}}>
          Journée
        </option>
        <option value="week" {{if $view == "week"}}selected="selected"{{/if}}>
          Semainier
        </option>
      </select>
      </form>
    </th>
  </tr>
  {{if $view == "day"}}
  <tr>
    <td style="border: 1px dotted #000;" class="halfPane" onmouseover="showIcon('consultations')" onmouseout="hideIcon('consultations')">
      <div style="position:absolute" id="icon-consultations">
        <a href="index.php?m=dPcabinet&amp;tab=edit_consultation&amp;date={{$date}}">
          <img src="modules/dPcabinet/images/dPcabinet.png" height="24px" width="24px" />
        </a>
      </div>
      <div style="overflow: auto; height: 250px;" id="consultations">
      </div>
    </td>
    <td style="border: 1px dotted #000;" class="halfPane" onmouseover="showIcon('operations')" onmouseout="hideIcon('operations')">
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
    <td style="border: 1px dotted #000;" onmouseover="showIcon('patients')" onmouseout="hideIcon('patients')">
      <div style="position:absolute" id="icon-patients">
        <a href="index.php?m=dPpatients&amp;tab=vw_idx_planning&amp;date={{$date}}">
          <img src="modules/dPpatients/images/dPpatients.png" height="24px" width="24px" />
        </a>
      </div>
      <div style="overflow: auto; height: 250px;" id="patients">
      </div>
    </td>
    <td style="border: 1px dotted #000;" onmouseover="showIcon('hospi')" onmouseout="hideIcon('hospi')">
      <div style="position:absolute" id="icon-hospi">
        <img src="modules/dPhospi/images/dPhospi.png" height="24px" width="24px" />
      </div>
      <div style="overflow: auto; height: 250px;">
        <div id="hospi">
        </div>
      </div>
    </td>
  </tr>
  {{/if}}
  {{if $view == "week"}}
  <tr>
    <td style="border: 1px dotted #000;" colspan="2">
      <div id="semainier">
      </div>
    </td>
  </tr>
  {{/if}}
</table>