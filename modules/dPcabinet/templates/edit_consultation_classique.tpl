{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="dPprescription" script="prescription_editor"}}
{{mb_include_script module="dPcompteRendu" script="document"}}
{{mb_include_script module="dPcompteRendu" script="modele_selector"}}
{{mb_include_script module="dPcabinet" script="edit_consultation"}}

<script type="text/javascript">
function printAllDocs() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_select_docs"); 
  url.addElement(document.editFrmFinish.consultation_id);
  url.popup(700, 500, "printDocuments");
  return;
}

function submitAll() {
  var oForm = document.editFrmExams;
  submitFormAjax(oForm, 'systemMsg');
}

Main.add(function () {
  ListConsults.init("{{$consult->_id}}", "{{$userSel->_id}}", "{{$date}}", "{{$vue}}", "{{$current_m}}");
} );

</script>


<table class="main">
  <tr>
    <td id="listConsult" style="width: 240px;"></td>
    <td>
			{{include file="../../dPpatients/templates/inc_intermax.tpl"}}
      {{if $consult->_id}}
      {{assign var="patient" value=$consult->_ref_patient}}
      <div id="finishBanner">
      {{include file="../../dPcabinet/templates/inc_finish_banner.tpl"}}
      </div>

      <div id="Infos">
        {{include file="../../dPcabinet/templates/inc_patient_infos_accord_consult.tpl"}}
      </div>

      <div id="mainConsult">
      {{include file="../../dPcabinet/templates/inc_main_consultform.tpl"}}
      </div>

      <div id="fdrConsult">
      {{include file="../../dPcabinet/templates/inc_fdr_consult.tpl"}}
      </div>

      {{/if}}

    </td>
  </tr>
</table>
