<!-- $Id$ -->

<script type="text/javascript">

function popCode(type) {
  var oElement = null;
  
  if (type == "ccam") {
    oElement = document.editOp._codeCCAM;
  }
  
  if (type == "cim10") {
    oElement = document.editSejour.DP;
  }

  var url = new Url();
  url.setModuleAction("dPplanningOp", "code_selector");
  url.addElement(document.editOp.chir_id, "chir");
  url.addParam("type", type)
  url.popup(600, 500, type);
}

function setCode(sCode, type ) {
  if (!sCode) {
    return;
  }
  
  var oElement = null;
  
  if (type == "ccam") {
    oElement = document.editOp._codeCCAM;
  }
  
  if (type == "cim10") {
    oElement = document.editSejour.DP;
  }
  
  oElement.value = sCode;
  oElement.onblur();
}

function popProtocole() {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "vw_protocoles");
  url.addElement(document.editOp.chir_id, "chir_id");
  url.popup(700, 500, "Protocole");
}

function setProtocole(protocole) {

  var formOp = document.editOp;
  var formSejour = document.editSejour;
  
  formOp.chir_id.value           = protocole.chir_id;
  formOp.chir_id.onchange();
  formOp.codes_ccam.value        = protocole.codes_ccam;
  refreshListCCAM();
  formOp.libelle.value           = protocole.libelle;
  formOp._hour_op.value          = protocole._hour_op;
  formOp._min_op.value           = protocole._min_op;
  formOp.materiel.value          = protocole.materiel;
  formOp.examen.value            = protocole.examen;
  formOp.depassement.value       = protocole.depassement;
  formOp.rques.value             = protocole.rques_operation;
  formSejour._duree_prevue.value = protocole.duree_hospi;
  formSejour._duree_prevue.onchange();
  formSejour.convalescence.value = protocole.convalescence;
  formSejour.DP.value            = protocole.DP;
  formSejour.rques.value         = protocole.rques_sejour;
  setRadioValue(formSejour.type, protocole.type);
}

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
  url.popup(700, 500, url, "printPlanning");
  return;
}

function submitForms() {
  var oSejourForm = document.editSejour;
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
  	objName : '{{$op->_view}}',
  	ajax : true,
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
       <a class="buttonnew" href="index.php?m={{$m}}&amp;operation_id=0&amp;sejour_id=0">Programmer une nouvelle urgence</a>
    </td>
  </tr>
  {{else}}
  <tr>
    <td colspan="2">
       <a class="buttonnew" href="index.php?m={{$m}}&amp;operation_id=0&amp;sejour_id=0">Programmer une nouvelle intervention</a>
    </td>
  </tr>
  {{/if}}
  {{/if}}
  <tr>
    {{if $op->operation_id}}
    {{if $modurgence}}
    <th colspan="2" class="title" style="color: #f00; background-color: #00a">
      <button style="float:left;" class="search" type="button" onclick="popProtocole()">Choisir un protocole</button>
      <a style="float:right;" href="javascript:view_log('COperation',{{$op->operation_id}})">
        <img src="images/history.gif" alt="historique" />
      </a>
      Modification de l'urgence de {{$patient->_view}} par le Dr. {{$chir->_view}}
    </th>
    {{else}}
    <th colspan="2" class="title" style="color: #f00;">
      <button class="search" style="float:left;" type="button" onclick="popProtocole()">Choisir un protocole</button>
      <a style="float:right;" href="javascript:view_log('COperation',{{$op->operation_id}})">
        <img src="images/history.gif" alt="historique" />
      </a>
      Modification de l'intervention de {{$patient->_view}} par le Dr. {{$chir->_view}}
    </th>
    {{/if}}
    {{else}}
    {{if $modurgence}}
    <th colspan="2" class="title" style="color: #fff; background-color: #00a"> 
      <button class="search" style="float: left;" type="button" onclick="popProtocole()">Choisir un protocole</button>
      Création d'une urgence
    </th>
    {{else}}
    <th colspan="2" class="title"> 
      <button class="search" style="float: left;" type="button" onclick="popProtocole()">Choisir un protocole</button>
      Création d'une intervention
    </th>
    {{/if}}
    {{/if}}
  </tr>
  <tr>
    <td>
      {{include file="inc_form_operation.tpl"}}
    </td>
    <td id="inc_form_sejour">
      {{assign var="mode_operation" value=true}}
      {{include file="inc_form_sejour.tpl"}}
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="form">
        <tr>
          <td class="button">
          {{if $op->operation_id}}
            <button class="modify" type="button" onclick="submitForms();">Modifier</button>
            <button class="trash" type="button" onclick="deleteObjects();">Supprimer</button>
            <button class="cancel" type="button" onclick="cancelObjects();">Annuler</button>
          {{else}}
            <button class="submit" type="button" onclick="submitForms();">Créer</button>
          {{/if}}
          {{if $op->operation_id}}
            <button class="print" type="button" onclick="printForm();">Imprimer</button>
            <select name="_choix_modele" onchange="printDocument(this)">
              <option value="">&mdash; Choisir un modèle</option>
              <optgroup label="Modèles du praticien">
              {{foreach from=$listModelePrat item=curr_modele}}
                <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
              {{foreachelse}}
                <option value="">Aucun modèle disponible</option>
              {{/foreach}}
              </optgroup>
              <optgroup label="Modèles du cabinet">
              {{foreach from=$listModeleFunc item=curr_modele}}
                <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
              {{foreachelse}}
                <option value="">Aucun modèle disponible</option>
              {{/foreach}}
              </optgroup>
            </select>
            <select name="_choix_pack" onchange="printPack(this)">
              <option value="">&mdash; Choisir un pack</option>
              {{foreach from=$listPack item=curr_pack}}
                <option value="{{$curr_pack->pack_id}}">{{$curr_pack->nom}}</option>
              {{foreachelse}}
                <option value="">Aucun pack disponible</option>
              {{/foreach}}
            </select>
          {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

