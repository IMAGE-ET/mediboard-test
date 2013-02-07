<script type="text/javascript">

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
