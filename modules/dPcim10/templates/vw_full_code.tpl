<table class="fullCode">
  <tr>
    <th colspan="2">
      <form action="index.php" target="_self" name="selectLang" method="get" >
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
      <input type="hidden" name="tab" value="vw_full_code" />
      <input type="hidden" name="code" value="{$cim10->code}" />
      <h1>&ldquo;{$cim10->libelle}&rdquo;</h1>
      </form>
    </th>
  </tr>
  
  <tr>
    <td class="leftPane">
      <form action="index.php" target="_self" name="selection" method="get" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="{$m}" />
      <input type="hidden" name="tab" value="{$tab}" />

      <table class="form">
        <tr>
          <th><label for="code" title="Code total ou partiel de l'acte">Code de l'acte :</label></th>
          <td>
            <input tabindex="1" type="text" title="notNull|code|cim10" name="code" value="{$cim10->code}" />
            <input tabindex="2" type="submit" value="afficher" />
          </td>
        </tr>
      </table>

      </form>
    </td>
     
    {if $canEdit}
    <td class="rightPane">
      <form name="addFavoris" action="?m={$m}" method="post">
      
      <input type="hidden" name="dosql" value="do_favoris_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="favoris_code" value="{$cim10->code}" />
      <input type="hidden" name="favoris_user" value="{$user}" />
      <input class="button" type="submit" name="btnFuseAction" value="Ajouter à mes favoris" />
      
      </form>
    </td>
    {/if}
  </tr>

  {if $cim10->_isInfo}
  <tr>
    <td class="pane" colspan="2">
      <strong>Informations sur ce code:</strong>
      <ul>
        {if $cim10->descr|@count}
        <li>
          Description:
          <ul>
            {foreach from=$cim10->descr item=curr_descr}
            <li>{$curr_descr}</li>
            {/foreach}
          </ul>
        </li>
        {/if}
        {if $cim10->_exclude|@count}
        <li>
          Exclusions:
          <ul>
            {foreach from=$cim10->_exclude item=curr_exclude}
            <li><a href="index.php?m={$m}&amp;t{$tab}&amp;code={$curr_exclude->code}"><strong>{$curr_exclude->code}</strong></a>: {$curr_exclude->libelle}</li>
            {/foreach}
          </ul>
        </li>
        {/if}
        {if $cim10->glossaire|@count}
        <li>
          Glossaire:
          <ul>
            {foreach from=$cim10->glossaire item=curr_glossaire}
            <li>{$curr_glossaire}</li>
            {/foreach}
          </ul>
        </li>
        {/if}
        {if $cim10->include|@count}
        <li>
          Inclusions:
          <ul>
            {foreach from=$cim10->include item=curr_include}
            <li>{$curr_include}</li>
            {/foreach}
          </ul>
        </li>
        {/if}
        {if $cim10->indir|@count}
        <li>
          Exclusions indirectes:
          <ul>
            {foreach from=$cim10->indir item=curr_indir}
            <li>{$curr_indir}</li>
            {/foreach}
          </ul>
        </li>
        {/if}
        {if $cim10->notes|@count}
        <li>
          Notes:
          <ul>
            {foreach from=$cim10->notes item=curr_note}
            <li>{$curr_note}</li>
            {/foreach}
          </ul>
        </li>
        {/if}
      </ul>
    </td>
  </tr>
  {/if}

  <tr>
    {if $cim10->_levelsSup|@count}
    <td class="pane">
      <strong>Codes de niveau supérieur:</strong>
      <ul>
        {foreach from=$cim10->_levelsSup item=curr_level}
        {if $curr_level->sid != 0}
        <li><a href="index.php?m={$m}&amp;tab={$tab}&amp;code={$curr_level->code}"><strong>{$curr_level->code}</strong></a>: {$curr_level->libelle}</li>
        {/if}
        {/foreach}
      </ul>
    </td>
    {/if}
    {if $cim10->_levelsInf|@count}
    <td class="pane">
      <strong>Codes de niveau inferieur :</strong>
      <ul>
        {foreach from=$cim10->_levelsInf item=curr_level}
        {if $curr_level->sid != 0}
        <li><a href="index.php?m={$m}&amp;tab={$tab}&amp;code={$curr_level->code}"><strong>{$curr_level->code}</strong></a>: {$curr_level->libelle}</li>
        {/if}
        {/foreach}
      </ul>
    </td>
    {/if}
  </tr>
</table>