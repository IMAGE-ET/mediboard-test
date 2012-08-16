<!-- $Id$ -->

{{if "dPprescription"|module_active}}
  {{mb_script module="dPprescription" script="prescription_editor"}}
  {{mb_script module="dPprescription" script="prescription"}}
{{/if}}

{{mb_script module="dPplanningOp" script="protocole_selector"}}

{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}

<script type="text/javascript">
  
Main.add(function(){
  // Il faut sauvegarder le sejour_id pour la création de l'affectation
  // après la fermeture de la modale.
  {{if $op->_id && $dialog == 1}}
    window.parent.sejour_id_for_affectation = '{{$op->_ref_sejour->_id}}';
  {{/if}}
  
  new TogglePairEffect("modeEasy", "modeExpert", { 
    idFirstVisible: {{$app->user_prefs.mode_dhe+1}}
  });
});

function printDocument(iDocument_id) {
  var form = document.editOp;
  
  if (checkFormOperation() && (iDocument_id.value != 0)) {
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addElement(form.operation_id, "object_id");
    url.addElement(iDocument_id, "modele_id");
    url.popup(700, 600, "Document");
    return true;
  }
  
  return false;
}

function printPack(iPack_id) {
  var form = document.editOp;

  if (checkFormOperation() && (iPack_id.value != 0)) {
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addElement(form.operation_id, "object_id");
    url.addElement(iPack_id, "pack_id");
    url.popup(700, 600, "Document");
    return true;
  }
  
  return false;
}

function printForm() {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "view_planning"); 
  url.addElement(document.editOp.operation_id);
  url.popup(700, 500, "printPlanning");
  return;
}

function submitForms() {
  var oSejourForm = document.editSejour;
  if(!checkDureeHospi() || !checkForm(oSejourForm) || !checkFormOperation()) {
    return;
  }
  submitFormAjax(oSejourForm, 'systemMsg');
}

function submitFormOperation(iSejour_id) {
  if(iSejour_id) {
    var oForm = document.editOp;
    var oFormSejour = document.editSejour;
    oForm.sejour_id.value = iSejour_id;
    $V(oForm._protocole_prescription_chir_id, $V(oFormSejour._protocole_prescription_chir_id));
    if (oForm.onsubmit()) {
      oForm.submit();
    }
  }
}

function deleteSejour() {
  var oForm = document.editSejour;
  oForm.del.value = 1;
  oForm.submit();
}

function deleteObjects() {
  var oOptions = {
    objName : '{{$op->_view|smarty:nodefaults|escape:"javascript"}}',
    ajax : true
  }
  
  var oAjaxOptions = {
    onComplete : deleteSejour
  }

  confirmDeletion(document.editOp, oOptions, oAjaxOptions);
}
 
function cancelObjects() {
  cancelOperation();
//  cancelSejour();
}

ProtocoleSelector.init = function(){
  this.sForSejour     = false;
  this.sChir_id       = "chir_id";
  this.sCodes_ccam    = "codes_ccam";
  this.sCote          = "cote";
  this.sLibelle       = "libelle";
  this.sHour_op       = "_hour_op";
  this.sMin_op        = "_min_op";
  this.sMateriel      = "materiel";
  this.sExamen        = "examen";
  this.sDepassement   = "depassement";
  this.sForfait       = "forfait";
  this.sFournitures   = "fournitures";
  this.sRques_op      = "rques";
  this.sServiceId     = "service_id";
  this.sPresencePreop = "presence_preop";
  this.sPresencePostop = "presence_postop";
  this.sType          = "type";
  this.sUf_hebergement_id = "uf_hebergement_id";
  this.sUf_medicale_id = "uf_medicale_id";
  this.sUf_soins_id = "uf_soins_id";
  this.sTypesRessourcesIds = "_types_ressources_ids";
  {{if $conf.dPplanningOp.CSejour.show_type_pec == 1}}
    this.sTypePec     = "type_pec";
  {{/if}}
  this.sDuree_uscpo   = "duree_uscpo";
  this.sDuree_preop   = "duree_preop";
  this.sDuree_prevu   = "_duree_prevue";
  this.sConvalescence = "convalescence";
  this.sDP            = "DP";
  this.sRques_sej     = "rques";

  this.sChir_id_easy    = "chir_id";
  this.sServiceId_easy  = "service_id";
  this.sLibelle_easy    = "libelle";
  this.sCodes_ccam_easy = "codes_ccam";
  this.sLibelle_sejour  = "libelle";
  
  this.sProtoPrescAnesth = "_protocole_prescription_anesth_id";
  this.sProtoPrescChir   = "_protocole_prescription_chir_id";
  
  this.pop();
}

modeExpertDisplay = function() {
  if($("modeExpert").style.display == "none"){
    $("modeEasy").hide(); 
    $("modeExpert").show(); 
    $("modeExpert-trigger").show(); 
    $("modeEasy-trigger").hide();
  }
}
</script> 

{{mb_include module=planningOp template=js_form_operation}}
{{mb_include module=planningOp template=js_form_sejour}}

<div class="big-info text"  style="display: none; text-align: center;" id="sejour-value-chooser">
  Veuillez indiquer si vous souhaitez garder les valeurs du <strong>dossier existant</strong> ou bien utiliser celles que vous venez de saisir (<strong>nouveau dossier</strong>) :
  <br /><br />
  <form name="sejourChooserFrm" action="?m={{$m}}" method="get">
  <input name="majDP"     type="hidden" value="0" />
  <input name="majEntree" type="hidden" value="0" />
  <input name="majSortie" type="hidden" value="0" />
  <table class="form">
    <tr>
      <th class="title"></th>
      <th class="category" colspan="2">Dossier existant</th>
      <th class="category" colspan="2">Nouveau dossier</th>
    </tr>
    <tr id="chooseDiag">
      <th>Diagnostic</th>
      <td class="narrow"><input name="valueDiag" type="radio" value="" /></td>
      <td id="chooseNewDiag"></td>
      <td class="narrow"><input name="valueDiag" type="radio" checked="checked" value="" /></td>
      <td id="chooseOldDiag"></td>
    </tr>
    <tr id="chooseAdm">
      <th>Admission</th>
      <td class="narrow"><input name="valueAdm" type="radio" value="" /></td>
      <td id="chooseNewAdm"></td>
      <td class="narrow"><input name="valueAdm" type="radio" checked="checked" value="" /></td>
      <td id="chooseOldAdm"></td>
    </tr>
    <tr id="chooseSortie">
      <th>Sortie prévue</th>
      <td class="narrow"><input name="valueSortie" type="radio" value="" /></td>
      <td id="chooseNewSortie"></td>
      <td class="narrow"><input name="valueSortie" type="radio" checked="checked" value="" /></td>
      <td id="chooseOldSortie"></td>
    </tr>
    <tr>
      <td colspan="5" class="button">
        <button class="tick" type="button" onclick="applyNewSejour()">{{tr}}OK{{/tr}}</button>
      </td>
    </tr>
  </table>  
  </form>
</div>

<table class="main">
  {{if $op->operation_id}}
  <tr>
    <td colspan="2">
       <a class="button new" href="?m={{$m}}&amp;operation_id=0&amp;sejour_id=0">
         {{tr}}COperation.create{{if $modurgence}}_urgence{{/if}}{{/tr}}
       </a>
    </td>
  </tr>
  {{/if}}

  <tr>
    <!-- Création/Modification d'intervention/urgence -->
    <th colspan="2" class="title{{if $modurgence}} urgence{{/if}}{{if $op->_id}} modify{{/if}}">
      <button class="hslip" id="modeEasy-trigger" style="float: right; display:none;" type="button">
        {{tr}}button-COperation-modeEasy{{/tr}}
      </button>
      <button class="hslip" id="modeExpert-trigger" style="float: right; display:none;" type="button">
        {{tr}}button-COperation-modeExpert{{/tr}}
      </button>
      <button style="float:left;" class="search" type="button" onclick="ProtocoleSelector.init()">
        {{tr}}button-COperation-choixProtocole{{/tr}}
      </button>
      {{mb_ternary var=message test=$op->_id value=modify other=create}}
      {{tr}}COperation-title-{{$message}}{{if $modurgence}}-urgence{{/if}}{{/tr}} 
      <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient}}</span> 
      {{if $chir->_id}}
      - {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$chir}}
      {{/if}}
    </th>
  </tr>
  
  <!-- Mode easy -->
  <tr id="modeEasy" style="display:none;"> 
    <td style="width: 60%;">
    {{mb_include template=inc_form_operation_easy}}
    </td>
    <td class="text" style="width: 40%;">
      <div class="big-info">
       Ceci est le <strong>mode simplifié</strong> de planification d'intervention.<br/>
       Il est nécessaire de <strong>sélectionner un protocole</strong> pour créer une intervention.<br/>
       <em>Pour plus de paramètres vous pouvez passer en mode expert.</em>
      </div>
    </td>
  </tr>
  
  <!-- Mode expert -->
  <tr id="modeExpert" style="display:none;">
    <td>
      {{mb_include template=inc_form_operation}}
    </td>
    <td id="inc_form_sejour">
      {{mb_include template=inc_form_sejour mode_operation=true}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
    {{if $op->_id}}
    {{if !$op->_ref_sejour->sortie_reelle || $modules.dPbloc->_can->edit || $modules.dPhospi->_can->edit}}
      <button class="submit" type="button" onclick="submitForms();">{{tr}}Save{{/tr}}</button>
      {{if $op->annulee}}
      <button class="change" type="button" onclick="cancelObjects();">{{tr}}Restore{{/tr}}</button>
      {{else}}
        {{if !$conf.dPplanningOp.COperation.cancel_only_for_resp_bloc || $modules.dPbloc->_can->edit || (!$op->_ref_sejour->entree_reelle && !$op->rank)}}
          <button class="cancel" type="button" onclick="cancelObjects();">{{tr}}Cancel{{/tr}}</button>
        {{/if}}
      {{/if}}
      {{if !$conf.dPplanningOp.COperation.delete_only_admin || $can->admin}}
      <button class="trash" type="button" onclick="deleteObjects();">{{tr}}Delete{{/tr}}</button>
      {{/if}}
      
      <button class="print" type="button" onclick="printForm();">{{tr}}Print{{/tr}}</button>
    {{else}}
      <div class="big-info">
        Les informations sur le séjour et sur l'intervention ne peuvent plus être modifiées car <strong>le patient est déjà sorti de l'établissement</strong>.
        Veuillez contacter le <strong>responsable du service d'hospitalisation</strong> pour annuler la sortie ou
        <strong>un administrateur</strong> si vous devez tout de même modifier certaines informations.
      </div>
    {{/if}}
    {{else}}
      <button class="submit" type="button" onclick="submitForms();">{{tr}}Create{{/tr}}</button>
    {{/if}}
    </td>
  </tr>
</table>

<!-- Documents -->
{{if $op->_id}}
  <hr />
  {{mb_include template=inc_documents_operation operation=$op}}
{{/if}}

<!-- Files -->
{{if $op->_id}}
  <hr />
  {{mb_include template=inc_files_operation operation=$op}}
{{/if}}
    
<!-- Actes -->
{{if $op->_ref_actes|@count}}
<hr />
<table class="tbl">
  {{mb_include module=cabinet template=inc_list_actes_ccam subject=$op vue=complete}}
</table>
{{/if}}

<!-- la modale qui s'affiche dans le cas où la date de l'intervention est en dehors de celle du séjour -->
<div id="date_alert" style="display:none">
  <div style="text-align:center">  L'intervention est en dehors du séjour, voulez-vous passer au mode expert pour modifier les dates du séjour?
  </div>
  <div style="text-align:center">
    <button  class="tick" onclick="modalWindow.close();modeExpertDisplay();"> {{tr}}Yes{{/tr}}</button>
    <button  class="cancel" onclick="modalWindow.close();"> {{tr}}No{{/tr}}</button>
  </div>
</div>