<!-- $Id$ -->

<script type="text/javascript">
{literal}

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
    modifOp();
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
  modifOp();
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
    sCodeNode += "<button type='button' onclick='delCCAM(\"" + sCode + "\")'>";
    sCodeNode += "<img src='modules/dPplanningOp/images/cross.png' />";
    sCodeNode += "<\/button>";
    aCodeNodes.push(sCodeNode);
  }
  oCcamNode.innerHTML = aCodeNodes.join(" &mdash; ");
}


function checkFormOperation() {
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
    alert("Vous indiquez un acte ou remplir le libellé")
    oForm.libelle.focus();
    return false
  }
  return true;
}

function checkDureeHospi() {
  var form = document.editFrm;

  field1 = form.type_adm;
  field2 = form.duree_hospi;
  if (field1 && field2) {
    if (field1[0].checked && (field2.value == 0 || field2.value == '')) {
      field2.value = prompt("Veuillez saisir une durée prévue d'hospitalisation d'au moins 1 jour", "1");
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

function modifOp() {
  f = document.editFrm;
  if (f.saisie.value == 'o') {
    f.modifiee.value = 1;
    f.saisie.value = 'n';
  }
}

function popChir() {
  var url = new Url();
  url.setModuleAction("mediusers", "chir_selector");
  url.popup(400, 250, 'Chirurgien');
}

function setChir( key, val ){
  var f = document.editFrm;
  if (val != '') {
     f.chir_id.value = key;
     f._chir_name.value = val;
  }
}

function popPat() {
  var url = new Url();
  url.setModuleAction("dPpatients", "pat_selector");
  url.popup(800, 500, "Patient");
}

function setPat( key, val ) {
  var f = document.editFrm;

  if (val != '') {
    f.pat_id.value = key;
    f._pat_name.value = val;
  }
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
    var field = form.CIM10_code;
    if (type == 'ccam')  field = form._codeCCAM;
    field.value = key;
  }
}

function popPlage() {
  if (checkChir() & checkDuree()) {
    var oForm = document.editFrm;
    var url = new Url();
    url.setModuleAction("dPplanningOp", "plage_selector");
    url.addElement(oForm.chir_id, "chir");
    url.addElement(oForm._hour_op, "curr_op_hour");
    url.addElement(oForm._min_op, "curr_op_min");
    url.popup(400, 250, 'Plage');
  }
}

function setPlage(plage_id, sDate, bAdm) {
  var form = document.editFrm;

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
    

      var div_rdv_adm = document.getElementById("editFrm_date_adm_da");
      div_rdv_adm.innerHTML = makeLocaleDateFromDate(dAdm);
    }
  }
}

function popProtocole() {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "vw_protocoles");
  url.addElement(document.editFrm.chir_id);
  url.popup(700, 500, "Protocole");
}

function setProtocole(
    chir_id,
    chir_last_name,
    chir_first_name,
    prot_codes_ccam,
    prot_libelle,
    prot_hour_op,
    prot_min_op,
    prot_examen,
    prot_materiel,
    prot_convalescence,
    prot_depassement,
    prot_type_adm,
    prot_duree_hospi,
    prot_rques) {

  var form = document.editFrm;
  
  form.chir_id.value       = chir_id;
  form._chir_name.value    = chir_last_name + " " + chir_first_name;
  form.codes_ccam.value    = prot_codes_ccam;
  refreshListCCAM();
  form.libelle.value       = prot_libelle;

  // Hospitalisation ?
  if (form._hour_op && form._min_op) {
    form._hour_op.value      = prot_hour_op;
    form._min_op.value       = prot_min_op;
    form.materiel.value      = prot_materiel;
    form.convalescence.value = prot_convalescence;
  }
  
  form.examen.value        = prot_examen;
  form.depassement.value   = prot_depassement;
  setRadioValue(form.type_adm, prot_type_adm);
  form.duree_hospi.value   = prot_duree_hospi;
  form.rques.value         = prot_rques;
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
  regFieldCalendar("editFrm", "date_anesth");
  regFieldCalendar("editFrm", "date_adm");
  refreshListCCAM();
}

{/literal}
</script>

<form name="editFrm" action="?m={$m}" method="post" onsubmit="return checkFormOperation()">

<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="operation_id" value="{$op->operation_id}" />
<input type="hidden" name="commande_mat" value="{$op->commande_mat}" />
<input type="hidden" name="rank" value="{$op->rank}" />
<input type="hidden" name="saisie" value="{$op->saisie}" />
<input type="hidden" name="annulee" value="0" />
<input type="hidden" name="modifiee" value="{$op->modifiee}" />

<table class="main" style="margin: 4px; border-spacing: 0px;">
  {if $op->operation_id}
  <tr>
    {if $hospitalisation}
    <td>
        <a class="button" href="index.php?m={$m}&amp;hospitalisation_id=0">Créer une nouvelle hospitalisation</a>
    </td>
    <td>
        <a href="index.php?m={$m}&amp;tab=vw_edit_planning&amp;operation_id={$op->operation_id}&amp;trans=1">Programmer une intervention pour ce patient</a>
    </td>
    {else}
    <td colspan="2">
       {if $protocole}
       <a class="button" href="index.php?m={$m}&amp;protocole_id=0">Créer un nouveau protocole</a>
       {else}
       <a class="button" href="index.php?m={$m}&amp;operation_id=0">Programmer une nouvelle intervention</a>
       {/if}
    </td>
    {/if}
  </tr>
  {/if}

  <tr>
    {if $op->operation_id}
    <th colspan="2" class="title" style="color: #f00;">
      <a style="float:right;" href="javascript:view_log('COperation',{$op->operation_id})">
        <img src="images/history.gif" alt="historique" />
      </a>
      {if $protocole}
      Modification du protocole {$op->_codes_ccam} du Dr. {$chir->_view}
      {elseif $hospitalisation}
      Modification de l'hospitalisation de {$pat->_view} par le Dr. {$chir->_view}
      {else}
      Modification de l'intervention de {$pat->_view} par le Dr. {$chir->_view}
      {/if}
    </th>
    {else}
    <th colspan="2" class="title">      
      {if $protocole}
      Création d'un protocole
      {elseif $hospitalisation}
      Création d'une hospitalisation
      {else}
      Création d'une intervention
      {/if}
    </th>
    {/if}
  </tr>
  
  <tr>
    <td>
  
      <table class="form">
        <tr>
          <th class="category" colspan="3">
            {if $protocole}
            Informations concernant le protocole
            {elseif $hospitalisation}
            Informations concernant l'hospitalisation
            {else}
            Informations concernant l'opération
            {/if}
          </th>
        </tr>

        {if !$protocole}
        <tr>
          <td class="button" colspan="3">
            <input type="button" value="Choisir un protocole" onclick="popProtocole()" />
          </td>
        </tr>
        {/if}
        
        <tr>
          <th class="mandatory">
            <input type="hidden" name="chir_id" title="{$op->_props.chir_id}" value="{$chir->user_id}" ondblclick="popChir()" />
            <label for="chir_id" title="Chirurgien Responsable. Obligatoire">Chirurgien :</label>
          </th>
          <td class="readonly"><input type="text" name="_chir_name" size="30" value="{if $chir->user_id}{$chir->_view}{/if}" readonly="readonly" /></td>
          <td class="button"><input type="button" value="choisir un chirurgien" onclick="popChir()" /></td>
        </tr>

        {if !$protocole}
        <tr>
          <th class="mandatory">
            <input type="hidden" name="pat_id" title="{$op->_props.pat_id}|notNull" ondblclick="popPat()" value="{$pat->patient_id}" />
            <label for="pat_id" title="Patient concerné. Obligatoire">Patient :</label>
          </th>
          <td class="readonly"><input type="text" name="_pat_name" size="30" value="{$pat->_view}" readonly="readonly" /></td>
          <td class="button"><input type="button" value="Rechercher un patient" onclick="popPat()" /></td>
        </tr>
        {/if}
        
        {if !$hospitalisation}
        <tr>
          <th class="mandatory">
            <label for="_hour_op" title="Durée de l'intervention. Obligatoire">Temps opératoire :</label>
          </th>
          <td colspan="2">
            <select name="_hour_op">
            {foreach from=$hours key=key item=hour}
              <option value="{$key}" {if (!$op && $key == 1) || $op->_hour_op == $key} selected="selected" {/if}>{$key}</option>
            {/foreach}
            </select>
            :
            <select name="_min_op">
            {foreach from=$mins item=min}
              <option value="{$min}" {if (!$op && $min == 0) || $op->_min_op == $min} selected="selected" {/if}>{$min}</option>
            {/foreach}
            </select>
          </td>
        </tr>
        
        {if !$protocole}
        <tr>
          <th class="mandatory">
            <input type="hidden" name="plageop_id" title="{$op->_props.pat_id}|notNull" ondblclick="popPlage()" value="{$plage->id}" />
            <label for="plageop_id" title="Date de l'intervention. Obligatoire">Date de l'intervention :</label>
          </th>
          <td class="readonly"><input type="text" name="date" readonly="readonly" size="10" value="{$plage->_date}" /></td>
          <td class="button"><input type="button" value="Choisir une date" onclick="popPlage()" /></td>
        </tr>
        {/if}
        {/if}

        <tr>
          <th><label for="CIM10_code" title="Code CIM10 de diagnostic">Diagnostic (CIM10) :</label></th>
          <td><input type="text" name="CIM10_code" title="{$op->_props.CIM10_code}" size="10" value="{$op->CIM10_code}" /></td>
          <td class="button"><input type="button" value="Sélectionner un code" onclick="popCode('cim10')" /></td>
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
          <td class="button"><input type="button" value="Sélectionner un code" onclick="popCode('ccam')"/></td>
        </tr>

        <tr>
          <th>
            Liste des codes CCAM:
            <input name="codes_ccam" type="hidden" value="{$op->codes_ccam}" />
          </th>
          <td colspan="2" class="text" id="listCodesCcam">
          </td>
        </tr>
        
        <tr>
          <th><label for="libelle" title="Libellé facultatif d'intervention">Libellé :</label></th>
          <td colspan="2"><input type="text" name="libelle" title="{$op->_props.libelle}" size="70" value="{$op->libelle}"/></td>
        </tr>
        
        {if !$protocole}
        <tr>
          <th class="mandatory"><label for="cote" title="Côté concerné par l'intervention">Côté :</label></th>
          <td colspan="2">
            <select name="cote" title="{$op->_props.cote}" onchange="modifOp()">
              <option value="total"     {if !$op || $op->cote == "total"} selected="selected" {/if} >total</option>
              <option value="droit"     {if $op->cote == "droit"        } selected="selected" {/if} >droit    </option>
              <option value="gauche"    {if $op->cote == "gauche"       } selected="selected" {/if} >gauche   </option>
              <option value="bilatéral" {if $op->cote == "bilatéral"    } selected="selected" {/if} >bilatéral</option>
            </select>
          </td>
        </tr>
        {/if}
        
        {if $hospitalisation}
        <tr>
          <th><label for="examen" title="Bilan pré-opératoire">Bilan pré-op</label></th>
          <td colspan="2"><textarea name="examen" title="{$op->_props.examen}" rows="3">{$op->examen}</textarea></td>
        </tr>
        {else}
        <tr>
          <td class="text"><label for="examen" title="Bilan pré-opératoire">Bilan pré-op</label></td>
          <td class="text"><label for="materiel" title="Matériel à prévoir / examens per-opératoire">Matériel à prévoir / examens per-op</label></td>
          <td class="text"><label for="convalescence" title="Convalescence post-opératoire">Convalescence</label></td>
        </tr>

        <tr>
          <td><textarea name="examen" title="{$op->_props.examen}" rows="3">{$op->examen}</textarea></td>
          <td><textarea name="materiel" title="{$op->_props.materiel}" rows="3">{$op->materiel}</textarea></td>
          <td><textarea name="convalescence" title="{$op->_props.convalescence}" rows="3">{$op->convalescence}</textarea></td>
        </tr>
        {/if}
        
        <tr>
          <th><label for="depassement"title="Valeur du dépassement d'honoraire éventuel">Dépassement d'honoraire :</label></th>
          <td colspan="2"><input name="depassement" title="{$op->_props.depassement}" type="text" size="4" value="{$op->depassement}" /> €</td>
        </tr>

        {if !$protocole}
        <tr>
          <th><label for="info_n">Information du patient :</label></th>
          <td colspan="2">
            <input name="info" value="o" type="radio" {if $op->info == "o"} checked="checked" {/if}/>
            <label for="info_o">Oui</label>
            <input name="info" value="n" type="radio" {if !$op->operation_id || $op->info == "n"} checked="checked" {/if}/>
            <label for="info_n">Non</label>
          </td>
        </tr>
        {/if}

      </table>

    </td>
    <td>

      <table class="form">
        {if !$protocole && !$hospitalisation}
        <tr><th class="category" colspan="3">RDV d'anesthésie</th></tr>

        <tr>
          <th><label for="date_anesth_trigger" title="Date de rendez-vous d'anesthésie">Date :</label></th>
          <td class="date">
            <div id="editFrm_date_anesth_da">{$op->date_anesth|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="date_anesth" title="{$op->_props.date_anesth}" value="{$op->date_anesth}" onchange="modifOp()" />
            <img id="editFrm_date_anesth_trigger" src="./images/calendar.gif" alt="calendar"/>
          </td>
        </tr>

        <tr>
          <th><label for="_hour_anesth">Heure :</label></th>
          <td>
            <select name="_hour_anesth">
            {foreach from=$hours item=hour}
              <option {if $op->_hour_anesth == $hour} selected="selected" {/if}>{$hour}</option>
            {/foreach}
            </select>
            :
            <select name="_min_anesth">
            {foreach from=$mins item=min}
              <option {if $op->_min_anesth == $min} selected="selected" {/if}>{$min}</option>
            {/foreach}
            </select>
          </td>
        </tr>
        {/if}
        
        <tr><th class="category" colspan="3">Admission</th></tr>

        {if !$protocole}
        <tr>
          <th><label for="date_adm" title="Choisir une date d'admission">Date :</label></th>
          <td class="date">
            <div id="editFrm_date_adm_da">{$op->date_adm|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="date_adm" title="{$op->_props.date_adm}|notNull" value="{$op->date_adm}" onchange="modifOp()"/>
            <img id="editFrm_date_adm_trigger" src="./images/calendar.gif" alt="calendar"/>
          </td>
        </tr>

        <tr>
          <th class="mandatory"><label for="_hour_adm" title="Heure d'admission">Heure :</label></th>
          <td>
            <select name="_hour_adm">
            {foreach from=$hours item=hour}
              <option value="{$hour}" {if $op->_hour_adm == $hour} selected="selected" {/if}>{$hour}</option>
            {/foreach}
            </select>
            :
            <select name="_min_adm">
            {foreach from=$mins item=min}
              <option value="{$min}" {if $op->_min_adm == $min} selected="selected" {/if}>{$min}</option>
            {/foreach}
            </select>
          </td>
        </tr>
        {/if}

        <tr>
          <th class="mandatory"><label for="duree_hospi" title="Durée d'hospitalisation en jours">Durée d'hospitalisation :</label></th>
          <td><input type="text" name="duree_hospi" title="{$op->_props.duree_hospi}" size="2" value="{$op->duree_hospi}" /> jours</td>
        </tr>
        <tr>
          <th><label for="type_adm_comp" title="Type d'admission">{tr}type_adm{/tr} :</label></th>
          <td>
            <input name="type_adm" value="comp" type="radio" {if !$op->operation_id || $op->type_adm == "comp"}checked="checked"{/if} onchange="modifOp()" />
            <label for="type_adm_comp">{tr}comp{/tr}</label><br />
            <input name="type_adm" value="ambu" type="radio" {if $op->type_adm == "ambu"}checked="checked"{/if} onchange="modifOp()" />
            <label for="type_adm_ambu">{tr}ambu{/tr}</label><br />
            <input name="type_adm" value="exte" type="radio" {if $op->type_adm == "exte"}checked="checked"{/if} onchange="modifOp()" />
            <label for="type_adm_exte">{tr}exte{/tr}</label><br />
          </td>
        </tr>
        
        {if !$protocole}
        <tr>
          <th><label for="chambre_o" title="Patient à placer dans une chambre particulière">Chambre particulière :</label></th>
          <td>
            <input name="chambre" value="o" type="radio" {if $op->operation_id && $op->_ref_sejour->chambre_seule == "o"} checked="checked" {/if} onchange="modifOp()" />
            <label for="chambre_o">Oui</label>
            <input name="chambre" value="n" type="radio" {if !$op->operation_id || $op->_ref_sejour->chambre_seule == "n"} checked="checked" {/if} onchange="modifOp()" />
            <label for="chambre_n">Non</label>
        </tr>
        <tr>
          <th>
            <label for="venue_SHS" title="Code Administratif SHS">
              Code de venue SHS :
            </label>
          </th>
          <td>
            <input type="text" size="8" maxlength="8" name="venue_SHS" title="{$op->_props.venue_SHS}" value="{$op->venue_SHS}" />
          </td>
        </tr>
        <tr><th class="category" colspan="3">Autre</th></tr>
        <tr>
          <th><label for="ATNC_n" title="Intervention présantant un risque ATNC">Risque ATNC:</label></th>
          <td>
            <input name="ATNC" value="o" type="radio" {if $op->ATNC == "o"} checked="checked" {/if} />
            <label for="ATNC_o">Oui</label>
            <input name="ATNC" value="n" type="radio" {if !$op || $op->ATNC == "n"} checked="checked" {/if} />
            <label for="ATNC_n">Non</label>
          </td>
        </tr>
        
        {/if}
        <tr>
          <th><label for="rques" title="Remarques générales sur l'intervention">Remarques :</label></th>
          <td><textarea name="rques" rows="3">{$op->rques}</textarea></td>
        </tr>

      </table>
    
    </td>
  </tr>

  <tr>
    <td colspan="2">

      <table class="form">
        <tr>
          <td class="button">
          {if $op->operation_id}
            <input type="reset" value="Réinitialiser" />
            <input type="submit" value="Modifier" />
            <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{ldelim}typeName:'l\'intervention du Dr',objName:'{$op->_ref_chir->_view}'{rdelim})" />
            <input type="button" value="Annuler" onclick="{literal}if (confirm('Veuillez confirmer l\'annulation')) {var f = this.form; f.annulee.value = 1; f.rank.value = 0; f.submit();}{/literal}" />
          {else}
            <input type="submit" value="Créer" />
          {/if}
          {if !$protocole}
            {if $op->operation_id}
            <input type="button" value="Imprimer" onClick="printForm();" />
            <select name="_choix_modele" onchange="printDocument()">
              <option value="">&mdash; Choisir un modèle</option>
              <optgroup label="Modèles du praticien">
              {foreach from=$listModelePrat item=curr_modele}
                <option value="{$curr_modele->compte_rendu_id}">{$curr_modele->nom}</option>
              {/foreach}
              </optgroup>
              <optgroup label="Modèles du cabinet">
              {foreach from=$listModeleFunc item=curr_modele}
                <option value="{$curr_modele->compte_rendu_id}">{$curr_modele->nom}</option>
              {/foreach}
              </optgroup>
            </select>
            <select name="_choix_pack" onchange="printPack()">
              <option value="">&mdash; Choisir un pack</option>
              {foreach from=$listPack item=curr_pack}
                <option value="{$curr_pack->pack_id}">{$curr_pack->nom}</option>
              {/foreach}
            </select>
            {/if}
          {/if}
          </td>
        </tr>
      </table>
    
    </td>
  </tr>

</table>

</form>
