<!-- $Id: vw_addedit_planning.tpl 110 2006-06-11 20:19:38Z Rhum1 $ -->

<script type="text/javascript">

function putCCAM(code) {
  aSpec = new Array();
  aSpec[0] = "code";
  aSpec[1] = "ccam";
  aSpec[2] = "notNull";
  oCode = new Object();
  oCode.value = code
  if(sAlert = checkElement(oCode, aSpec)) {
    alert(sAlert);
    return false;
  }
  else {
    var oForm = document.editFrm;
    aCcam = oForm.codes_ccam.value.split("|");
    // Si la chaine est vide, il crée un tableau à un élément vide donc :
    aCcam.removeByValue("");
    aCcam.push(code);
    oForm.codes_ccam.value = aCcam.join("|");
    oForm._codeCCAM.value = "";
    refreshListCCAM();
    return true;
  }
}

function delCCAM(code) {
  var oForm = document.editFrm;
  var aCcam = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCcam.removeByValue("");
  aCcam.removeByValue(code, true);
  oForm.codes_ccam.value = aCcam.join("|");
  refreshListCCAM();
}

function refreshListCCAM() {
  oCcamNode = document.getElementById("listCodesCcam");
  var oForm = document.editFrm;
  var aCcam = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCcam.removeByValue("");
  
  var aCodeNodes = new Array();
  var iCode = 0;
  while (sCode = aCcam[iCode++]) {
    var sCodeNode = sCode;
    sCodeNode += "<button class='cancel notext' type='button' onclick='delCCAM(\"" + sCode + "\")'>";
    sCodeNode += "<\/button>";
    aCodeNodes.push(sCodeNode);
  }
  oCcamNode.innerHTML = aCodeNodes.join(" &mdash; ");
}


function checkFormSejour() {
  var oForm = document.editFrm;
  if (!checkForm(oForm)) {
    return false;
  }
  if (!checkDuree()) {
    return false;
  }
  if (!checkDureeHospi()) {
    return false;
  }
  if(!checkCCAM()) {
    return false
  }
  return true;
}

function checkCCAM() {
  var oForm = document.editFrm;
  var sCcam = oForm._codeCCAM.value;
  if(sCcam != "") {
    if(!putCCAM(sCcam)) {
      return false;
    }
  }
  delCCAM("XXXXXX");
  var sCodesCcam = oForm.codes_ccam.value;
  var sLibelle = oForm.libelle.value;
  if(sCodesCcam == "" && sLibelle == "") {
    alert("Vous indiquez un acte ou remplir le libellé");
    oForm.libelle.focus();
    return false;
  }
  return true;
}

function checkDureeHospi() {
  var form = document.editFrm;
  field1 = form.type;
  field2 = form.duree_hospi;
  if (field1 && field2) {
    var sTypeAdmission = "";
    for(i=0; i<field1.length; i++){
      if(field1[i].checked){
        sTypeAdmission = field1[i].value;
      }
    }
    
    if (sTypeAdmission=="comp" && (field2.value == 0 || field2.value == '')) {
      field2.value = prompt("Veuillez saisir une durée prévue d'hospitalisation d'au moins 1 jour", "1");
      field2.onchange();
      field2.focus();
      return false;
    }
    if (sTypeAdmission=="ambu" && field2.value != 0 && field2.value != '') {
      alert('Pour une admission de type Ambulatoire, la durée du séjour doit être de 0 jour.');
      field2.focus();
      return false;
    }
  }
  return true;
}

function checkChir() {
  var form = document.editFrm;
  var field = null;
  
  if (field = form.chir_id) {
    if (field.value == 0) {
      alert("Chirurgien manquant");
      popChir();
      return false;
    }
  }
  return true;
}

function checkDuree() {
  var form = document.editFrm;
  field1 = form._hour_op;
  field2 = form._min_op;
  if (field1 && field2) {
    if (field1.value == 0 && field2.value == 0) {
      alert("Temps opératoire invalide");
      field1.focus();
      return false;
    }
  }
  return true
}

function popCode(type) {
  var url = new Url();
  url.setModuleAction("dPplanningOp", "code_selector");
  url.addElement(document.editFrm.chir_id, "chir");
  url.addParam("type", type)
  url.popup(600, 500, type);
}

function setCode( key, type ) {
  if (key) {
    var form = document.editFrm;
    var field = form.DP;
    if (type == 'ccam')  field = form._codeCCAM;
    field.value = key;
  }
}

function pageMain() {
  refreshListCCAM();
}

</script>

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkFormSejour()">

<input type="hidden" name="dosql" value="do_protocole_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="protocole_id" value="{{$protocole->protocole_id}}" />

<table class="main" style="margin: 4px; border-spacing: 0px;">
  {{if $protocole->protocole_id}}
  <tr>
    <td colspan="2">
       <a class="buttonnew" href="index.php?m={{$m}}&amp;protocole_id=0">Créer un nouveau protocole</a>
    </td>
  </tr>
  {{/if}}

  <tr>
    {{if $protocole->protocole_id}}
    <th colspan="2" class="title" style="color: #f00;">
      <a style="float:right;" href="#" onclick="view_log('CProtocole',{{$protocole->protocole_id}})">
        <img src="images/history.gif" alt="historique" />
      </a>
      Modification du {{$protocole->_view}}
    </th>
    {{else}}
    <th colspan="2" class="title"> 
      Création d'un protocole
    </th>
    {{/if}}
  </tr>
  
  <tr>
    <td>
  
      <table class="form">
        <tr>
          <th class="category" colspan="3">
            Informations concernant l'opération
          </th>
        </tr>
        
        <tr>
          <th>
            <label for="chir_id" title="Praticien responsable. Obligatoire">Praticien</label>
          </th>
          <td colspan="2">
            <select name="chir_id" title="{{$protocole->_props.chir_id}}">
              <option value="">&mdash; Choisir un praticien</option>
              {{foreach from=$listPraticiens item=curr_praticien}}
              <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $chir->user_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
              {{$curr_praticien->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>
            <label for="_hour_op" title="Durée de l'intervention. Obligatoire">Temps opératoire</label>
          </th>
          <td colspan="2">
            <select name="_hour_op" title="notNull|num|min|0">
            {{foreach from=$hours|smarty:nodefaults key=key item=hour}}
              <option value="{{$key}}" {{if (!$protocole && $key == 1) || $protocole->_hour_op == $key}} selected="selected" {{/if}}>{{$key}}</option>
            {{/foreach}}
            </select> h 
            <select name="_min_op">
            {{foreach from=$mins|smarty:nodefaults item=min}}
              <option value="{{$min}}" {{if (!$protocole && $min == 0) || $protocole->_min_op == $min}} selected="selected" {{/if}}>{{$min}}</option>
            {{/foreach}}
            </select> mn
          </td>
        </tr>
        <tr>
          <th>
            <label for="_codeCCAM" title="Codes CCAM d'intervention">Ajout de codes CCAM</label>
          </th>
          <td>
            <input type="text" name="_codeCCAM" ondblclick="popCode('ccam')" size="10" value="" />
            <button class="tick notext" type="button" onclick="putCCAM(this.form._codeCCAM.value)"></button>
            
          </td>
          <td class="button"><button class="search" type="button" onclick="popCode('ccam')">Choisir un code</button></td>
        </tr>
        <tr>
          <th>
            Liste des codes CCAM:
            <input name="codes_ccam" type="hidden" value="{{$protocole->codes_ccam}}" />
          </th>
          <td colspan="2" class="text" id="listCodesCcam">
          </td>
        </tr>
        <tr>
          <th><label for="libelle" title="Libellé facultatif d'intervention">Libellé</label></th>
          <td colspan="2"><input type="text" name="libelle" title="{{$protocole->_props.libelle}}" size="50" value="{{$protocole->libelle}}"/></td>
        </tr>
        <tr>
          <td class="text"><label for="examen" title="Bilan pré-opératoire">Bilan pré-op</label></td>
          <td class="text"><label for="materiel" title="Matériel à prévoir / examens per-opératoire">Matériel à prévoir / examens per-op</label></td>
          <td class="text"><label for="rques_operation" title="Remarques sur l'intervention">Remarques</label></td>
        </tr>

        <tr>
          <td><textarea name="examen" title="{{$protocole->_props.examen}}" rows="3">{{$protocole->examen}}</textarea></td>
          <td><textarea name="materiel" title="{{$protocole->_props.materiel}}" rows="3">{{$protocole->materiel}}</textarea></td>
          <td><textarea name="rques_operation" title="{{$protocole->_props.rques_operation}}" rows="3">{{$protocole->rques_operation}}</textarea></td>
        </tr>
        <tr>
          <th><label for="depassement"title="Valeur du dépassement d'honoraire éventuel">Dépassement d'honoraire</label></th>
          <td colspan="2"><input name="depassement" title="{{$protocole->_props.depassement}}" type="text" size="4" value="{{$protocole->depassement}}" /> €</td>
        </tr>
      </table>
    </td>
    <td>
      <table class="form">
        <tr>
         <th class="category" colspan="3">Informations concernant le séjour</th>
       </tr>
        <tr>
          <th><label for="DP" title="Code CIM du diagnostic principal">Diagnostic principal (CIM)</label></th>
          <td><input type="text" name="DP" title="{{$protocole->_props.DP}}" size="10" value="{{$protocole->DP}}" /></td>
          <td class="button"><button type="button" class="search" onclick="popCode('cim10')">Choisir un code</button></td>
        </tr>
        <tr>
          <th><label for="duree_hospi" title="Durée d'hospitalisation en jours">Durée d'hospitalisation</label></th>
          <td colspan="2"><input type="text" name="duree_hospi" title="{{$protocole->_props.duree_hospi}}" size="2" value="{{$protocole->duree_hospi}}" /> jours</td>
        </tr>
        <tr>
          <th><label for="type_comp" title="Type d'admission">{{tr}}type_adm{{/tr}}</label></th>
          <td colspan="2">
            <input name="type" value="comp" type="radio" {{if !$protocole->protocole_id || $protocole->type == "comp"}}checked="checked"{{/if}} />
            <label for="type_comp">{{tr}}CProtocole.type.comp{{/tr}}</label><br />
            <input name="type" value="ambu" type="radio" {{if $protocole->type == "ambu"}}checked="checked"{{/if}} />
            <label for="type_ambu">{{tr}}CProtocole.type.ambu{{/tr}}</label><br />
            <input name="type" value="exte" type="radio" {{if $protocole->type == "exte"}}checked="checked"{{/if}} />
            <label for="type_exte">{{tr}}CProtocole.type.exte{{/tr}}</label><br />
          </td>
        </tr>
        <tr>
          <td><label for="convalescence" title="Convalescence post-opératoire">Convalescence</label></td>
          <td colspan="2"><label for="rques_sejour" title="Remarques générales sur le séjour">Remarques</label></td>
        </tr>
        <tr>
          <td><textarea name="convalescence" title="{{$protocole->_props.convalescence}}" rows="3">{{$protocole->convalescence}}</textarea></td>
          <td colspan="2"><textarea name="rques_sejour" title="{{$protocole->_props.rques_sejour}}" rows="3">{{$protocole->rques_sejour}}</textarea></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2">

      <table class="form">
        <tr>
          <td class="button">
          {{if $protocole->protocole_id}}
            <button class="modify" type="submit">Modifier</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le {{$protocole->_view|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
          {{else}}
            <button class="submit" type="submit">Créer</button>
          {{/if}}
          </td>
        </tr>
      </table>
    
    </td>
  </tr>

</table>

</form>
