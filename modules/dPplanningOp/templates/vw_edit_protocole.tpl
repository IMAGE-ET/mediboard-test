<!-- $Id: vw_addedit_planning.tpl 110 2006-06-11 20:19:38Z Rhum1 $ -->

<script type="text/javascript">

var oCcamField = null;

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
    sCodeNode += "<button class='cancel notext' type='button' onclick='oCcamField.remove(\"" + sCode + "\")'>";
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
    if(!oCcamField.add(sCcam,true)) {
      return false;
    }
  }
  oCcamField.remove("XXXXXX");
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
    if (field1.value=="comp" && (field2.value == 0 || field2.value == '')) {
      field2.value = prompt("Veuillez saisir une durée prévue d'hospitalisation d'au moins 1 jour", "1");
      field2.onchange();
      field2.focus();
      return false;
    }
    if (field1.value=="ambu" && field2.value != 0 && field2.value != '') {
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

  aSpecCcam = new Array();
  aSpecCcam[0] = "code";
  aSpecCcam[1] = "ccam";
  aSpecCcam[2] = "notNull";
  
  oCcamField = new TokenField(document.editFrm.codes_ccam, { 
    onChange : refreshListCCAM,
    aSpec : aSpecCcam
    } );
}

</script>

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkFormSejour()">

<input type="hidden" name="dosql" value="do_protocole_aed" />
<input type="hidden" name="del" value="0" />
{{mb_field object=$protocole field="protocole_id" hidden=1 prop=""}}

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
    <th colspan="2" class="title modify">
      <a style="float:right;" href="#" onclick="view_log('CProtocole',{{$protocole->protocole_id}})">
        <img src="images/icons/history.gif" alt="historique" />
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
            {{mb_label object=$protocole field="chir_id"}}
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
            {{mb_label object=$protocole field="_hour_op"}}
          </th>
          <td colspan="2">
            <select name="_hour_op" title="notNull num min|0">
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
            {{mb_label object=$protocole field="codes_ccam"}}
          </th>
          <td>
            <input type="text" name="_codeCCAM" ondblclick="popCode('ccam')" size="10" value="" />
            <button class="tick notext" type="button" onclick="oCcamField.add(this.form._codeCCAM.value,true)"></button>
            
          </td>
          <td class="button"><button class="search" type="button" onclick="popCode('ccam')">Choisir un code</button></td>
        </tr>
        <tr>
          <th>
            Liste des codes CCAM:
            {{mb_field object=$protocole field="codes_ccam" hidden=1 prop=""}}
          </th>
          <td colspan="2" class="text" id="listCodesCcam">
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$protocole field="libelle"}}</th>
          <td colspan="2">{{mb_field object=$protocole field="libelle" size="50"}}</td>
        </tr>
        <tr>
          <td class="text">{{mb_label object=$protocole field="examen"}}</td>
          <td class="text">{{mb_label object=$protocole field="materiel"}}</td>
          <td class="text">{{mb_label object=$protocole field="rques_operation"}}</td>
        </tr>

        <tr>
          <td>{{mb_field object=$protocole field="examen" rows="3"}}</td>
          <td>{{mb_field object=$protocole field="materiel" rows="3"}}</td>
          <td>{{mb_field object=$protocole field="rques_operation" rows="3"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$protocole field="depassement"}}</th>
          <td colspan="2">{{mb_field object=$protocole field="depassement" size="4"}} €</td>
        </tr>
      </table>
    </td>
    <td>
      <table class="form">
        <tr>
         <th class="category" colspan="3">Informations concernant le séjour</th>
       </tr>
        <tr>
          <th>{{mb_label object=$protocole field="DP"}}</th>
          <td>{{mb_field object=$protocole field="DP" size="10"}}</td>
          <td class="button"><button type="button" class="search" onclick="popCode('cim10')">Choisir un code</button></td>
        </tr>
        <tr>
          <th>{{mb_label object=$protocole field="duree_hospi"}}</th>
          <td colspan="2">{{mb_field object=$protocole field="duree_hospi" size="2"}} jours</td>
        </tr>
        <tr>
          <th>{{mb_label object=$protocole field="type"}}</th>
          <td colspan="2">
            {{mb_field object=$protocole field="type"}}
          </td>
        </tr>
        <tr>
          <td>{{mb_label object=$protocole field="convalescence"}}</td>
          <td colspan="2">{{mb_label object=$protocole field="rques_sejour"}}</td>
        </tr>
        <tr>
          <td>{{mb_field object=$protocole field="convalescence" rows="3"}}</td>
          <td colspan="2">{{mb_field object=$protocole field="rques_sejour" rows="3"}}</td>
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
