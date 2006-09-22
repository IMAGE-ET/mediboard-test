<script>

function popCode(type) {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "code_selector");
  url.addParam("type", type);
  url.popup(600, 500, type);
}

function setCode(code, type) {
  var oForm = document.bloc;
  var oField = oForm.codeCCAM;
  oField.value = code;
}

</script>

<table class="main">
  <tr>
    <td>
      <form name="bloc" action="index.php" method="get">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="2" class="category">
            <select name="typeVue" onChange="this.form.submit();">
              <option value="0">Moyenne des temps opératoires</option>
              <option value="1"{{if $typeVue}} selected="selected"{{/if}}>Moyenne des temps de préparation</option>
            </select>
          </th>
        </tr>

        {{if !$typeVue}}
        <tr>
          <th><label for="codeCCAM" title="Acte CCAM">Acte CCAM</label></th>
          <td>
            <input type="text" name="codeCCAM" value="{{$codeCCAM}}" />
            <button type="button" class="search" onclick="popCode('ccam')">Sélectionner un code</button>
          </td>
        </tr>
        <tr>
          <th><label for="prat_id" title="Praticien">Praticien</label></th>
          <td>
            <select name="prat_id">
              <option value="0">&mdash; Tous les praticiens</option>
              {{foreach from=$listPrats item=curr_prat}}
              <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $prat_id}}selected="selected"{{/if}}>
                {{$curr_prat->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{/if}}
        <tr>
          <td colspan="2" class="button"><button type="submit" class="search">Afficher</button></td>
        </tr>
      </table>
      </form>
      {{if $typeVue}}
        {{include file="inc_vw_timeop_prepa.tpl"}}
      {{else}}
        {{include file="inc_vw_timeop_op.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>