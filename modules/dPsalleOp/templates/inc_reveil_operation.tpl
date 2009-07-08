{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module=dPprescription script=prescription_med}}

{{assign var="sejour"  value=$op_reveil->_ref_sejour}}
{{assign var="patient" value=$sejour->_ref_patient}}

<script type="text/javascript">
Main.add(function () {
  if($('prescription_sejour')){
    Prescription.reloadPrescSejour('','{{$op_reveil->_ref_sejour->_id}}', null, null, '{{$op_reveil->_id}}', null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}});
  }
  
  if($('soins')){
    loadTraitement('{{$op_reveil->sejour_id}}','{{$date}}','','administration');
  }
  
  if($('Imeds_tab')){
    var url = new Url;
    url.setModuleAction("dPImeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", {{$sejour->_id}});
    url.requestUpdate('Imeds_tab', { waitingText : null });
  }
});


function loadTraitement(sejour_id, date, nb_decalage, mode_dossier, object_id, object_class, unite_prise, chapitre) {
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_dossier_soin");
  url.addParam("sejour_id", sejour_id);
  url.addParam("date", date);
  url.addParam("line_type", "bloc");
  url.addParam("mode_bloc", "1");
  url.addParam("mode_dossier", mode_dossier);
  if(nb_decalage){
    url.addParam("nb_decalage", nb_decalage);
  }
  url.addParam("chapitre", chapitre);
  url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("unite_prise", unite_prise);
    
    if(object_id && object_class){
      if(object_class == 'CPerfusion'){
        url.requestUpdate("line_"+object_class+"-"+object_id, { waitingText: null , onComplete: function() { 
          $("line_"+object_class+"-"+object_id).hide();
          moveDossierSoin($("line_"+object_class+"-"+object_id));
        } } );
      }
      else {
        first_td = $('first_'+object_id+"_"+object_class+"_"+unite_prise);
        last_td = $('last_'+object_id+"_"+object_class+"_"+unite_prise);
        
        // Suppression des td entre les 2 td bornes
        td = first_td;
        first_td.colSpan = 0;
        
        while(td.next().id != last_td.id){
          if(td.next().visible()){
            first_td.colSpan = first_td.colSpan + 1;
          }
          td.next().remove();
          first_td.show();
        }
        
        unite_prise = unite_prise.replace(/[^a-z0-9_-]/gi, '_');
        //unite_prise = unite_prise.replace(/\(/g, '_').replace(/\)/g, '_').replace(/\//g, '_').replace(/ /g, '');
                
        url.requestUpdate(first_td, {
                          waitingText: null, 
                          insertion: Insertion.After,
                          onComplete: function(){
                            moveDossierSoin($("line_"+object_class+"_"+object_id+"_"+unite_prise));
                            first_td.hide().colSpan = 0;
                          }
                          } );
      }
    } else {
      if(chapitre){
        if(chapitre == "med" || chapitre == "perf" || chapitre == "inj"){
          chapitre = "_"+chapitre;
        } else {
          chapitre = "_cat-"+chapitre;
        }
        url.requestUpdate(chapitre, { onComplete: function() { moveDossierSoin($(chapitre)); } } );
      } else {
        url.requestUpdate("soins", { waitingText: null } );
      }
    }
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
    if(oForm.object_class.value != "" || oForm.libelle_ATC.value != ''){
      // Refresh de la partie administration
      loadTraitement(sejour_id,'{{$date}}','','administration');
    }  
  } });
}

function reloadPrescription(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '', null, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}});
}
 
</script>

<!-- Informations générales sur l'intervention et le patient -->
<table class="tbl">
  <tr>
    <th class="title text">
      <button class="hslip notext" id="listpatients-trigger" type="button" style="float:left">
        {{tr}}Programme{{/tr}}
      </button>
      <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}"'>
        {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$patient size=42}}
      </a>
      <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
        <img src="images/icons/edit.png" alt="modifier" />
       </a>
      {{$patient->_view}}
      ({{$patient->_age}} ans
      {{if $patient->_age != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
      &mdash; Dr {{$op_reveil->_ref_chir->_view}}
      <br />
      {{if $op_reveil->libelle}}{{$op_reveil->libelle}} &mdash;{{/if}}
      {{mb_label object=$op_reveil field=cote}} : {{mb_value object=$op_reveil field=cote}}
      
    </th>
  </tr>
  
  {{if $op_reveil->_ref_sejour->rques || $op_reveil->rques || $op_reveil->materiel}}
  <!-- Mise en avant du matériel et remarques -->
  <tr>
    {{if $op_reveil->_ref_sejour->rques || $op_reveil->rques}}
    <td class="text big-warning">
      {{if $op_reveil->_ref_sejour->rques}}
      <strong>{{mb_label object=$op_reveil->_ref_sejour field=rques}}</strong>
      {{mb_value object=$op_reveil->_ref_sejour field=rques}}
      {{/if}}
      {{if $op_reveil->rques}}
      <strong>{{mb_label object=$op_reveil field=rques}}</strong>
      {{mb_value object=$op_reveil field=rques}}
      {{/if}}
    </td>
    {{/if}}
  </tr>
  {{/if}}
</table>

<!-- Tabulations -->
<ul id="main_tab_group" class="control_tabs">
  <li><a href="#diag_tab">Diagnostics</a></li>
  <li><a href="#codage_tab">Actes</a></li>
  <li><a href="#dossier_tab">Dossier</a></li>

  {{if $isPrescriptionInstalled}}
    <li><a href="#prescription_sejour_tab">Prescription</a></li>
    <li onclick="loadTraitement('{{$op_reveil->sejour_id}}','{{$date}}','','administration');"><a href="#soins">Soins</a></li>
  {{/if}}
  
  {{if $isImedsInstalled}}
    <li><a href="#Imeds_tab">Labo</a></li>
  {{/if}}
</ul>
  
<hr class="control_tabs" />

<!-- codage diagnostics CIM -->
<div id="diag_tab" style="display:none">
  <div id="cim">
    {{include file="inc_diagnostic_principal.tpl" modeDAS=true}}
  </div>
</div>

{{if !$dPconfig.dPsalleOp.mode_anesth}}
<!-- codage des acte ccam et ngap -->
<div id="codage_tab" style="display:none">

  {{if $can->edit || $modif_operation}}
  <form name="infoFactu" action="?m={{$m}}" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  <input type="hidden" name="operation_id" value="{{$op_reveil->_id}}" />
  <input type="hidden" name="del" value="0" />
  <table class="form">
    <tr>
      <th style="text-align: right">
        {{mb_label object=$op_reveil field=anapath}}
      </th>
      <td>
        {{mb_field object=$op_reveil field=anapath typeEnum="radio" onChange="submitFormAjax(this.form, 'systemMsg');"}}
      </td>
      <th style="text-align: right">
        {{mb_label object=$op_reveil field=prothese}}
      </th>
      <td>
        {{mb_field object=$op_reveil field=prothese typeEnum="radio" onChange="submitFormAjax(this.form, 'systemMsg');"}}
      </td>
    </tr>
    <tr>
      <th style="text-align: right">
        {{mb_label object=$op_reveil field=labo}}
      </th>
      <td style="vertical-align:middle;">     
        {{mb_field object=$op_reveil field=labo typeEnum="radio" onChange="submitFormAjax(this.form, 'systemMsg');"}}
      </td>
      <td colspan="2" />
    </tr>
  </table>
  </form>
  {{/if}}
  <ul id="codage_tab_group" class="control_tabs">
    <li><a href="#ccam_tab">CCAM</a></li>
    <li><a href="#ngap_tab">NGAP</a></li>
  </ul>
  
  <hr class="control_tabs" />
  
  <div id="ccam_tab" style="display:none">
    <div id="ccam">
      {{assign var="subject" value=$op_reveil}}
      {{mb_include module=dPsalleOp template=inc_codage_ccam}}
    </div>
  </div>

  <div id="ngap_tab" style="display:none">
    <div id="listActesNGAP">
      {{assign var="object" value=$op_reveil}}
      {{mb_include module=dPcabinet template=inc_codage_ngap}}
    </div>
  </div>
</div>

<!-- Dossier Medical et documents-->
{{assign var="dossier_medical" value=$op_reveil->_ref_sejour->_ref_dossier_medical}}
<div id="dossier_tab" style="display:none">
  <table class="form">
    <tr>
      <th class="title">Documents</th>
    </tr>
    <tr>
      <td>
        <div id="documents">
          {{mb_include_script module="dPcompteRendu" script="document"}}
          {{mb_include_script module="dPcompteRendu" script="modele_selector"}}
          {{include file=../../dPplanningOp/templates/inc_documents_operation.tpl operation=$op_reveil}}
        </div>
      </td>
    </tr>
    <tr>
      <td>
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
      </td>
    </tr>
  </table>
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

{{/if}}

{{if $isImedsInstalled}}
<!-- Affichage de la prescription -->
<div id="Imeds_tab" style="display:none">
</div>
{{/if}}