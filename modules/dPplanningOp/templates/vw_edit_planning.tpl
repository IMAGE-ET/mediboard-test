<!-- $Id$ -->

<script type="text/javascript">

function popCode(type) {
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
  
  var oForm = null
  var oField = null;
  
  if (type == "ccam") {
    oForm = document.editOp;
    oField = oForm._codeCCAM;
  }
  
  if (type == "cim10") {
    oForm = document.editSejour;
    oField = oForm.CIM10_code;
  }
  
  oField.value = sCode;
}

function popProtocole() {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "vw_protocoles");
  url.addElement(document.editFrm.chir_id);
  url.popup(700, 500, "Protocole");
}

function setProtocole(protocole) {

  var form = document.editFrm;
  
  form.chir_id.value       = protocole.chir_id;
  form._chir_name.value    = protocole._chir_view;
  form.codes_ccam.value    = protocole.codes_ccam;
  refreshListCCAM();
  form.libelle.value       = protocole.libelle;
  form._hour_op.value      = protocole._hour_op;
  form._min_op.value       = protocole.min_op;
  form.materiel.value      = protocole.materiel;
  form.convalescence.value = protocole.convalescence;
  form.examen.value        = protocole.examen;
  form.depassement.value   = protocole.depassement;
  setRadioValue(form.type_adm, protocole.type);
  form.duree_hospi.value   = protocole.duree_hospi;
  form.rques.value         = protocole.rques_sejour;
}

function printDocument() {
  form = document.editFrm;
  
  if (checkFormOperation() && (form._choix_modele.value != 0)) {
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addElement(form.operation_id, "object_id");
    url.addElement(form._choix_modele, "modele_id");
    url.popup(700, 600, "Document");
    return true;
  }
  
  return false;
}

function printPack() {
  form = document.editFrm;

  if (checkFormOperation() && (form._choix_pack.value != 0)) {
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addElement(form.operation_id, "object_id");
    url.addElement(form._choix_pack, "pack_id");
    url.popup(700, 600, "Document");
    return true;
  }
  
  return false;
}

function printForm() {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "view_planning"); 
  url.addElement(document.editFrm.operation_id);
  url.popup(700, 500, url, "printPlanning");
  return;
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
  <tr>
    <td colspan="2">
       <a class="button" href="index.php?m={{$m}}&amp;operation_id=0">Programmer une nouvelle intervention</a>
    </td>
  </tr>
  {{/if}}
  <tr>
    {{if $op->operation_id}}
    <th colspan="2" class="title" style="color: #f00;">
      <button style="float:left;" type="button" onclick="popProtocole()">Choisir un protocole</button>
      <a style="float:right;" href="javascript:view_log('COperation',{{$op->operation_id}})">
        <img src="images/history.gif" alt="historique" />
      </a>
      Modification de l'intervention de {{$pat->_view}} par le Dr. {{$chir->_view}}
    </th>
    {{else}}
    <th colspan="2" class="title"> 
      <button style="float:left;" type="button" onclick="popProtocole()">Choisir un protocole</button>
      Création d'une intervention
    </th>
    {{/if}}
  </tr>
  <tr>
    <td>
      {{include file="inc_form_operation.tpl"}}
    </td>
    <td id="inc_form_sejour">
      {{include file="inc_form_sejour.tpl"}}
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="form">
        <tr>
          <td class="button">
          {{if $op->operation_id}}
            <input type="submit" value="Modifier" />
            <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{typeName:'l\'intervention du Dr',objName:'{{$op->_ref_chir->_view}}'})" />
            <input type="button" value="Annuler" onclick="if (confirm('Veuillez confirmer l\'annulation')) {var f = this.form; f.annulee.value = 1; f.rank.value = 0; f.submit();}" />
          {{else}}
            <input type="submit" value="Créer" />
          {{/if}}
          {{if $op->operation_id}}
            <input type="button" value="Imprimer" onClick="printForm();" />
            <select name="_choix_modele" onchange="printDocument()">
              <option value="">&mdash; Choisir un modèle</option>
              <optgroup label="Modèles du praticien">
              {{foreach from=$listModelePrat item=curr_modele}}
                <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
              {{/foreach}}
              </optgroup>
              <optgroup label="Modèles du cabinet">
              {{foreach from=$listModeleFunc item=curr_modele}}
                <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
              {{/foreach}}
              </optgroup>
            </select>
            <select name="_choix_pack" onchange="printPack()">
              <option value="">&mdash; Choisir un pack</option>
              {{foreach from=$listPack item=curr_pack}}
                <option value="{{$curr_pack->pack_id}}">{{$curr_pack->nom}}</option>
              {{/foreach}}
            </select>
          {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

