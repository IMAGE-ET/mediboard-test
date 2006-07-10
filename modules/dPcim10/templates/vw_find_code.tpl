<form action="index.php" target="_self" name="selectLang" method="get" >

<input type="hidden" name="m" value="dPcim10" />
<input type="hidden" name="tab" value="vw_find_code" />
<input type="hidden" name="keys" value="{$keys}" />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      <select name="lang" style="float:right;" onchange="this.form.submit()">
        <option value="{$smarty.const.LANG_FR}" {if $lang == $smarty.const.LANG_FR}selected="selected"{/if}>
          Français
        </option>
        <option value="{$smarty.const.LANG_EN}" {if $lang == $smarty.const.LANG_EN}selected="selected"{/if}>
          English
        </option>
        <option value="{$smarty.const.LANG_DE}" {if $lang == $smarty.const.LANG_DE}selected="selected"{/if}>
          Deutsch
        </option>
      </select>
      Critères de recherche
    </th>
  </tr>
</table>

</form>

<form action="index.php" name="selection" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{$m}" />
<input type="hidden" name="tab" value="vw_find_code" />

<table class="form">
  <tr>
    <th><label for="keys" title="Un ou plusieurs mots clés, séparés par des espaces. Obligatoire">Mots clefs</label></th>
    <td><input type="text" title="str" name="keys" value="{$keys}" /></td>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button class="search" type="submit">Rechercher</button>
    </td>
  </tr>
</table>

</form>

<table class="findCode">

  <tr>
    <th colspan="4">
      {if $numresults == 100}
      Plus de {$numresults} résultats trouvés, seuls les 100 premiers sont affichés:
      {else}
      {$numresults} résultats trouvés:
      {/if}
    </th>
  </tr>

  {foreach from=$master item=curr_master key=curr_key}
  {if $curr_key is div by 4}
  <tr>
  {/if}
    <td>
      <strong>
        <a href="index.php?m={$m}&amp;tab=vw_full_code&amp;code={$curr_master.code}">{$curr_master.code}</a>
      </strong>
      <br />{$curr_master.text}
    </td>
  {if ($curr_key+1) is div by 4 or ($curr_key+1) == $master|@count}
  </tr>
  {/if}
  {/foreach}

</table>