{{if $op}}
{{assign var="chir_id" value=$selOp->_ref_chir->_id}}
{{/if}}
{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="module" value="dPsalleOp"}}
{{assign var="object" value=$selOp}}
{{mb_include module=dPsalleOp template=js_codage_ccam}}

<script type="text/javascript">

function printFicheAnesth(consult_id) {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_fiche"); 
  url.addParam("consultation_id", consult_id);
  url.popup(700, 500, "printFiche");
  return;
}

function submitTiming(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
  	onComplete : function() { 
  		reloadTiming(oForm.operation_id.value) 
  	} 
  });
  oAnesthForm = document.forms['anesthTiming'];
  submitAnesth(oAnesthForm);
}

function reloadTiming(operation_id){
  var url = new Url();
  {{if $object->_id}}
  url.setModuleAction("dPsalleOp", "httpreq_vw_timing");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("timing", { waitingText: null, onComplete: function() { ActesCCAM.refreshList({{$object->_id}},{{$object->_praticien_id}});} } );
  {{/if}}
}

function submitAnesth(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
  	onComplete: function() { 
  		reloadAnesth(oForm.operation_id.value) 
  	}
  });
}

function signVisiteAnesth(anesth_id) {
  alert('anesth numéro ' + anesth_id);
}

function reloadAnesth(operation_id){
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_vw_anesth");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("anesth", { 
  	waitingText: null,
  	onComplete: function() { 
  		ActesCCAM.refreshList(operation_id,"{{$selOp->chir_id}}"); 
  	}
  } );	
}

function reloadDiagnostic(sejour_id, modeDAS) {
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_diagnostic_principal");
  url.addParam("sejour_id", sejour_id);
  url.addParam("modeDAS", modeDAS);
  url.requestUpdate("cim", { waitingText : null } );
}

function reloadPersonnel(operation_id){
  var url = new Url;
  url.setModuleAction("dPsalleOp", "httpreq_vw_personnel");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("listPersonnel", {waitingText: null} );
}

function confirmeCloture() {
  return confirm("Action irréversible. Seul le service PSMI pourra modifier le codage de vos actes. Confirmez-vous la cloture de votre cotation pour aujourd'hui ?");
}

Main.add(function () {
  var url = new Url;
  {{if $dPconfig.dPsalleOp.COperation.mode}}
  url.setModuleAction("dPsalleOp", "httpreq_liste_op_prat");
  {{else}}
  url.setModuleAction("dPsalleOp", "httpreq_liste_plages");
  {{/if}}
  url.addParam("date", "{{$date}}");
  url.addParam("operation_id", "{{$selOp->_id}}");
  url.addParam("hide_finished", "{{$hide_finished}}");
  url.periodicalUpdate('listplages', { frequency: 90 });
  
  {{if $selOp->_id}}
  // Initialisation des onglets
	if ($('main_tab_group')){
    Control.Tabs.create('main_tab_group', true);
	}
	if ($('codage_tab_group')){
    Control.Tabs.create('codage_tab_group', true);
	}
  // Effet sur le programme
	if ($('listplages') && $('listplages-trigger')){
    new PairEffect("listplages", { sEffect : "appear", bStartVisible : true });
	}
  {{/if}}
});

</script>

<table class="main">
  <tr>
    <td style="width: 220px;" id="listplages"></td>
    <td>
    {{if $selOp->_id}}
		  {{if $dPconfig.dPsalleOp.CDailyCheckList.active != '1' || $check_list->_id && $check_list->validator_id}}
        {{include file=inc_operation.tpl}}
			{{else}}
        {{include file=inc_edit_check_list.tpl}}
      {{/if}}
    {{else}}
      <div class="big-info">
      	Veuillez sélectionner une intervention dans la liste pour pouvoir :
      	<ul>
      	  <li>coder les actes</li>
      	  <li>coder les diagnostics</li>
      	  <li>effectuer l'horodatage</li>
      	  <li>sélectionner le personnel en salle</li>
      	  <li>consulter le dossier</li>
      	</ul>
      </div>
    {{/if}}
    </td>
  </tr>
</table>