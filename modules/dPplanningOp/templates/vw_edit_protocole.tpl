{{mb_include_script module="dPplanningOp" script="cim10_selector"}}
{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

<script type="text/javascript">

var oCcamField = null;

function copier(){
  var oForm = document.editFrm;
  
  oForm.chir_id.value = "{{$mediuser->user_id}}";
  if(oForm.chir_id.value != "{{$mediuser->user_id}}") {
    alert("Vous n\'êtes pas un praticien, vous ne pouvez pas dupliquer ce protocole");
    return;
  }
  oForm.protocole_id.value = "";
  if(oForm.libelle.value){
    oForm.libelle.value = "Copie de "+oForm.libelle.value;
  } else {
    oForm.libelle.value = "Copie de "+oForm.codes_ccam.value;
  }
  oForm.submit();
}

function refreshListProtocolesPrescription(praticien_id, selected_id) {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "httpreq_vw_list_protocoles_prescription");
  url.addParam("praticien_id", praticien_id);
  url.addParam("without_pack", true);
  //url.addParam("selected_id", selected_id || "{{$protocole->protocole_prescription_anesth_id}}");
  //url.requestUpdate(document.editFrm.protocole_prescription_anesth_id, { waitingText: null } );

  url.addParam("selected_id", selected_id || "{{$protocole->protocole_prescription_chir_id}}");
  if (document.editFrm.protocole_prescription_chir_id) {
    url.requestUpdate(document.editFrm.protocole_prescription_chir_id, { waitingText: null } );
  }
}

function refreshListCCAM() {
  oCcamNode = $("listCodesCcam");

  var oForm = document.editFrm;
  oForm._codeCCAM.value="";
  var aCcam = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCcam = aCcam.without("");
  
  var aCodeNodes = new Array();
  var iCode = 0;
  
  while (sCode = aCcam[iCode++]) {
    var sCodeNode = sCode;
      sCodeNode += '<button class="cancel notext" type="button" onclick="oCcamField.remove(\'' + sCode + '\')"></button>';
    aCodeNodes.push(sCodeNode);
  }
  oCcamNode.innerHTML = aCodeNodes.join(", ");
}

function checkFormSejour() {
  var oForm = document.editFrm;
  return checkForm(oForm) && checkDuree() && checkDureeHospi() && checkCCAM();
}

function checkCCAM() {
  var oForm = document.editFrm;
  if ($V(oForm.for_sejour) == 1) return true;
  
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
  if ($V(form.for_sejour) == 1) return true;
  
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
  
  if ($V(form.for_sejour) == 1) return true; // Si mode séjour
  
  if (field1 && field2) {
    if (field1.value == 0 && field2.value == 0) {
      alert("Temps opératoire invalide");
      field1.focus();
      return false;
    }
  }
  return true;
}

function setOperationActive(active) {
  var op = $('operation'),
      form = getForm('editFrm');
  op.setOpacity(active ? 1 : 0.4);
  op.select('input, button, select, textarea').each(Form.Element[active ? 'enable' : 'disable']);
}

Main.add(function () {
  refreshListCCAM();

  refreshListProtocolesPrescription($V(document.editFrm.chir_id));
  
  setOperationActive($V(getForm('editFrm').for_sejour) == 0);
  
  oCcamField = new TokenField(document.editFrm.codes_ccam, { 
    onChange : refreshListCCAM,
    sProps : "notNull code ccam"
  } );
});

</script>

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkFormSejour()">

<input type="hidden" name="dosql" value="do_protocole_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="_class_name_" value="COperation" />

{{mb_field object=$protocole field="protocole_id" hidden=1 prop=""}}

<table class="main" style="margin: 4px; border-spacing: 0px;">
  {{if $protocole->protocole_id}}
  <tr>
    <td colspan="2">
       <a class="button new" href="?m={{$m}}&amp;protocole_id=0">Créer un nouveau protocole</a>
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
    <td colspan="2" style="padding: 2px; text-align: center;">
      {{mb_label object=$protocole field="chir_id"}}
      <select name="chir_id" class="{{$protocole->_props.chir_id}}" onchange="refreshListProtocolesPrescription($V(this));">
        <option value="">&mdash; Choisir un praticien</option>
        {{foreach from=$listPraticiens item=curr_praticien}}
        <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $chir->user_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien->_view}}
        </option>
        {{/foreach}}
      </select>
      
      {{mb_label object=$protocole field="for_sejour"}}
      {{mb_field object=$protocole field="for_sejour" onchange="setOperationActive(\$V(this.form.elements[this.name]) != 1)"}}
    </td>
  </tr>
  <tr>
    <td id="operation">
      <table class="form">
        <tr>
          <th class="category" colspan="3">
            Informations concernant l'intervention
          </th>
        </tr>
        <tr>
          <th>
            {{mb_label object=$protocole field="_hour_op"}}
          </th>
          <td colspan="2">
            <select name="_hour_op" class="notNull num min|0">
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
            <input type="text" name="_codeCCAM" ondblclick="CCAMSelector.init()" size="10" value="" />
            <button class="tick notext" type="button" onclick="oCcamField.add(this.form._codeCCAM.value,true)">{{tr}}Add{{/tr}}</button>
            
          </td>
          <td class="button"><button class="search" type="button" onclick="CCAMSelector.init()">Choisir un code</button>
          <script type="text/javascript">
            CCAMSelector.init = function(){
              this.sForm  = "editFrm";
              this.sView  = "_codeCCAM";
              this.sChir  = "chir_id";
              this.sClass = "_class_name_";
              this.pop();
            }
          </script>          
          </td>
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
          <td class="text" style="width: 33%;">{{mb_label object=$protocole field="examen"}}</td>
          <td class="text" style="width: 33%;">{{mb_label object=$protocole field="materiel"}}</td>
          <td class="text" style="width: 33%;">{{mb_label object=$protocole field="rques_operation"}}</td>
        </tr>

        <tr>
          <td>{{mb_field object=$protocole field="examen" rows="3"}}</td>
          <td>{{mb_field object=$protocole field="materiel" rows="3"}}</td>
          <td>{{mb_field object=$protocole field="rques_operation" rows="3"}}</td>
        </tr>
  
        <tr>
          <td class="text">{{mb_label object=$protocole field="depassement"}}</td>
          <td class="text">{{mb_label object=$protocole field="forfait"}}</td>
          <td class="text">{{mb_label object=$protocole field="fournitures"}}</td>
        </tr>

        <tr>
          <td>{{mb_field object=$protocole field="depassement" size="4"}}</td>
          <td>{{mb_field object=$protocole field="forfait" size="4"}}</td>
          <td>{{mb_field object=$protocole field="fournitures" size="4"}}</td>
        </tr>
      </table>
    </td>
    
    <td id="sejour">
      <table class="form">
        <tr>
         <th class="category" colspan="3">Informations concernant le séjour</th>
        </tr>
        <tr>
          <th>{{mb_label object=$protocole field="DP"}}</th>
          <td>{{mb_field object=$protocole field="DP" size="10"}}</td>
          <td class="button"><button type="button" class="search" onclick="CIM10Selector.init()">Choisir un code</button>
          <script type="text/javascript">
            CIM10Selector.init = function(){
              this.sForm = "editFrm";
              this.sView = "DP";
              this.sChir = "chir_id";
              this.pop();
            }
          </script>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$protocole field="libelle_sejour"}}</th>
          <td colspan="3">{{mb_field object=$protocole field="libelle_sejour"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$protocole field="duree_hospi"}}</th>
          <td colspan="2">{{mb_field object=$protocole field="duree_hospi" size="2"}} nuits</td>
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
        {{if array_key_exists("dPprescription", $modules)}}
        <tr>
          <td colspan="2">{{mb_label object=$protocole field="protocole_prescription_chir_id"}}</td>
          <td colspan="2"><select name="protocole_prescription_chir_id"></select></td>
        </tr>
        {{/if}}
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center;">
    {{if $protocole->protocole_id}}
      <button class="submit" type="button" onclick="copier()">Dupliquer</button>
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

</form>
