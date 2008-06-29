{{mb_include_script module="dPcompteRendu" script="document"}}
{{mb_include_script module="dPcompteRendu" script="modele_selector"}}

<script type="text/javascript">
var notWhitespace   = /\S/;

Document.refreshList = function(){
  updateListOperations();
  updateListHospi("sortie");
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

  url.addParam("chirSel"   , "{{$prat->_id}}");
  url.addParam("date"      , "{{$date}}");
  url.addParam("vue2"      , "{{$vue}}");
  url.addParam("selConsult", "");
  url.addParam("board"     , "1");

  url.requestUpdate("consultations");
}

function updateListOperations() {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "httpreq_vw_list_operations");

  url.addParam("chirSel" , "{{$prat->_id}}");
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
    url.addParam("jeuneFille", oForm.jeuneFille.value);
    url.addParam("patient_ipp", oForm.patient_ipp.value);
  }
  url.addParam("board"   , "1");

  url.requestUpdate("patients");
}

function updateListHospi() {
  var url = new Url;
  url.setModuleAction("dPboard", "httpreq_vw_hospi");

  url.addParam("chirSel" , "{{$prat->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("board"   , "1");

  url.requestUpdate("hospi");
}

function updateSemainier() {
  var url = new Url;
  url.setModuleAction("dPboard", "httpreq_semainier");

  url.addParam("chirSel" , "{{$prat->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("board"   , "1");

  url.requestUpdate("semainier");
}

function pageMain() {
  hideIcon("consultations");
  hideIcon("operations");
  hideIcon("hospi");
  hideIcon("patients");
  {{if $prat->_id}}
    updateListConsults();
    updateListOperations();
    updateListPatients();
    updateListHospi();
  {{/if}}
  ViewPort.SetAvlHeight("consultations", 0.5);
  ViewPort.SetAvlHeight("operations", 0.5);
  ViewPort.SetAvlHeight("patients", 1);
  ViewPort.SetAvlHeight("hospi", 1);
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<!-- Script won't be evaled in Ajax inclusion. Need to force it -->
{{mb_include_script path="includes/javascript/intermax.js"}}

<table class="main">
  {{include file=inc_board.tpl}}

  <tr>
    <th colspan="2">
      <form name="editFrmPratDate" action="?m={{$m}}" method="get">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$prec}}">&lt;&lt;&lt;</a>
      <input type="hidden" name="m" value="{{$m}}" />
      {{$date|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$suiv}}">&gt;&gt;&gt;</a>
      </form>
    </th>
  </tr>

  <tbody class="viewported">
  <tr>
    <!--  Consultations -->
    <td class="viewport" onmouseover="showIcon('consultations')" onmouseout="hideIcon('consultations')">
      <div style="position:absolute" id="icon-consultations">
        <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;date={{$date}}">
          <img src="images/modules/dPcabinet.png" height="24" width="24" />
        </a>
      </div>
      <div id="consultations">
      </div>
    </td>
    
    <!-- Operations -->
    <td class="viewport" onmouseover="showIcon('operations')" onmouseout="hideIcon('operations')">
      <div style="position:absolute" id="icon-operations">
        <a href="?m=dPplanningOp&amp;tab=vw_idx_patients">
          <img src="images/modules/dPplanningOp.png" height="24" width="24" />
        </a>
      </div>
      <div id="operations">
      </div>
    </td>
  </tr>
  
  <tr>
    <!-- Recherche de patients -->
    <td class="viewport" id="patients-viewport" onmouseover="showIcon('patients')" onmouseout="hideIcon('patients')">
      <div style="position:absolute" id="icon-patients">
        <a href="?m=dPpatients&amp;tab=vw_idx_planning&amp;date={{$date}}">
          <img src="images/modules/dPpatients.png" height="24" width="24" />
        </a>
      </div>
      <div id="patients" style="overflow: auto">
      </div>
    </td>

    <!-- Patients hospitalisés -->
    <td class="viewport" onmouseover="showIcon('hospi')" onmouseout="hideIcon('hospi')">
      <div style="position:absolute" id="icon-hospi">
        <img src="images/modules/dPhospi.png" height="24" width="24" />
      </div>
      <div id="hospi" style="overflow: auto">
      </div>
    </td>
  </tr>
  </tbody>
  
</table>