{literal}
<script type="text/javascript">
function checkForm() {
  var form = document.editFrm;
  if (form.text.value.length == 0) {
    alert("Intitulé manquant");
    form.text.focus();
    return false;
  }  
  return true;
}

function popColor() {
  var url = new Url;
  url.setModuleAction("mediusers", "color_selector");
  url.addParam("callback", "setColor");
  url.popup(320, 250, "color");
}

function setColor(color) {
  var f = document.editFrm;
  if (color) {
    f.color.value = color;
  }
  document.getElementById('test').style.background = '#' + f.color.value;
}
</script>
{/literal}

<table class="main">
  <tr>
    <td class="halfPane">
      <a href="index.php?m={$m}&amp;tab={$tab}&amp;userfunction=0"><strong>Créer une fonction</strong></a>
      <table class="tbl">
      {foreach from=$listGroups item=curr_group}
        <tr><th>Groupe {$curr_group->text} &mdash; {$curr_group->_ref_functions|@count} fonction(s)</th><th>Utilisateurs</th></tr>
        {foreach from=$curr_group->_ref_functions item=curr_function}
        <tr>
          <td style="background: #fff"><a href="index.php?m={$m}&amp;tab={$tab}&amp;function_id={$curr_function->function_id}">{$curr_function->text}</a></td>
          <td style="background: #{$curr_function->color}"><a href="index.php?m={$m}&amp;tab={$tab}&amp;function_id={$curr_function->function_id}">{$curr_function->_ref_users|@count}</a></td></tr>
        {/foreach}
      {/foreach}
      </table>
    </td>
    <td class="halfPane">
    <form name="editFrm" action="./index.php?m={$m}" method="post" onSubmit="return checkForm()">
      <input type="hidden" name="dosql" value="do_functions_aed" />
      <input type="hidden" name="function_id" value="{$userfunction->function_id}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
          {if $userfunction->function_id}
            <a style="float:right;" href="javascript:view_log('CFunctions',{$userfunction->function_id})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Modification de la fonction &lsquo;{$userfunction->text}&rsquo;
          {else}
            {tr}Création d'une fonction{/tr}
          {/if}
          </th>
        </tr>
        <tr>
          <th class="mandatory"><label for="text" title="Intitulé de la fonction. Obligatoire">Intitulé</label></th>
          <td><input type="text" name="text" size="30" value="{$userfunction->text}" /></td>
        </tr>
        <tr>
          <th class="mandatory"><label for="group_id" title="Groupe auquel se rattache la fonction">Groupe</label></th>
          <td>
            <select name="group_id">
            {foreach from=$listGroups item=curr_group}
              <option value="{$curr_group->group_id}" {if $curr_group->group_id == $userfunction->group_id} selected="selected" {/if}>
              {$curr_group->text}
              </option>
            {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="color" title="Couleur de visualisation des fonctions dans les plannings">Couleur</label></th>
          <td>
            <span id="test" title="test" style="background: #{$userfunction->color};">
            <a href="javascript:popColor()">cliquez ici</a>
            </span>
            <input type="hidden" name="color" value="{$userfunction->color}" />
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
          {if $userfunction->function_id}
            <input type="reset" value="Réinitialiser" />
            <input type="submit" value="Valider" />
            <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{ldelim}typeName:'la fonction',objName:'{$userfunction->text|escape:javascript}'{rdelim})"/>
          {else}
            <input type="submit" name="btnFuseAction" value="Créer" />
          {/if}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>