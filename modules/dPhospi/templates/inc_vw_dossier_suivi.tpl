<script type="text/javascript">

delCibleTransmission = function() {
  var oDiv = $('cibleTrans');
  if(!oDiv) return;
  var oForm = document.forms['editTrans'];
  $V(oForm.object_class, '');
  $V(oForm.object_id, '');
  $V(oForm.libelle_ATC, '');
  oDiv.innerHTML = "";
}

showListTransmissions = function(page, total) {
  $$("div.list_trans").invoke("hide");
  $("list_"+page).show();
  if (!page){
    page = 0;
  }
  var url = new Url("system", "ajax_pagination");
  
  if (total){
    url.addParam("total",total);
  }
  url.addParam("step",'{{$page_step}}');
  url.addParam("page",page);
  url.addParam("change_page","showListTransmissions");
  url.requestUpdate("pagination");
}

// Submit d'une ligne d'element
submitLineElement = function(){
  // Formulaire de creation de ligne
  var oFormLineElementSuivi = getForm('addLineElementSuivi');

  // Formulaire autocomplete
  var oFormLineSuivi = getForm('addLineSuivi');
  $V(oFormLineElementSuivi.commentaire, $V(oFormLineSuivi.commentaire));
	
	// Si la prescription de sejour n'existe pas
	if(!$V(oFormLineElementSuivi.prescription_id)){
	  var oFormPrescription = getForm("addPrescriptionSuiviSoins");
		return onSubmitFormAjax(oFormPrescription);
	} else {
    return onSubmitFormAjax(oFormLineElementSuivi, { onComplete: function() {
      Control.Modal.close();
      loadSuivi('{{$sejour->_id}}');
    }
    });
	}
}

// Submit d'une ligne de commentaire
submitLineComment = function(){
  var oFormLineCommentSuivi = getForm('addLineCommentMedSuiviSoins');

  // Si la prescription de sejour n'existe pas
  if(!$V(oFormLineCommentSuivi.prescription_id)){
    var oFormPrescription = getForm("addPrescriptionSuiviSoins");
    return onSubmitFormAjax(oFormPrescription);
  } else {
    return onSubmitFormAjax(oFormLineCommentSuivi, { onComplete: function() {
      Control.Modal.close();
      loadSuivi('{{$sejour->_id}}');
    }
    });
  }
}

submitProtocoleSuiviSoins = function(){
  var oFormProtocoleSuiviSoins = getForm("applyProtocoleSuiviSoins");
	// Si la prescription de sejour n'existe pas
  if(!$V(oFormProtocoleSuiviSoins.prescription_id)){
    var oFormPrescription = getForm("addPrescriptionSuiviSoins");
    return onSubmitFormAjax(oFormPrescription);
  } else {
    return onSubmitFormAjax(oFormProtocoleSuiviSoins, { onComplete: function() {
      Control.Modal.close();
      if (window.updateNbTrans) {
        updateNbTrans('{{$sejour->_id}}');
      }
      if (window.loadSuivi) {
        loadSuivi('{{$sejour->_id}}');
      }
    } });
  }
}

updatePrescriptionId = function(prescription_id){
  // Ligne d'element
	var oFormLineElementSuivi = getForm('addLineElementSuivi');
  $V(oFormLineElementSuivi.prescription_id, prescription_id);
	
	// Ligne de commentaire
	var oFormLineCommentSuivi = getForm('addLineCommentMedSuiviSoins');
	$V(oFormLineCommentSuivi.prescription_id, prescription_id);
	
	var oFormProtocoleSuiviSoins = getForm("applyProtocoleSuiviSoins");
	$V(oFormProtocoleSuiviSoins.prescription_id, prescription_id);
  
	// Selection du formulaire a envoyer (suivant celui qui est rempli)
	if($V(oFormLineElementSuivi.element_prescription_id)){
	  var oForm = oFormLineElementSuivi;
	}
	if($V(oFormLineCommentSuivi.commentaire)){
    var oForm = oFormLineCommentSuivi;
  }
  if($V(oFormProtocoleSuiviSoins.pack_protocole_id)){
    var oForm = oFormProtocoleSuiviSoins;
  }
  
  return onSubmitFormAjax(oForm, { onComplete: function() {
    Control.Modal.close();
    if (window.updateNbTrans) {
      updateNbTrans('{{$sejour->_id}}');
    }
  } });
}

addTransmissionAdm = function(line_id, line_class){
  var oFormTransmission = getForm("addTransmissionFrm");
	$V(oFormTransmission.object_id, line_id);
	$V(oFormTransmission.object_class, line_class);
  $V(oFormTransmission.text, "Réalisé");
	return onSubmitFormAjax(oFormTransmission, { onComplete: loadSuivi.curry('{{$sejour->_id}}')});
}

highlightTransmissions = function(cible_guid){
  $('transmissions').select("."+cible_guid).each(function(e){
    e.down('.libelle_trans').addClassName('highlight');
  });
}

removeHighlightTransmissions = function(){
 $('transmissions').select('.highlight').invoke("removeClassName", "highlight");
}

addTransmission = function(sejour_id, user_id, transmission_id, object_id, object_class, libelle_ATC, refreshTrans) {
  var url = new Url("dPhospi", "ajax_transmission");
  url.addParam("sejour_id", sejour_id);
  url.addParam("user_id", user_id);
  url.addParam("refreshTrans", refreshTrans);
  
  if (transmission_id != undefined) {
    url.addParam("transmission_id", transmission_id);
  }
  if (object_id != undefined && object_class !=undefined) {
    url.addParam("object_id",    object_id);
    url.addParam("object_class", object_class);
  }
  if (libelle_ATC != undefined) {
    url.addParam("libelle_ATC", libelle_ATC);
  }
  url.requestModal(600, 400);
}

addObservation = function(sejour_id, user_id, observation_id) {
  var url = new Url("dPhospi", "ajax_observation");
  url.addParam("sejour_id", sejour_id);
  url.addParam("user_id", user_id);
  if (observation_id != undefined) {
    url.addParam("observation_id", observation_id);
  }
  url.requestModal(600);
}

addPrescription = function(sejour_id, user_id, object_id, object_class) {
  var url = new Url("dPhospi", "ajax_prescription_lite");
  url.addParam("sejour_id", sejour_id);
  url.addParam("user_id", user_id);
  if (object_id && object_class) {
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.requestModal(300);
  }
  else {
    url.requestModal(800, 180);
  }
}

Main.add(function () {
	if({{$count_trans}} > 0) {
	  showListTransmissions(0, {{$count_trans}});
	}
});

</script>

<form name="addTransmissionFrm" method="post" action="?">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_transmission_aed" />
  <input type="hidden" name="object_id" />
  <input type="hidden" name="object_class" />
  <input type="hidden" name="text" />
  <input type="hidden" name="user_id" value="{{$user->_id}}" />
  <input type="hidden" name="date" value="now" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
</form>

{{if !$isPraticien}}
  <button class="add" onclick="addTransmission('{{$sejour->_id}}', '{{$user->_id}}', null, null, null, null, 1);">Ajouter une transmission</button>
{{else}}
  <button class="add" onclick="addObservation('{{$sejour->_id}}', '{{$user->_id}}');">Ajouter une observation</button>
  {{if $sejour->type == "urg" && $conf.dPprescription.CPrescription.prescription_suivi_soins}}
    <button class="add" onclick="addPrescription('{{$sejour->_id}}', '{{$user->_id}}')">Ajouter une prescription</button>
  {{/if}}
{{/if}}

<div id="pagination"></div>
{{assign var=start value=0}}
{{assign var=end value=$page_step}}
{{foreach from=$sejour->_ref_suivi_medical name=steps item=_item}}
  {{if $smarty.foreach.steps.index % $page_step == 0}}
    {{assign var=id value=$smarty.foreach.steps.index}}
    <div class="list_trans" id="list_{{$id}}" style="display:none">
	    {{assign var=start value=$smarty.foreach.steps.index}}
	    {{if $start+$end > $count_trans}}
	       {{assign var=end value=$count_trans-$start}}
	    {{/if}}
	    {{assign var=mini_list value=$sejour->_ref_suivi_medical|@array_slice:$start:$end}}
	    {{include file="../../dPhospi/templates/inc_list_transmissions.tpl" readonly=false list_transmissions=$mini_list}}
    </div>
  {{/if}}
{{foreachelse}}
  {{include file="../../dPhospi/templates/inc_list_transmissions.tpl" readonly=false list_transmissions=null}}
{{/foreach}}