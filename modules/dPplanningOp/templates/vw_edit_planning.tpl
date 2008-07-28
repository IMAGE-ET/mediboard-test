<!-- $Id$ -->

{{mb_include_script module="dPplanningOp" script="protocole_selector"}}
{{mb_include_script module="dPprescription" script="prescription_editor"}}
{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="dPcompteRendu" script="document"}}
{{mb_include_script module="dPcompteRendu" script="modele_selector"}}

<script type="text/javascript">

function printDocument(iDocument_id) {
  form = document.editOp;
  
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
  form = document.editOp;

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
  if(!checkDureeHospi()) {
    return;
  }
  if(!checkForm(oSejourForm)) {
    return;
  }
  if(!checkFormOperation()) {
    return;
  }
  submitFormAjax(oSejourForm, 'systemMsg');
}

function submitFormOperation(iSejour_id) {
  if(iSejour_id) {
    var oForm = document.editOp;
    oForm.sejour_id.value = iSejour_id;
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

function pageMain() {
  incFormOperationMain();
  incFormSejourMain();
}

</script>

{{include file="js_form_operation.tpl"}}
{{include file="js_form_sejour.tpl"}}

<table class="main" style="margin: 4px; border-spacing: 0px;">
  {{if $op->operation_id}}
  {{if $modurgence}}
  <tr>
    <td colspan="2">
       <a class="buttonnew" href="?m={{$m}}&amp;operation_id=0&amp;sejour_id=0">
         {{tr}}COperation.create_urgence{{/tr}}
       </a>
    </td>
  </tr>
  {{else}}
  <tr>
    <td colspan="2">
       <a class="buttonnew" href="?m={{$m}}&amp;operation_id=0&amp;sejour_id=0">
         {{tr}}COperation.create{{/tr}}
       </a>
    </td>
  </tr>
  {{/if}}
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
      {{tr}}msg-COperation-title-{{$message}}{{if $modurgence}}-urgence{{/if}}{{/tr}} 
      {{$patient->_view}} 
      {{if $chir->_id}}
      par le Dr {{$chir->_view}}
      {{/if}}
    </th>
  </tr>
  
  <!-- Mode easy -->
  
  
  <tbody id="modeEasy" style="display:none;">
    <tr> 
      <td>
      {{include file="inc_form_operation_easy.tpl"}}
      </td>
      
      <td class="text">
        <div class="big-info">
         Ceci est le <strong>mode simplifié</strong> de planification d'intervention.
         <br/>
         Il est nécessaire de <strong>sélectionner un protocole</strong> pour créer une intervention.
         <br/>
         <em>Pour plus de paramètres vous pouvez passer en mode expert.</em>
        </div>
      </td>
    </tr>
  </tbody>
  
  <!-- Mode expert -->
  <tbody id="modeExpert" style="display:none;">
  <tr>
    <td>
      {{include file="inc_form_operation.tpl"}}
    </td>
    
    <td>
      <div id="inc_form_sejour"> 
        {{include file="inc_form_sejour.tpl" mode_operation=true}}
      </div>
    </td>
  </tr>
  </tbody>

  <tr class="script">
    <td>
      <script type="text/javascript">
      new TogglePairEffect("modeEasy", "modeExpert", { 
        idFirstVisible: {{$app->user_prefs.mode_dhe+1}}
      } );

      ProtocoleSelector.init = function(){
  
        this.sChir_id       = "chir_id";
        this.sCodes_ccam    = "codes_ccam";
        this.sLibelle       = "libelle";
        this.sHour_op       = "_hour_op";
        this.sMin_op        = "_min_op";
        this.sMateriel      = "materiel";
        this.sExamen        = "examen";
        this.sDepassement   = "depassement";
        this.sForfait       = "forfait";
        this.sFournitures   = "fournitures";
        this.sRques_op      = "rques";
        
        this.sType          = "type";
        this.sDuree_prevu   = "_duree_prevue";
        this.sConvalescence = "convalescence";
        this.sDP            = "DP";
        this.sRques_sej     = "rques";

        this.sChir_id_easy    = "chir_id";
        this.sLibelle_easy    = "libelle";
        this.sCodes_ccam_easy = "codes_ccam";
        
        this.pop();
      }
      </script> 
    </td>
  </tr>
    
  <tr>
    <td colspan="2">
      <table class="form">
        <tr>
          <td class="button">
          {{if $op->_id}}
            <button class="modify" type="button" onclick="submitForms();">{{tr}}Modify{{/tr}}</button>
            <button class="trash" type="button" onclick="deleteObjects();">{{tr}}Delete{{/tr}}</button>
            {{if $op->annulee}}
            <button class="change" type="button" onclick="cancelObjects();">{{tr}}Restore{{/tr}}</button>
            {{else}}
            <button class="cancel" type="button" onclick="cancelObjects();">{{tr}}Cancel{{/tr}}</button>
            {{/if}}
          {{else}}
            <button class="submit" type="button" onclick="submitForms();">{{tr}}Create{{/tr}}</button>
          {{/if}}
            <button class="print" type="button" onclick="printForm();">{{tr}}Print{{/tr}}</button>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

<!-- Documents -->
{{if $op->_id}}
<hr />
<div id="documents" />
<script type="text/javascript">
  Document.register('{{$op->_id}}','{{$op->_class_name}}','{{$op->chir_id}}','documents');
</script>
{{/if}}
    

<!-- Actes -->
{{if $op->_ref_actes|@count}}
<hr />
{{include file="../../dPsalleOp/templates/inc_vw_actes.tpl" subject=$op}}
{{/if}}

