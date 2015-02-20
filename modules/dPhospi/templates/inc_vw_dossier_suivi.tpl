<script>
  delCibleTransmission = function() {
    var oDiv = $('cibleTrans');
    if(!oDiv) return;
    var oForm = getForm('editTrans');
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

    if (total) {
      url.addParam("total",total);
    }
    url.addParam("step", '{{$page_step}}');
    url.addParam("page", page);
    url.addParam("change_page", "showListTransmissions");
    url.requestUpdate("pagination");
  }

  // Submit d'une ligne d'element
  submitLineElement = function() {
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

    return onSubmitFormAjax(oFormLineElementSuivi, function() {
      Control.Modal.close();
      loadSuivi('{{$sejour->_id}}');
    } );
  }

  // Submit d'une ligne de commentaire
  submitLineComment = function(){
    var oFormLineCommentSuivi = getForm('addLineCommentMedSuiviSoins');

    // Si la prescription de sejour n'existe pas
    if (!$V(oFormLineCommentSuivi.prescription_id)){
      var oFormPrescription = getForm("addPrescriptionSuiviSoins");
      return onSubmitFormAjax(oFormPrescription);
    }

    return onSubmitFormAjax(oFormLineCommentSuivi, function() {
      Control.Modal.close();
      loadSuivi('{{$sejour->_id}}');
    } );
  }

  submitProtocoleSuiviSoins = function() {
    var oFormProtocoleSuiviSoins = getForm("applyProtocoleSuiviSoins");
    // Si la prescription de sejour n'existe pas
    if (!$V(oFormProtocoleSuiviSoins.prescription_id)){
      var oFormPrescription = getForm("addPrescriptionSuiviSoins");
      return onSubmitFormAjax(oFormPrescription);
    }

    return onSubmitFormAjax(oFormProtocoleSuiviSoins, function() {
      Control.Modal.close();
      if (window.updateNbTrans) {
        updateNbTrans('{{$sejour->_id}}');
      }
      if (window.loadSuivi) {
        loadSuivi('{{$sejour->_id}}');
      }
    } );
  }

  updatePrescriptionId = function(prescription_id) {
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

  addTransmissionAdm = function(line_id, line_class) {
    var oFormTransmission = getForm("addTransmissionSuiviFrm");
    $V(oFormTransmission.object_id, line_id);
    $V(oFormTransmission.object_class, line_class);
    $V(oFormTransmission.text, "Réalisé");
    return onSubmitFormAjax(oFormTransmission, loadSuivi.curry('{{$sejour->_id}}'));
  }

  highlightTransmissions = function(cible_guid) {
    $('transmissions').select("."+cible_guid+" .libelle_trans").invoke("addClassName", "highlight");
  }

  removeHighlightTransmissions = function() {
   $('transmissions').select('.highlight').invoke("removeClassName", "highlight");
  }

  addTransmission = function(sejour_id, user_id, transmission_id, object_id, object_class, libelle_ATC, refreshTrans) {
    var url = new Url("hospi", "ajax_transmission");
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
    var url = new Url("hospi", "ajax_observation");
    url.addParam("sejour_id", sejour_id);
    url.addParam("user_id", user_id);
    if (observation_id != undefined) {
      url.addParam("observation_id", observation_id);
    }
    url.requestModal(600, 400);
  }

  addPrescription = function(sejour_id, user_id, object_id, object_class) {
    var url = new Url("hospi", "ajax_prescription_lite");
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
    var url = new Url("cabinet", "ajax_bind_operation");
    url.addParam("sejour_id", sejour_id);
    url.requestModal(500, null, {showReload: false, showClose: false});
  }

  validateAdministration = function(sejour_id) {
    var url = new Url("prescription", "ajax_administration_for_consult");
    url.addParam("sejour_id", sejour_id);
    url.requestModal(500, null, {showReload: false, showClose: false});
  }

  {{if $count_trans > 0}}
    Main.add(showListTransmissions.curry(0, {{$count_trans}}));
  {{/if}}
</script>

<div style="display: none;" id="legend_suivi">
  {{mb_include module=hospi template=inc_legend_suivi}}
</div>

{{if $show_header}}
  {{assign var=patient value=$sejour->_ref_patient}}
  <table class="tbl">
    <tr>
      <th class="title" colspan="2">
        <a style="float: left" href="?m=patients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}">
          {{mb_include module=patients template=inc_vw_photo_identite size=42}}
        </a>

        <h2 style="color: #fff; font-weight: bold;">
          <span style="font-size: 0.7em;" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
            {{$patient->_view}}
          </span>
          {{if isset($sejour|smarty:nodefaults)}}
            <span style="font-size: 0.7em;" onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')"> - {{$sejour->_shortview|replace:"Du":"Séjour du"}}</span>
          {{/if}}
        </h2>
      </th>
    </tr>
  </table>
{{/if}}

<form name="addTransmissionSuiviFrm" method="post" action="?">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="dosql" value="do_transmission_aed" />
  <input type="hidden" name="object_id" />
  <input type="hidden" name="object_class" />
  <input type="hidden" name="text" />
  <input type="hidden" name="type" value="data" />
  <input type="hidden" name="user_id" value="{{$app->_ref_user->_id}}" />
  <input type="hidden" name="date" value="now" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
</form>

<button type="button" class="search" onclick="Modal.open('legend_suivi')" style="float: right;">Légende</button>

{{if !$isPraticien}}
  <button class="add" onclick="addTransmission('{{$sejour->_id}}', '{{$user->_id}}', null, null, null, null, 1);" style="display: inline !important;">Ajouter une transmission</button>
{{/if}}
{{if $isPraticien || (($app->_ref_user->isInfirmiere() || $app->_ref_user->isAideSoignant()) && "soins suivi obs_infirmiere"|conf:"CGroups-$g")}}
  <button class="add" onclick="addObservation('{{$sejour->_id}}', '{{$user->_id}}');" style="display: inline !important;">Ajouter une observation</button>
{{/if}}
{{if $isPraticien}}
  {{if $sejour->type == "urg" && "dPprescription CPrescription prescription_suivi_soins"|conf:"CGroups-$g" && "dPprescription"|module_active}}
    <button class="add" onclick="addPrescription('{{$sejour->_id}}', '{{$user->_id}}')" style="display: inline !important;">Ajouter une prescription</button>
  {{/if}}
  {{if @isset($modules.dPcabinet|smarty:nodefaults)}}
    <button type="button" class="new" id="newConsult" style="display: inline !important;"
      onclick="validateAdministration('{{$sejour->_id}}');">Nouvelle consultation</button>
    <button type="button" class="new oneclick" id="newConsultEntree" {{if $has_obs_entree}}disabled{{/if}}
      onclick="createConsultEntree();" style="display: inline !important;">Nouvelle observation d'entrée</button>
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
