<!-- $Id: $ -->

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
    var oForm = document.editOp;
    aCcam = oForm.codes_ccam.value.split("|");
    // Si la chaine est vide, il crée un tableau à un élément vide donc :
    aCcam.removeByValue("");
    aCcam.push(code);
    oForm.codes_ccam.value = aCcam.join("|");
    oForm._codeCCAM.value = "";
    refreshListCCAM();
    modifOp();
    return true;
  }
}

function delCCAM(code) {
  var oForm = document.editOp;
  var aCcam = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCcam.removeByValue("");
  aCcam.removeByValue(code, true);
  oForm.codes_ccam.value = aCcam.join("|");
  refreshListCCAM();
  modifOp();
}

function refreshListCCAM() {
  oCcamNode = document.getElementById("listCodesCcam");
  var oForm = document.editOp;
  var aCcam = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCcam.removeByValue("");
  
  var aCodeNodes = new Array();
  var iCode = 0;
  while (sCode = aCcam[iCode++]) {
    var sCodeNode = sCode;
    sCodeNode += "<button type='button' onclick='delCCAM(\"" + sCode + "\")'>";
    sCodeNode += "<img src='modules/dPplanningOp/images/cross.png' />";
    sCodeNode += "<\/button>";
    aCodeNodes.push(sCodeNode);
  }
  oCcamNode.innerHTML = aCodeNodes.join(" &mdash; ");
}


function checkFormOperation() {
  var oForm = document.editOp;
  
  if (!checkForm(oForm)) {
    return false;
  }

  if (!checkDuree()) {
    return false;
  }
  
  if(!checkCCAM()) {
    return false
  }

  return true;
}

function checkCCAM() {
  var oForm = document.editOp;
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
    alert("Vous indiquez un acte ou remplir le libellé")
    oForm.libelle.focus();
    return false
  }
  return true;
}

function checkDuree() {
  var form = document.editOp;
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

function modifOp() {
  f = document.editOp;
  if (f.saisie.value == 'o') {
    f.modifiee.value = 1;
    f.saisie.value = 'n';
  }
}

function popPat() {
  var url = new Url();
  url.setModuleAction("dPpatients", "pat_selector");
  url.popup(800, 500, "Patient");
}

function setPat( key, val ) {
  var f = document.editOp;

  if (val != '') {
    f.pat_id.value = key;
    f._pat_name.value = val;
  }
}

function popPlage() {
  if (checkChir() & checkDuree()) {
    var oForm = document.editOp;
    var url = new Url();
    url.setModuleAction("dPplanningOp", "plage_selector");
    url.addElement(oForm.chir_id, "chir");
    url.addElement(oForm._hour_op, "curr_op_hour");
    url.addElement(oForm._min_op, "curr_op_min");
    url.popup(400, 250, 'Plage');
  }
}

function setPlage(plage_id, sDate, bAdm) {
  var form = document.editOp;

  if (plage_id) {
    form.plageop_id.value = plage_id;
    form.date.value = sDate;
    
    // Initialize adminission date according to operation date
    var dAdm = makeDateFromLocaleDate(sDate);
    switch(bAdm) {
      case 0 :
        dAdm.setHours(17);
        dAdm.setDate(dAdm.getDate()-1);
        break;
      case 1 :
        dAdm.setHours(8);
        break;
    }
    
    if (bAdm != 2) {
      form._hour_adm.value = dAdm.getHours();
      form._min_adm.value = dAdm.getMinutes();
      form.date_adm.value = makeDATEFromDate(dAdm);
    

      var div_rdv_adm = document.getElementById("editOp_date_adm_da");
      div_rdv_adm.innerHTML = makeLocaleDateFromDate(dAdm);
    }
  }
}
  
function incFormOperationMain() {
  regFieldCalendar("editOp", "date_anesth");
  refreshListCCAM();
}

</script>
  

<form name="editOp" action="?m={{$m}}" method="post" onsubmit="return checkFormOperation()">

<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="operation_id" value="{{$op->operation_id}}" />
<input type="hidden" name="commande_mat" value="{{$op->commande_mat}}" />
<input type="hidden" name="rank" value="{{$op->rank}}" />
<input type="hidden" name="annulee" value="{{$op->annulee}}" />
     
<table class="form">
  <tr>
    <th class="category" colspan="3">
      Informations concernant l'opération
    </th>
  </tr>
  
  <tr>
    <th class="mandatory">
      <input type="hidden" name="chir_id" title="{{$op->_props.chir_id}}" value="{{$chir->user_id}}" ondblclick="popChir()" />
      <label for="chir_id" title="Chirurgien Responsable. Obligatoire">Chirurgien :</label>
    </th>
    <td colspan="2">
      <select name="praticien_id" title="{{$sejour->_props.praticien_id}}">
        <option value="">&mdash; Choisir un praticien</option>
        {{foreach from=$listPraticiens item=curr_praticien}}
        <option value="{{$curr_praticien->user_id}}" {{if $chir->user_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr
  >
  <tr>
    <th class="mandatory">
      <label for="_hour_op" title="Durée de l'intervention. Obligatoire">Temps opératoire :</label>
    </th>
    <td colspan="2">
      <select name="_hour_op">
      {{foreach from=$hours_duree key=key item=hour}}
        <option value="{{$key}}" {{if (!$op && $key == 1) || $op->_hour_op == $key}} selected="selected" {{/if}}>{{$key}}</option>
      {{/foreach}}
      </select> h
      <select name="_min_op">
      {{foreach from=$mins_duree item=min}}
        <option value="{{$min}}" {{if (!$op && $min == 0) || $op->_min_op == $min}} selected="selected" {{/if}}>{{$min}}</option>
      {{/foreach}}
    </select> mn
    </td>
  </tr>

  <tr>
    <th class="mandatory">
      <input type="hidden" name="plageop_id" title="{{$op->_props.plageop_id}}|notNull" ondblclick="popPlage()" value="{{$plage->id}}" />
      <label for="plageop_id" title="Date de l'intervention. Obligatoire">Date de l'intervention :</label>
    </th>
    <td class="readonly"><input type="text" name="date" readonly="readonly" size="10" value="{{$plage->_date}}" /></td>
    <td class="button"><input type="button" value="Choisir une date" onclick="popPlage()" /></td>
  </tr>
  
  <tr>
    <th>
      <label for="_codeCCAM" title="Codes CCAM d'intervention">Ajout de codes CCAM :</label>
    </th>
    <td>
      <input type="text" name="_codeCCAM" ondblclick="popCode('ccam')" size="10" value="" />
      <button type="button" onclick="putCCAM(this.form._codeCCAM.value)">
         <img src="modules/dPplanningOp/images/tick.png" alt="ajouter" />
      </button>
    </td>
    <td class="button">
      <input type="button" value="Sélectionner un code" onclick="popCode('ccam')"/>
    </td>
  </tr>

  <tr>
    <th>
      Liste des codes CCAM:
      <input name="codes_ccam" type="hidden" value="{{$op->codes_ccam}}" />
    </th>
    <td colspan="2" class="text" id="listCodesCcam">
    </td>
  </tr>
  
  <tr>
    <th><label for="libelle" title="Libellé facultatif d'intervention">Libellé :</label></th>
    <td colspan="2"><input type="text" name="libelle" title="{{$op->_props.libelle}}" size="70" value="{{$op->libelle}}"/></td>
  </tr>
  
  <tr>
    <th class="mandatory"><label for="cote" title="Côté concerné par l'intervention">Côté :</label></th>
    <td colspan="2">
      <select name="cote" title="{{$op->_props.cote}}" onchange="modifOp()">
        <option value="total"     {{if !$op || $op->cote == "total"}} selected="selected" {{/if}} >total</option>
        <option value="droit"     {{if $op->cote == "droit"        }} selected="selected" {{/if}} >droit    </option>
        <option value="gauche"    {{if $op->cote == "gauche"       }} selected="selected" {{/if}} >gauche   </option>
        <option value="bilatéral" {{if $op->cote == "bilatéral"    }} selected="selected" {{/if}} >bilatéral</option>
      </select>
    </td>
  </tr>

  <tr>
    <td class="text"><label for="examen" title="Bilan pré-opératoire">Bilan pré-op</label></td>
    <td class="text"><label for="materiel" title="Matériel à prévoir / examens per-opératoire">Matériel à prévoir / examens per-op</label></td>
    <td class="text"><label for="rques" title="Remarques sur l'iitervention">Remarques</label></td>
  </tr>

  <tr>
    <td><textarea name="examen" title="{{$op->_props.examen}}" rows="3">{{$op->examen}}</textarea></td>
    <td><textarea name="materiel" title="{{$op->_props.materiel}}" rows="3">{{$op->materiel}}</textarea></td>
    <td><textarea name="rques" title="{{$op->_props.rques}}" rows="3">{{$op->rques}}</textarea></td>
  </tr>
  
  <tr>
    <th><label for="depassement"title="Valeur du dépassement d'honoraire éventuel">Dépassement d'honoraire :</label></th>
    <td colspan="2"><input name="depassement" title="{{$op->_props.depassement}}" type="text" size="4" value="{{$op->depassement}}" /> €</td>
  </tr>
  
  <tr>
    <th><label for="info_n">Information du patient :</label></th>
    <td colspan="2">
      <input name="info" value="o" type="radio" {{if $op->info == "o"}} checked="checked" {{/if}}/>
      <label for="info_o">Oui</label>
      <input name="info" value="n" type="radio" {{if !$op->operation_id || $op->info == "n"}} checked="checked" {{/if}}/>
      <label for="info_n">Non</label>
    </td>
  </tr>

  <tr>
    <th class="category" colspan="3">RDV d'anesthésie</th>
  </tr>

  <tr>
    <th>
      <label for="date_anesth" title="Choisir une date rendez-vous d'anesthésie">Date de RdV :</label>
    </th>
    <td class="date">
      <div id="editOp_date_anesth_da">{{$op->date_anesth|date_format:"%d/%m/%Y"}}</div>
      <input type="hidden" name="date_anesth" title="{{$op->_props.date_anesth}}" value="{{$op->date_anesth}}" onchange="modifOp()" />
      <img id="editOp_date_anesth_trigger" src="./images/calendar.gif" alt="calendar"/>
    </td>
    <td colspan="2">
      à
      <select name="_hour_anesth">
      {{foreach from=$hours item=hour}}
        <option {{if $op->_hour_anesth == $hour}} selected="selected" {{/if}}>{{$hour}}</option>
      {{/foreach}}
      </select> h      <select name="_min_anesth">
      {{foreach from=$mins item=min}}
        <option {{if $op->_min_anesth == $min}} selected="selected" {{/if}}>{{$min}}</option>
      {{/foreach}}
      </select> mn
    </td>
  </tr>
</table>

</form>
