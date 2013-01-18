{{mb_script module="dPcabinet" script="edit_consultation"}}

<script type="text/javascript">
{{if !$consult->_canEdit}}
  App.readonly = true;
{{/if}}

function reloadDiagnostic(sejour_id, modeDAS) {
  var url = new Url('salleOp', 'httpreq_diagnostic_principal');
  url.addParam("sejour_id", sejour_id);
  url.addParam("modeDAS", modeDAS);
  url.requestUpdate("cim");
}

function printAllDocs() {
  var url = new Url('cabinet', 'print_select_docs');
  url.addElement(document.editFrmFinish.consultation_id);
  url.popup(700, 500, "printDocuments");
}

function submitAll() {
  return onSubmitFormAjax(getForm("editFrmExams"));
}

Main.add(function () {
  ListConsults.init("{{$consult->_id}}", "{{$userSel->_id}}", "{{$date}}", "{{$vue}}", "{{$current_m}}");
      
  if (document.editAntFrm){
    document.editAntFrm.type.onchange();
  } 
   
  {{if $consult->_id}}
  // Chargement des antecedents, traitements, diagnostics du patients
  DossierMedical.reloadDossierPatient();
  {{/if}}
});
</script>

<table class="main">
  <tr>
    <td id="listConsult" style="width: 240px;"></td>
    <td>{{mb_include module=cabinet template=inc_full_consult}}</td>
  </tr>
</table>
