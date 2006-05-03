{literal}
<script type="text/javascript">

function showCode(form) {
  {/literal}
  {if $selOp->operation_id}
  
  var code = form._selCode.value;
  var aItemsList = new Array();
  aItemsList["0"] = "Sélectionnez un code";
  {foreach from=$selOp->_ext_codes_ccam item=curr_code}
    aItemsList["{$curr_code->code}"] = "{$curr_code->libelleLong|escape:javascript}";
  {/foreach}
  myNode = document.getElementById("codename");
  myNode.innerHTML = aItemsList[code];
  
  {/if}
  {literal}
}

function popCode() {
  var url = new Url();
  url.setModuleAction("dPplanningOp", "code_selector");
  {/literal}
  {if $selOp->operation_id}
  url.addParam("chir", "{$selOp->chir_id}");
  {/if}
  {literal}
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
{/literal}

<form name="manageCodes" action="?m={$m}" method="post" onsubmit="return false;">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  <input type="hidden" name="operation_id" value="{$selOp->operation_id}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="codes_ccam" value="{$selOp->codes_ccam}" />
  <table width="100%">
    <tr>
      <td>
        <select name="_selCode" onchange="showCode(this.form)">
          <option value="0">&mdash; Codes</option>
          {foreach from=$selOp->_codes_ccam item=curr_code}
          <option value="{$curr_code}">{$curr_code}</option>
          {/foreach}
        </select>
      </td>
      <td style="text-align:right">
        Ajouter un code
        <input type="text" size="7" name="_newCode" />
        <button type="button" onclick="addCode()">
          <img src="modules/dPcabinet/images/tick.png">
        </button>
      </td>
    </tr>
    <tr>
      <td>
        <button type="button" onclick="delCode()">
          <img src="modules/dPcabinet/images/cross.png">
        </button>
        <div id="codename" style="display:inline; white-space:normal">Selectionnez un code</div>
      </td>
      <td style="text-align:right">
        <button type="button" onclick="popCode()">Rechercher...</button>
      </td>
    </tr>
  </table>
</form>