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
              <option value="0">
                Moyenne des temps opératoires
              </option>
              <option value="1"{{if $typeVue == 1}} selected="selected"{{/if}}>
                Moyenne des temps de préparation
              </option>
              <option value="2"{{if $typeVue == 2}} selected="selected"{{/if}}>
                Moyenne des temps d'hospitalisation
              </option>
            </select>
          </th>
        </tr>

        {{if $typeVue == 0 || $typeVue == 2}}
        <tr>
          <th><label for="codeCCAM" title="Acte CCAM">Acte CCAM</label></th>
          <td>
            <input type="text" name="codeCCAM" value="{{$codeCCAM|stripslashes}}" />
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
        {{if $typeVue == 2}}
        <tr>
          <th><label for="type" title="Type d'hospitalisation">Type</label></th>
          <td>
            <select name="type">
              <option value="">
                &mdash; Tous les types
              </option>
              <option value="ambu" {{if $type == "ambu"}}selected="selected"{{/if}}>
                {{tr}}CSejour.type.ambu{{/tr}}
              </option>
              <option value="comp" {{if $type == "comp"}}selected="selected"{{/if}}>
                {{tr}}CSejour.type.comp{{/tr}}
              </option>
            </select>
          </td>
        </tr>
        {{/if}}
        <tr>
          <td colspan="2" class="button"><button type="submit" class="search">Afficher</button></td>
        </tr>
      </table>
      </form>
      {{if $typeVue == 0}}
        {{include file="inc_vw_timeop_op.tpl"}}
      {{elseif $typeVue == 1}}
        {{include file="inc_vw_timeop_prepa.tpl"}}
      {{else}}
        {{include file="inc_vw_timehospi.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>