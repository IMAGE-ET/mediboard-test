<!-- $Id$ -->

{{mb_include_script module="dPplanningOp" script="protocole_selector"}}

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
       <a class="buttonnew" href="index.php?m={{$m}}&amp;operation_id=0&amp;sejour_id=0">
         {{tr}}COperation.create_urgence{{/tr}}
       </a>
    </td>
  </tr>
  {{else}}
  <tr>
    <td colspan="2">
       <a class="buttonnew" href="index.php?m={{$m}}&amp;operation_id=0&amp;sejour_id=0">
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
      par le Dr. {{$chir->_view}}
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
      {{if !$op->operation_id}}
        <div class="big-info">
         Ceci est le <strong>mode simplifié</strong> de planification d'intervention.
         <br/>
         Il est nécessaire de <strong>sélectionner un protocole</strong> pour créer une intervention.
         <br/>
         <em>Pour plus de paramètres vous pouvez passer en mode expert.</em>
        </div>
      {{/if}}
      </td>
    </tr>
  </tbody>
  
  <!-- Mode expert -->
  <tbody id="modeExpert" style="display:none;">
  <tr>
    <td>
      {{include file="inc_form_operation.tpl"}}
    </td>
    
    <td id="inc_form_sejour">
      {{assign var="mode_operation" value=true}}
      {{include file="inc_form_sejour.tpl"}}
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
          {{if $op->operation_id}}
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
          {{if $op->operation_id}}
            <button class="print" type="button" onclick="printForm();">{{tr}}Print{{/tr}}</button>
            <select name="_choix_modele" onchange="printDocument(this)">
              <option value="">&mdash; {{tr}}modele-choice{{/tr}}</option>
              <optgroup label="Modèles du praticien">
              {{foreach from=$listModelePrat item=curr_modele}}
                <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
              {{foreachelse}}
                <option value="">{{tr}}modele-none{{/tr}}</option>
              {{/foreach}}
              </optgroup>
              <optgroup label="Modèles du cabinet">
              {{foreach from=$listModeleFunc item=curr_modele}}
                <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
              {{foreachelse}}
                <option value="">{{tr}}modele-none{{/tr}}</option>
              {{/foreach}}
              </optgroup>
            </select>
            <select name="_choix_pack" onchange="printPack(this)">
              <option value="">&mdash; {{tr}}pack-choice{{/tr}}</option>
              {{foreach from=$listPack item=curr_pack}}
                <option value="{{$curr_pack->pack_id}}">{{$curr_pack->nom}}</option>
              {{foreachelse}}
                <option value="">{{tr}}pack-none{{/tr}}</option>
              {{/foreach}}
            </select>
          {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

