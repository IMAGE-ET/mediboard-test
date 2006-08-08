<script type="text/javascript">

function showCode(form) {
  {{if $selOp->operation_id}}
  
  var code = form._selCode.value;
  var aItemsList = new Array();
  aItemsList["0"] = "Sélectionnez un code";
  {{foreach from=$selOp->_ext_codes_ccam item=curr_code}}
    aItemsList["{{$curr_code->code}}"] = "{{$curr_code->libelleLong|escape:javascript}}";
  {{/foreach}}
  myNode = document.getElementById("codename");
  myNode.innerHTML = aItemsList[code];
  
  {{/if}}
}

function popCode() {
  var url = new Url();
  url.setModuleAction("dPplanningOp", "code_selector");
  {{if $selOp->operation_id}}
  url.addParam("chir", "{{$selOp->chir_id}}");
  {{/if}}
  url.addParam("type", "ccam");
  url.popup(600, 500, "ccam");
}

function setCode( key, type ) {
  if (key) {
    var oForm = document.manageCodes;
    oForm._newCode.value = key;
  }
}

function addCode() {
  var oForm = document.manageCodes;
  var aCCAM = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCCAM.removeByValue("");
  if(oForm._newCode.value != '')
    aCCAM.push(oForm._newCode.value);
  aCCAM.sort();
  oForm.codes_ccam.value = aCCAM.join("|");
  oForm.submit();
}

function delCode() {
  var oForm = document.manageCodes;
  var aCCAM = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCCAM.removeByValue("");
  if (oForm._selCode.value != '') {
    aCCAM.removeByValue(oForm._selCode.value, true);
  }
  aCCAM.sort();
  oForm.codes_ccam.value = aCCAM.join("|");
  oForm.submit();
}

</script>

<form name="manageCodes" action="?m={{$m}}" method="post" onsubmit="return false;">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  <input type="hidden" name="operation_id" value="{{$selOp->operation_id}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="codes_ccam" value="{{$selOp->codes_ccam}}" />
  <table class="form">
    <tr>
      <td style="vertical-align:middle;">
        <select name="_selCode" onchange="showCode(this.form)">
          <option value="0">&mdash; Codes</option>
          {{foreach from=$selOp->_codes_ccam item=curr_code}}
          <option value="{{$curr_code}}">{{$curr_code}}</option>
          {{/foreach}}
        </select>
      </td>
      <td style="vertical-align:middle; text-align:right;">
        Ajouter un code
        <input type="text" size="7" name="_newCode" />
        <button class="tick" type="button" onclick="addCode()">Ajouter</button>
      </td>
    </tr>
    <tr>
      <td style="vertical-align: middle;">
        <button class="trash" type="button" onclick="delCode()">Supprimer</button>
        <div id="codename" style="vertical-align:middle; display:inline; white-space:normal">Selectionnez un code</div>
      </td>
      <td style="vertical-align:middle; text-align:right;">
        <button class="search" type="button" onclick="popCode()">Rechercher</button>
      </td>
    </tr>
  </table>
</form>