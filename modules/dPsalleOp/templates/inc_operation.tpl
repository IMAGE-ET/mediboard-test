{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="bloodSalvage" script="bloodSalvage"}}
{{mb_include_script module=dPprescription script=prescription_med}}

{{assign var="sejour" value=$selOp->_ref_sejour}}
{{assign var="patient" value=$sejour->_ref_patient}}

<script type="text/javascript">
Main.add(function () {
	
	{{if $isPrescriptionInstalled}}
  if($('prescription_sejour')){
    Prescription.reloadPrescSejour('','{{$selOp->_ref_sejour->_id}}', null, null, '{{$selOp->_id}}', null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}});
  }
  {{/if}}
  
  if($('soins')){
    loadTraitement('{{$selOp->sejour_id}}','{{$date}}','','administration');
  }
  
  if($('antecedents')){
    var url = new Url("dPcabinet", "httpreq_vw_antecedents");
    url.addParam("sejour_id","{{$selOp->sejour_id}}");
    url.requestUpdate("antecedents", {waitingText: null});
  }
  
  if($('bloodSalvage_tab')){
    var url = new Url("bloodSalvage", "httpreq_vw_bloodSalvage");
    url.addParam("op","{{$selOp->_id}}");
    url.requestUpdate("bloodSalvage_tab", { waitingText: null});
  }
  
  if($('Imeds_tab')){
    var url = new Url("dPImeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", {{$sejour->_id}});
    url.requestUpdate('Imeds_tab', { waitingText : null });
  }
});


function loadTraitement(sejour_id, date, nb_decalage, mode_dossier, object_id, object_class, unite_prise, chapitre) {
  var url = new Url("dPprescription", "httpreq_vw_dossier_soin");
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

function loadSuivi(sejour_id, user_id) {
  if(sejour_id) {
    var urlSuivi = new Url("dPhospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.addParam("user_id", user_id);
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

{{if $isPrescriptionInstalled}}
function reloadPrescription(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '', null, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}});
}
{{/if}}

</script>

<!-- Informations générales sur l'intervention et le patient -->
<table class="tbl">
  <tr>
    <th class="title text" colspan="2">
      <button class="hslip notext" id="listplages-trigger" type="button" style="float:left">
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
      &mdash; Dr {{$selOp->_ref_chir->_view}}
      {{if $sejour->_ref_curr_affectation->_id}}- {{$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view}}{{/if}}
      <br />
      {{if $selOp->libelle}}{{$selOp->libelle}} &mdash;{{/if}}
      {{mb_label object=$selOp field=cote}} : {{mb_value object=$selOp field=cote}}
      &mdash; {{mb_label object=$selOp field=temp_operation}} : {{mb_value object=$selOp field=temp_operation}}
      
    </th>
  </tr>
  
  {{if $dPconfig.dPplanningOp.COperation.verif_cote && $selOp->cote_bloc && ($selOp->cote == "droit" || $selOp->cote == "gauche")}}
  <!-- Vérification du côté -->
  <tr>
    <td>
      <strong>Côté DHE : {{mb_value object=$selOp field="cote"}}</strong> -
      <span class="{{if !$selOp->cote_admission}}warning{{elseif $selOp->cote_admission != $selOp->cote}}error{{else}}ok{{/if}}">
        Admission : {{mb_value object=$selOp field="cote_admission"}}
      </span> -
      <span class="{{if !$selOp->cote_consult_anesth}}warning{{elseif $selOp->cote_consult_anesth != $selOp->cote}}error{{else}}ok{{/if}}">
        Consult Anesth : {{mb_value object=$selOp field="cote_consult_anesth"}}
      </span> -
      <span class="{{if !$selOp->cote_hospi}}warning{{elseif $selOp->cote_hospi != $selOp->cote}}error{{else}}ok{{/if}}">
        Service : {{mb_value object=$selOp field="cote_hospi"}}
      </span> -
      <span class="{{if !$selOp->cote_bloc}}warning{{elseif $selOp->cote_bloc != $selOp->cote}}error{{else}}ok{{/if}}">
        Bloc : {{mb_value object=$selOp field="cote_bloc"}}
      </span>
    </td>
  </tr>
  {{/if}}
  
  {{if $selOp->_ref_sejour->rques || $selOp->rques || $selOp->materiel}}
  <!-- Mise en avant du matériel et remarques -->
  <tr>
    {{if $selOp->_ref_sejour->rques || $selOp->rques}}
    {{if !$selOp->materiel}}
    <td class="text big-warning" colspan="2">
    {{else}}
    <td class="text big-warning halfPane">
    {{/if}}
      {{if $selOp->_ref_sejour->rques}}
      <strong>{{mb_label object=$selOp->_ref_sejour field=rques}}</strong>
      {{mb_value object=$selOp->_ref_sejour field=rques}}
      {{/if}}
      {{if $selOp->rques}}
      <strong>{{mb_label object=$selOp field=rques}}</strong>
      {{mb_value object=$selOp field=rques}}
      {{/if}}
    </td>
    {{/if}}
    
    {{if $selOp->materiel}}
    {{if !$selOp->_ref_sejour->rques && !$selOp->rques}}
    <td class="text big-info" colspan="2">
    {{else}}
    <td class="text big-info halfPane">
    {{/if}}
      {{if $selOp->materiel}}
      <strong>{{mb_label object=$selOp field=materiel}}</strong>
      {{mb_value object=$selOp field=materiel}}
      {{/if}}
    </td>
    {{/if}}
  </tr>
  {{/if}}
</table>

<!-- Tabulations -->
<ul id="main_tab_group" class="control_tabs">
	{{if !$dPconfig.dPsalleOp.mode_anesth}}
    <li><a href="#timing_tab">Timings</a></li>
	{{/if}}

  {{if $isbloodSalvageInstalled}}
    <li><a href="#bloodSalvage_tab">Cell Saver</a></li>
  {{/if}}

	{{if !$dPconfig.dPsalleOp.mode_anesth}}
    <li><a href="#diag_tab">Diags.</a></li>
    <li><a href="#codage_tab">Actes</a></li>
    <li onmouseup="reloadAnesth('{{$selOp->_id}}');"><a href="#anesth_tab">Anesth.</a></li>
    <li><a href="#dossier_tab">Chir.</a></li>
    <li><a href="#antecedents">Atcd.</a></li>

	  {{if $isPrescriptionInstalled}}
      <li><a href="#prescription_sejour_tab">Prescription</a></li>
      <li onmouseup="loadTraitement('{{$selOp->sejour_id}}','{{$date}}','','administration');"><a href="#soins">Soins</a></li>
	  {{/if}}
	{{/if}}
  
  {{if $isImedsInstalled}}
    <li><a href="#Imeds_tab">Labo</a></li>
  {{/if}}
</ul>
  
<hr class="control_tabs" />

<!-- Timings + Personnel -->
{{if !$dPconfig.dPsalleOp.mode_anesth}}
<div id="timing_tab" style="display:none">
 	<div id="timing">
    {{include file="inc_vw_timing.tpl"}}
  </div>
  <div id="listPersonnel">
    {{include file="inc_vw_personnel.tpl"}}
  </div>
</div>
{{/if}}

{{if $isbloodSalvageInstalled}}
<!--  Cell Saver -->
<div id="bloodSalvage_tab" style="display:none"></div>
{{/if}}
<!-- Troisieme onglet bis: codage diagnostics CIM -->
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
  <input type="hidden" name="operation_id" value="{{$selOp->_id}}" />
  <input type="hidden" name="del" value="0" />
  <table class="form">
    <tr>
      <th style="text-align: right">
        {{mb_label object=$selOp field=anapath}}
      </th>
      <td>
        {{mb_field object=$selOp field=anapath typeEnum="radio" onChange="submitFormAjax(this.form, 'systemMsg');"}}
      </td>
      <th style="text-align: right">
        {{mb_label object=$selOp field=prothese}}
      </th>
      <td>
        {{mb_field object=$selOp field=prothese typeEnum="radio" onChange="submitFormAjax(this.form, 'systemMsg');"}}
      </td>
    </tr>
    <tr>
      <th style="text-align: right">
        {{mb_label object=$selOp field=labo}}
      </th>
      <td style="vertical-align:middle;">     
        {{mb_field object=$selOp field=labo typeEnum="radio" onChange="submitFormAjax(this.form, 'systemMsg');"}}
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
      {{assign var="subject" value=$selOp}}
      {{mb_include module=dPsalleOp template=inc_codage_ccam}}
    </div>
  </div>

  <div id="ngap_tab" style="display:none">
    <div id="listActesNGAP">
      {{assign var="object" value=$selOp}}
      {{mb_include module=dPcabinet template=inc_codage_ngap}}
    </div>
  </div>
</div>

<!-- Anesthesie -->
<div id="anesth_tab" style="display:none">
  <div id="anesth">
    {{include file="inc_vw_anesth.tpl"}}
  </div>  
  <div id="info_anesth">
  {{include file="inc_vw_info_anesth.tpl"}}
  </div>
</div>

<!-- Dossier Medical et documents-->
{{assign var="dossier_medical" value=$selOp->_ref_sejour->_ref_dossier_medical}}
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
			    {{include file=../../dPplanningOp/templates/inc_documents_operation.tpl operation=$selOp}}
			  </div>
		  </td>
	  </tr>
	</table>
</div>

{{mb_include_script module=dPcompteRendu script=aideSaisie}}
<div id="antecedents" style="display:none"></div>

{{if $isPrescriptionInstalled}}
  <!-- Affichage de la prescription -->
  <div id="prescription_sejour_tab" style="display:none">
    <div id="prescription_sejour"></div>
  </div>
  
  <!-- Affichage du dossier de soins avec les lignes "bloc" -->
  <div id="soins" style="display:none"></div>
{{/if}}

{{/if}}

{{if $isImedsInstalled}}
  <!-- Affichage de la prescription -->
  <div id="Imeds_tab" style="display:none"></div>
{{/if}}