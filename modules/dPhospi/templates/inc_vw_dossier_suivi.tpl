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
  page = page || 0;
  
  $$("div.list_trans").invoke("hide");
  $("list_"+page).show();
  
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
  if (!$V(oFormLineElementSuivi.prescription_id)){
    var oFormPrescription = getForm("addPrescriptionSuiviSoins");
    return onSubmitFormAjax(oFormPrescription);
  }
  
  return onSubmitFormAjax(oFormLineElementSuivi, { onComplete: function() {
    Control.Modal.close();
    loadSuivi('{{$sejour->_id}}');
  } } );
}

// Submit d'une ligne de commentaire
submitLineComment = function(){
  var oFormLineCommentSuivi = getForm('addLineCommentMedSuiviSoins');

  // Si la prescription de sejour n'existe pas
  if (!$V(oFormLineCommentSuivi.prescription_id)){
    var oFormPrescription = getForm("addPrescriptionSuiviSoins");
    return onSubmitFormAjax(oFormPrescription);
  }
  
  return onSubmitFormAjax(oFormLineCommentSuivi, { onComplete: function() {
    Control.Modal.close();
    loadSuivi('{{$sejour->_id}}');
  } } );
}

submitProtocoleSuiviSoins = function(){
  var oFormProtocoleSuiviSoins = getForm("applyProtocoleSuiviSoins");
  // Si la prescription de sejour n'existe pas
  if (!$V(oFormProtocoleSuiviSoins.prescription_id)){
    var oFormPrescription = getForm("addPrescriptionSuiviSoins");
    return onSubmitFormAjax(oFormPrescription);
  } 
  
  return onSubmitFormAjax(oFormProtocoleSuiviSoins, { onComplete: function() {
    Control.Modal.close();
    if (window.updateNbTrans) {
      updateNbTrans('{{$sejour->_id}}');
    }
    if (window.loadSuivi) {
      loadSuivi('{{$sejour->_id}}');
    }
  } } );
}

updatePrescriptionId = function(prescription_id){
  // Ligne d'element
  var oFormLineElementSuivi = getForm('addLineElementSuivi');
  $V(oFormLineElementSuivi.prescription_id, prescription_id);
  
  // Ligne de commentaire
  var oFormLineCommentSuivi = getForm('addLineCommentMedSuiviSoins');
  $V(oFormLineCommentSuivi.prescription_id, prescription_id);

  // Protocole
  var oFormProtocoleSuiviSoins = getForm("applyProtocoleSuiviSoins");
  $V(oFormProtocoleSuiviSoins.prescription_id, prescription_id);
  
  // Envoi du formulaire (suivant celui qui est rempli)
  if($V(oFormLineElementSuivi.element_prescription_id)){
    submitLineElement();
  }
  else if($V(oFormLineCommentSuivi.commentaire)){
    submitLineComment();
  }
  else {
    submitProtocoleSuiviSoins();
  }
}

addTransmissionAdm = function(line_id, line_class){
  var oFormTransmission = getForm("addTransmissionFrm");
  $V(oFormTransmission.object_id, line_id);
  $V(oFormTransmission.object_class, line_class);
  $V(oFormTransmission.text, "Réalisé");
  return onSubmitFormAjax(oFormTransmission, { onComplete: loadSuivi.curry('{{$sejour->_id}}')});
}

highlightTransmissions = function(cible_guid){
  $('transmissions').select("."+cible_guid+" .libelle_trans").invoke("addClassName", "highlight");
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
    // Plusieurs transmissions
    if (typeof(transmission_id) == "object") {
      $H(transmission_id).each(function(trans) {
        url.addParam(trans["0"], trans["1"]);
      });
    }
    else {
      url.addParam("transmission_id", transmission_id);
    }
  }
  if (object_id != undefined && object_class !=undefined) {
    url.addParam("object_id",    object_id);
    url.addParam("object_class", object_class);
  }
  if (libelle_ATC != undefined) {
    url.addParam("libelle_ATC", libelle_ATC);
  }
  url.requestModal(800, 400);
}

addObservation = function(sejour_id, user_id, observation_id) {
  var url = new Url("dPhospi", "ajax_observation");
  url.addParam("sejour_id", sejour_id);
  url.addParam("user_id", user_id);
  if (observation_id != undefined) {
    url.addParam("observation_id", observation_id);
  }
  url.requestModal(600, 400);
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

bindOperation = function(sejour_id) {
  var url = new Url("dPcabinet", "ajax_bind_operation");
  url.addParam("sejour_id", sejour_id);
  url.requestModal(500, null, {showReload: false, showClose: false});
}

validateAdministration = function(sejour_id) {
  var url = new Url("dPprescription", "ajax_administration_for_consult");
  url.addParam("sejour_id", sejour_id);
  url.requestModal(500, null, {showReload: false, showClose: false});
}

modalConsult = function(consult_id) {
  var url = new Url("dPcabinet", "ajax_short_consult");
  url.addParam("sejour_id", "{{$sejour->_id}}");
  url.addParam("consult_id", consult_id);
  url.modal(600, 400);
  url.modalObject.observe("afterClose", function() {
    if (window.loadSuivi) {
      loadSuivi("{{$sejour->_id}}");
    }
  });
}

createConsult = function() {
  {{if $isAnesth}}
    bindOperation('{{$sejour->_id}}');
  {{else}}
    onSubmitFormAjax(getForm('addConsultation'));
  {{/if}}
}

createConsultEntree = function() {
  var form = getForm('addConsultation');
  $V(form.type, 'entree');
  onSubmitFormAjax(getForm('addConsultation'));
}

toggleLockCible = function(transmission_id, lock) {
  var form = getForm("lockTransmission");
  $V(form.transmission_medicale_id, transmission_id);
  $V(form.locked, lock);
  onSubmitFormAjax(form, {onComplete: function() {
    loadSuivi('{{$sejour->_id}}');
  }});
}

showLockedTrans = function(transmission_id) {
  var url = new Url("hospi", "ajax_list_locked_trans");
  url.addParam("transmission_id", transmission_id);
  url.requestModal(850, 550, {maxHeight: '550'});
}

{{if $count_trans > 0}}
  Main.add(showListTransmissions.curry(0, {{$count_trans}}));
{{/if}}

App.readonly = false;

</script>

<form name="lockTransmission" method="post" action="?">
  <input type="hidden" name="m" value="hospi"/>
  <input type="hidden" name="dosql" value="do_transmission_aed"/>
  <input type="hidden" name="transmission_medicale_id" />
  <input type="hidden" name="locked" value="1" />

</form>

<form name="addTransmissionFrm" method="post" action="?">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="dosql" value="do_transmission_aed" />
  <input type="hidden" name="object_id" />
  <input type="hidden" name="object_class" />
  <input type="hidden" name="text" />
  <input type="hidden" name="user_id" value="{{$user->_id}}" />
  <input type="hidden" name="date" value="now" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
</form>

<form name="addConsultation" method="post" action="?">
  <input type="hidden" name="m" value="cabinet" />
  <input type="hidden" name="dosql" value="do_consult_now" />
  <input type="hidden" name="prat_id" value="{{$user->_id}}" />
  <input type="hidden" name="patient_id" value="{{$sejour->patient_id}}" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_operation_id" value="" />
  <input type="hidden" name="type" value="" />
  <input type="hidden" name="_in_suivi" value="1" />
  <input type="hidden" name="callback" value="modalConsult" />
</form>

<div style="display: none;" id="legend_suivi">
  {{mb_include module=hospi template=inc_legend_suivi}}
</div>

<button type="button" class="search" onclick="modal('legend_suivi')" style="float: right;">Légende</button>

{{if !$isPraticien}}
  <button class="add" onclick="addTransmission('{{$sejour->_id}}', '{{$user->_id}}', null, null, null, null, 1);">Ajouter une transmission</button>
{{else}}
  <button class="add" onclick="addObservation('{{$sejour->_id}}', '{{$user->_id}}');">Ajouter une observation</button>
  {{if $sejour->type == "urg" && "dPprescription CPrescription prescription_suivi_soins"|conf:"CGroups-$g" && "dPprescription"|module_active}}
    <button class="add" onclick="addPrescription('{{$sejour->_id}}', '{{$user->_id}}')">Ajouter une prescription</button>
  {{/if}}
  {{if @isset($modules.dPcabinet|smarty:nodefaults)}}
    <a class="button new" href="#1" id="newConsult"
      onclick="validateAdministration('{{$sejour->_id}}');">Nouvelle consultation</a>
    <button type="button" class="new" id="newConsultEntree" {{if $has_obs_entree}}disabled="disabled"{{/if}}
      onclick="createConsultEntree();">Nouvelle observation d'entrée</button>
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
      {{mb_include module=hospi template=inc_list_transmissions readonly=false list_transmissions=$mini_list}}
    </div>
  {{/if}}
{{foreachelse}}
  {{mb_include module=hospi template=inc_list_transmissions readonly=false list_transmissions=null}}
{{/foreach}}
