{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="dPcabinet" script="edit_consultation"}}

<script type="text/javascript">

Consultation.edit = Consultation.editModal;

function hideIcon(frame) {
  $("icon-" + frame).hide();
}

function showIcon(frame) {
  $("icon-" + frame).show();
}

function updateListConsults() {
  var url = new Url("dPcabinet", "httpreq_vw_list_consult");
  url.addParam("chirSel"   , "{{$prat->_id}}");
  url.addParam("date"      , "{{$date}}");
  url.addParam("vue2"      , "{{$vue}}");
  url.addParam("selConsult", "");
  url.addParam("board"     , "1");
  url.requestUpdate("consultations");
}

function initUpdateListConsults() {
  var url = new Url("dPcabinet", "httpreq_vw_list_consult");
  url.addParam("chirSel"   , "{{$prat->_id}}");
  url.addParam("date"      , "{{$date}}");
  url.addParam("vue2"      , "{{$vue}}");
  url.addParam("selConsult", "");
  url.addParam("board"     , "1");
  url.periodicalUpdate("consultations", { frequency: 90 } );
}

function updateListOperations() {
  var url = new Url("dPplanningOp", "httpreq_vw_list_operations");
  url.addParam("chirSel" , "{{$prat->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("urgences", "0");
  url.addParam("board"   , "1");
  url.requestUpdate("operations");
}

function updateListPatients() {
  var url = new Url("dPpatients", "httpreq_list_patients");
  
  var oForm = getForm("find");
  if(oForm) {
    url.addElement(oForm.nom);
    url.addElement(oForm.prenom);
    url.addElement(oForm.naissance);
    url.addElement(oForm.Date_Day);
    url.addElement(oForm.Date_Month);
    url.addElement(oForm.Date_Year);
    url.addElement(oForm.patient_ipp);
    url.addElement(oForm.prat_id);
    url.addElement(oForm.sexe);
  }
  url.addParam("board"   , 1);
  url.requestUpdate("patients");
  
  return false;
}

function updateListHospi() {
  var url = new Url("dPboard", "httpreq_vw_hospi");
  url.addParam("chirSel" , "{{$prat->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("board"   , "1");
  url.requestUpdate("hospi");
}

Main.add(function () {
  hideIcon("consultations");
  hideIcon("operations");
  hideIcon("hospi");
  hideIcon("patients");
  {{if $prat->_id}}
    initUpdateListConsults();
    updateListOperations();
    updateListPatients();
    updateListHospi();
  {{/if}}
  ViewPort.SetAvlHeight("consultations", 0.5);
  ViewPort.SetAvlHeight("operations", 0.5);
  ViewPort.SetAvlHeight("patients", 1);
  ViewPort.SetAvlHeight("hospi", 1);
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
});

</script>

<table class="main">
  <tr>
    <th colspan="2">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$prec}}">&lt;&lt;&lt;</a>
      <form name="changeDate" action="?m={{$m}}" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        {{$date|date_format:$conf.longdate}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$suiv}}">&gt;&gt;&gt;</a>
    </th>
  </tr>

  <tbody class="viewported">
  
  <tr>

    <!--  Consultations -->
    <td class="viewport" style="width: 50%" onmouseover="showIcon('consultations')" onmouseout="hideIcon('consultations')">
      <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;date={{$date}}&amp;chirSel={{$prat->_id}}" style="position:absolute" id="icon-consultations">
        <img src="modules/dPcabinet/images/icon.png" height="24" width="24" />
      </a>
      <div id="consultations"></div>
    </td>
    
    <!-- Operations -->
    <td class="viewport" style="width: 50%" onmouseover="showIcon('operations')" onmouseout="hideIcon('operations')">
      <a href="?m=dPplanningOp&amp;tab=vw_idx_patients"style="position:absolute" id="icon-operations">
        <img src="modules/dPplanningOp/images/icon.png" height="24" width="24" />
      </a>
      <div id="operations"></div>
    </td>
    
  </tr>
  
  <tr>
  
    <!-- Recherche de patients -->
    <td class="viewport" style="width: 50%" id="patients-viewport" onmouseover="showIcon('patients')" onmouseout="hideIcon('patients')">
      <a href="?m=dPpatients&amp;tab=vw_idx_planning&amp;date={{$date}}" style="position:absolute" id="icon-patients">
        <img src="modules/dPpatients/images/icon.png" height="24" width="24" />
      </a>
      <div id="patients" style="overflow: auto"></div>
    </td>

    <!-- Patients hospitalisés -->
    <td class="viewport" style="width: 50%" onmouseover="showIcon('hospi')" onmouseout="hideIcon('hospi')">
      <img src="modules/dPhospi/images/icon.png" height="24" width="24" style="position:absolute" id="icon-hospi" />
      <div id="hospi" style="overflow: auto"></div>
    </td>
    
  </tr>
  
  </tbody>
  
</table>