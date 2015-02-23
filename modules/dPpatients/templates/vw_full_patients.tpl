<!-- $Id$ -->

{{mb_script module="compteRendu" script="document"}}
{{mb_script module="files" script="files"}}
{{mb_script module="files" script="file"}}
{{mb_include module="files" template="yoplet_uploader" object=$object}}

<script>
  function viewCompleteItem(object_guid) {
    var url = new Url("system", "httpreq_vw_complete_object");
    url.addParam("object_guid", object_guid);
    url.requestUpdate("listView");
  }

  function loadSejour(sejour_id){
    var url = new Url("patients","httpreq_vw_dossier_sejour");
    url.addParam("sejour_id",sejour_id);
    url.requestUpdate("listView");
  }

  function saveObjectInfos(oObject){
    var url = new Url("patients", "httpreq_save_classKey");
    url.addParam("selClass", oObject.objClass);
    url.addParam("selKey", oObject.id);
    url.requestUpdate('systemMsg');
  }

  function view_labo_patient() {
    var url = new Url("Imeds", "httpreq_vw_patient_results");
    url.addParam("patient_id", "{{$patient->_id}}");
    url.requestUpdate('listView');
  }

  function view_labo_sejour(sejour_id) {
    var url = new Url("Imeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate('listView');
  }

  Main.add(function () {
    {{if $consultation_id}}
    viewCompleteItem('CConsultation-{{$consultation_id}}');
    {{/if}}

    {{if $operation_id}}
    viewCompleteItem('COperation-{{$operation_id}}');
    {{/if}}

    {{if $sejour_id}}
    loadSejour('{{$sejour_id}}');
    {{/if}}
  });
</script>

<table class="main">
  <tr>
    <td style="display: none;">
      <form name="FrmClass" action="?m={{$m}}" method="get" onsubmit="reloadListFile('load'); return false;">
        <input type="hidden" name="selKey"   value="" />
        <input type="hidden" name="selClass" value="" />
        <input type="hidden" name="selView"  value="" />
        <input type="hidden" name="keywords" value="" />
        <input type="hidden" name="file_id"  value="" />
        <input type="hidden" name="typeVue"  value="1" />
      </form>
    </td>

    <td id="listInfosPat" style="width: 200px;">
      {{assign var="href" value="?m=dPpatients&tab=vw_full_patients"}}
      {{mb_include module="patients" template="inc_vw_full_patients"}}
    </td>

    <td class="greedyPane">
      {{if "dmp"|module_active}}
        {{mb_include module="dmp" template="inc_dossier_patient_dmp"}}
      {{/if}}
      <div id="listView">
        {{mb_include module="patients" template="CPatient_complete"}}
      </div>
    </td>
  </tr>
</table>