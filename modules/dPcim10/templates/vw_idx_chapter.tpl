<table width="100%" bgcolor="#cccccc">
  <tr>
    <th align="center">
      <form action="index.php" target="_self" name="selection" method="get" >
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
      <input type="hidden" name="m" value="dPcim10" />
      <input type="hidden" name="tab" value="vw_idx_chapter" />
      <input type="hidden" name="code" value="$cim10->code" />
      <h1>Liste des chapitres de la CIM10</h1>
      </form>
    </th>
  </tr>
  <tr>
    <td valign="top" align="center">
      <table width="750" bgcolor="#dddddd">
        {foreach from=$chapter item=curr_chapter}
        <tr>
          <td valign="top" align="right">
            <b>{$curr_chapter.rom}</b>
          </td>
          <td valign="top" align="left">
            <a href="index.php?m={$m}&amp;tab=vw_full_code&amp;code={$curr_chapter.code}"><b>{$curr_chapter.text}</b></a>
          </td>
        </tr>
        {/foreach}
      </table>
    </td>
  </tr>
</table>