{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="bloodSalvage" script="bloodSalvage"}}


{{assign var="sejour" value=$selOp->_ref_sejour}}
{{assign var="patient" value=$sejour->_ref_patient}}

<script type="text/javascript">
Main.add(function () {
  if($('prescription_sejour')){
    Prescription.reloadPrescSejour('','{{$selOp->_ref_sejour->_id}}', null, null, '{{$selOp->_id}}', null, null, true);
  }
  
  if($('bloodSalvage')){
    var url = new Url;
    url.setModuleAction("bloodSalvage", "httpreq_vw_bloodSalvage");
    url.addParam("op","{{$selOp->_id}}");
    url.requestUpdate("bloodSalvage", {waitingText: null});
  }
  
  if($('Imeds_tab')){
    var url = new Url;
    url.setModuleAction("dPImeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", {{$sejour->_id}});
    url.requestUpdate('Imeds_tab', { waitingText : null });
  }
});


function loadTraitement(sejour_id, date) {
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_dossier_soin");
  url.addParam("sejour_id", sejour_id);
  url.addParam("date", date);
  url.addParam("line_type", "bloc");
  url.addParam("mode_bloc", "1");
  url.requestUpdate("soins", { waitingText: null } );
}

function loadSuivi(sejour_id) {
  if(sejour_id) {
    var urlSuivi = new Url;
    urlSuivi.setModuleAction("dPhospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.requestUpdate("dossier_suivi", { waitingText: null } );
  }
}

function submitSuivi(oForm, prescription_id) {
  sejour_id = oForm.sejour_id.value;
  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { 
    loadSuivi(sejour_id); 
    if(oForm.object_class.value != ""){
      // Refresh de la partie administration
      loadTraitement(sejour_id,'{{$date}}');
    }  
  } });
}

function reloadPrescription(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '', null, null, null, null, null, true);
}
 
</script>

<!-- Informations générales sur l'intervention et le patient -->
<table class="form">
  <tr>
    <th class="title text" colspan="2">
      <button class="hslip notext" id="listplages-trigger" type="button" style="float:left">
        {{tr}}Programme{{/tr}}
      </button>
      <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
        <img src="images/icons/edit.png" alt="modifier" />
 			</a>
      {{$patient->_view}}
      ({{$patient->_age}} ans
      {{if $patient->_age != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
      &mdash; Dr {{$selOp->_ref_chir->_view}}
      <br />
      {{if $selOp->libelle}}{{$selOp->libelle}} &mdash;{{/if}}
      {{mb_label object=$selOp field=cote}} : {{mb_value object=$selOp field=cote}}
      &mdash; {{mb_label object=$selOp field=temp_operation}} : {{mb_value object=$selOp field=temp_operation}}
      
    </th>
  </tr>
  
  <!-- Mise en avant du matériel et remarques -->
  {{if $selOp->materiel}}
  <tr>
    <td class="text">
      <strong>{{mb_label object=$selOp field=materiel}} :</strong> 
      {{mb_value object=$selOp field=materiel}}
    </td>
  </tr>
  {{/if}}
  
  {{if $selOp->rques}}
  <tr>
    <td class="text">
      <strong>{{mb_label object=$selOp field=rques}} :</strong> 
      {{mb_value object=$selOp field=rques}}
    </td>
  </tr>
  {{/if}}
</table>

<!-- Tabulations -->
<ul id="main_tab_group" class="control_tabs">
  <li><a href="#one">Timings</a></li>
  <li onclick="reloadAnesth('{{$selOp->_id}}');"><a href="#two">Anesthésie</a></li>
  {{if $isbloodSalvageInstalled}}
  <li><a href="#bloodSalvage">Cell Saver</a></li>
  {{/if}}
  <li><a href="#threebis">Diagnostics</a></li>
  <li><a href="#three">CCAM</a></li>
  <li><a href="#four">NGAP</a></li>
  <li><a href="#five">Dossier</a></li>
  {{if $isPrescriptionInstalled}}
    <li><a href="#prescription_sejour_tab">Prescription</a></li>
    <li onclick="loadTraitement('{{$selOp->sejour_id}}','{{$date}}');"><a href="#soins">Soins</a></li>
  {{/if}}
  {{if $isImedsInstalled}}
    <li><a href="#Imeds_tab">Labo</a></li>
  {{/if}}
</ul>
  
<hr class="control_tabs" />

<!-- Premier onglet => Timings + Personnel -->
<div id="one" style="display:none">
 	<div id="timing">
    {{include file="inc_vw_timing.tpl"}}
  </div>
  <div id="listPersonnel">
    {{include file="inc_vw_personnel.tpl"}}
  </div>
</div>

<!-- Deuxieme onglet => Anesthesie -->
<div id="two" style="display:none">
  <div id="anesth">
  {{include file="inc_vw_anesth.tpl"}}
  </div>
  <div id="info_anesth">
  {{include file="inc_vw_info_anesth.tpl"}}
  </div>
</div>
{{if $isbloodSalvageInstalled}}
<!--  Cell Saver -->
<div id="bloodSalvage" style="display:none"></div>
{{/if}}
<!-- Troisieme onglet bis: codage diagnostics CIM -->
<div id="threebis" style="display:none">
  <div id="cim">
    {{include file="inc_diagnostic_principal.tpl" modeDAS=true}}
  </div>
</div>

<!-- Troisieme onglet: codage acte ccam -->
<div id="three" style="display:none">
  <div id="ccam">
    {{assign var="subject" value=$selOp}}
    {{include file="inc_gestion_ccam.tpl"}}
  </div>
</div>
<!-- Fin du troisieme onglet -->

<!-- Quatrième onglet => Codage acte NGAP -->
<div id="four" style="display:none">
  <div id="listActesNGAP">
    {{assign var="object" value=$selOp}}
    {{include file="../../dPcabinet/templates/inc_acte_ngap.tpl"}}
  </div>
</div>

<!-- Cinquieme onglet => Dossier Medical -->
{{assign var="dossier_medical" value=$selOp->_ref_sejour->_ref_dossier_medical}}
<div id="five" style="display:none">
  {{if !$dossier_medical->_id}}
  <div class="big-info">
    Le dossier médical pour ce séjour n'est pas créé, ou ne contient pas d'éléments parmi :
    <ul>
      <li>{{tr}}CAntecedent{{/tr}}</li>
      <li>Diagnostics associés</li>
    </ul>
    Ces informations doivent-être renseignés pendant la consultation de pré-anesthésie
  </div>

	{{else}}
  <div class="text">
		{{include file=../../dPpatients/templates/CDossierMedical_complete.tpl object=$dossier_medical}}
  </div>
  {{/if}}

  <hr />

  <div id="documents">
		{{mb_include_script module="dPcompteRendu" script="document"}}
		{{mb_include_script module="dPcompteRendu" script="modele_selector"}}
    {{include file=../../dPplanningOp/templates/inc_documents_operation.tpl operation=$selOp}}
  </div>
</div>

{{if $isPrescriptionInstalled}}
<!-- Affichage de la prescription -->
<div id="prescription_sejour_tab" style="display:none">
  <div id="prescription_sejour"></div>
</div>
<!-- Affichage du dossier de soins avec les lignes "bloc" -->
<div id="soins" style="display:none">
</div>
{{/if}}

{{if $isImedsInstalled}}
<div id="Imeds_tab" style="display:none">
</div>
{{/if}}