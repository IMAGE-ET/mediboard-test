{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="cabinet" script="edit_consultation"}}
{{mb_script module="planningOp" script="operation"}}

{{mb_script module="soins" script="plan_soins"}}

{{if "dPprescription"|module_active}}
  {{mb_script module="prescription" script="prescription"}}
  {{mb_script module="prescription" script="element_selector"}}
{{/if}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="medicament" script="medicament_selector"}}
  {{mb_script module="medicament" script="equivalent_selector"}}
{{/if}}

{{if "messagerie"|module_active}}
  {{mb_script module="messagerie" script="UserEmail"}}
{{/if}}

<script type="text/javascript">

Consultation.useModal();
Operation.useModal();

updateListConsults = function(withClosed) {
  var url = new Url("cabinet", "httpreq_vw_list_consult");
  url.addParam("chirSel"   , "{{$prat->_id}}");
  url.addParam("date"      , "{{$date}}");
  url.addParam("vue2"      , "{{$vue}}");
  url.addParam("selConsult", "");
  url.addParam("board"     , "1");
  if (!Object.isUndefined(withClosed)) {
    url.addParam('withClosed', withClosed);
  }
  url.requestUpdate("consultations");
};

initUpdateListConsults = function() {
  var url = new Url("cabinet", "httpreq_vw_list_consult");
  url.addParam("chirSel"   , "{{$prat->_id}}");
  url.addParam("date"      , "{{$date}}");
  url.addParam("vue2"      , "{{$vue}}");
  url.addParam("selConsult", "");
  url.addParam("board"     , "1");
  url.periodicalUpdate("consultations", { frequency: 120 } );
};

updateListOperations = function() {
  var url = new Url("planningOp", "httpreq_vw_list_operations");
  url.addParam("pratSel" , "{{$prat->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("urgences", "0");
  url.addParam("board"   , "1");
  url.requestUpdate("operations");
};

initUpdateListOperations = function() {
  var url = new Url("planningOp", "httpreq_vw_list_operations");
  url.addParam("pratSel" , "{{$prat->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("urgences", "0");
  url.addParam("board"   , "1");
  url.periodicalUpdate("operations", { frequency: 120 } );
};

updateListHospi = function() {
  var url = new Url("board", "httpreq_vw_hospi");
  url.addParam("chirSel" , "{{$prat->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.requestUpdate("hospi");
};

updateWorkList = function() {
  var url = new Url("board", "ajax_worklist");
  url.addParam("chirSel" , "{{$prat->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.requestUpdate("worklist");
};

showDossierSoins = function(sejour_id, date, default_tab){
  var url = new Url("soins", "ajax_vw_dossier_sejour");
  url.addParam("sejour_id", sejour_id);
  url.addParam("modal", "1");
  if (default_tab){
    url.addParam("default_tab", default_tab);
  }
  url.requestModal("95%", "90%", {
    onClose: updatePrescriptions
  });
  modalWindow = url.modalObject;
};

Main.add(function () {
  {{if $prat->_id}}
    initUpdateListConsults();
    initUpdateListOperations();
    updateListHospi();
    updateWorkList();
  {{/if}}
  ViewPort.SetAvlHeight("consultations", 0.5);
  ViewPort.SetAvlHeight("operations", 0.5);
  ViewPort.SetAvlHeight("worklist", 1.0);
  ViewPort.SetAvlHeight("hospi", 1.0);
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
});

</script>

<table class="main">
  <tr>
    <th colspan="2">
      <a id="vw_day_date_a" href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$prec}}">&lt;&lt;&lt;</a>
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
      <td id="vw_day_consultations_td" class="viewport" style="width: 50%">
        <div id="consultations"></div>
      </td>

      <!-- Operations -->
      <td id="vw_day_operations_td" class="viewport" style="width: 50%">
        <div id="operations"></div>
      </td>
    </tr>

    <tr>
      <!-- Volet des worklists -->
      <td id="vw_day_worklist_td" class="viewport" style="width: 50%">
        <div id="worklist" style="overflow: auto"></div>
      </td>

      <!-- Patients hospitalisés -->
      <td id="vw_day_hospi_td" class="viewport" style="width: 50%">
        <div id="hospi" style="overflow: auto"></div>
      </td>
    </tr>
  </tbody>
</table>