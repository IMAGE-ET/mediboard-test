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
    <!-- Cr�ation/Modification d'intervention/urgence -->
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
      {{$patient->_view}} par le Dr. {{$chir->_view}}
    </th>
  </tr>
  
  <!-- Mode easy -->
  <tbody id="modeEasy" style="display:none;">
    <tr>
    <td class="text">
    {{if !$op->operation_id}}
      Veuillez selectionner un protocole pour cr�er une intervention
    {{/if}}
    </td>
    </tr>
    <tr>
      <td>
      {{include file="inc_form_operation_easy.tpl"}}
      </td>
    </tr>
  </tbody>
  
  <!-- Mode expert -->
  <tbody id="modeExpert" style="display:none;">
  <tr>
    <td>
      {{include file="inc_form_operation.tpl"}}
      <script type="text/javascript">
      new TogglePairEffect("modeEasy", "modeExpert", { 
        idFirstVisible: {{$app->user_prefs.mode+1}}
      } );

      ProtocoleSelector.init = function(){
        
        var formOp        = document.editOp;
        var formOpEasy    = document.editOpEasy;
        var formSejour    = document.editSejour;
  
        this.eChir_id       = formOp.chir_id;
        this.eCodes_ccam    = formOp.codes_ccam;
        this.eLibelle       = formOp.libelle;
        this.eHour_op       = formOp._hour_op;
        this.eMin_op        = formOp._min_op;
        this.eMateriel      = formOp.materiel;
        this.eExamen        = formOp.examen;
        this.eDepassement   = formOp.depassement;
        this.eForfait       = formOp.forfait;
        this.eFournitures    = formOp.fournitures;
        this.eRques_op         = formOp.rques;
        
        this.eType          = formSejour.type;
        this.eDuree_prevu   = formSejour._duree_prevue;
        this.eConvalescence = formSejour.convalescence;
        this.eDP            = formSejour.DP;
        this.eRques_sej     = formSejour.rques;

        this.eChir_id_easy = formOpEasy.chir_id;
        
        this.pop();
      }
      </script> 
    </td>
    
    <td id="inc_form_sejour">
      {{assign var="mode_operation" value=true}}
      {{include file="inc_form_sejour.tpl"}}
    </td>
  </tr>
  </tbody>
  
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
              <optgroup label="Mod�les du praticien">
              {{foreach from=$listModelePrat item=curr_modele}}
                <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
              {{foreachelse}}
                <option value="">{{tr}}modele-none{{/tr}}</option>
              {{/foreach}}
              </optgroup>
              <optgroup label="Mod�les du cabinet">
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

