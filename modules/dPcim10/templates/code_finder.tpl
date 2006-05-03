{literal}
<script type="text/javascript">
function setClose(code) {
  window.opener.putCim10(code);
  //window.close();
}
</script>
{/literal}

<table class="tbl">
  {if $up}
  <tr>
    <th class="category" colspan="2">
      <a href="index.php?m=dPcim10&amp;a=code_finder&amp;dialog=1&amp;code={$up->code}">
        <img src="modules/dPcim10/images/uparrow.png" />
        {$up->code}: {$up->libelle}
        <img src="modules/dPcim10/images/uparrow.png" />
      </a>
    </th>
  <tr>
  {/if}
  <tr>
    <th class="category" colspan="2">
      {if !$cim10->_levelsInf|@count}
        <button type="button" onclick="setClose('{$cim10->code}')">
          <img src="modules/dPcim10/images/tick.png" />
        </button>
      {/if}
      <strong>{$cim10->code}: {$cim10->libelle}</strong>
    </th>
  <tr>
  </tr>
    <td class="text" style="vertical-align:top;">
      <strong>Informations sur ce code:</strong>
      {if $cim10->_isInfo}
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
      {else}
      <ul>
        <li>Pas d'information disponible</li>
      {/if}
    </td>
    <td class="text" style="vertical-align:top;">
      {if $cim10->_levelsInf|@count or $cim10->_exclude|@count}
      <strong>Codes associés:</strong>
      <ul>
      {if $cim10->_levelsInf|@count}
      <li>Précisions:
        <ul>
          {foreach from=$cim10->_levelsInf item=curr_code}
          <li>
            <button type="button" onclick="setClose('{$curr_code->code}')">
              <img src="modules/dPcim10/images/tick.png" />
            </button>
            <a class="action" href="index.php?m=dPcim10&amp;a=code_finder&amp;dialog=1&amp;code={$curr_code->code}">
              <button type="button">
                <img src="modules/dPcim10/images/downarrow.png" />
              </button>
              {$curr_code->code}: {$curr_code->libelle}
            </a>
          </li>
          {/foreach}
        </ul>
      </li>
      {/if}
      {if $cim10->_exclude|@count}
      <li>Exclusions:
        <ul>
          {foreach from=$cim10->_exclude item=curr_code}
          <li>
            <button type="button" onclick="setClose('{$curr_code->code}')">
              <img src="modules/dPcim10/images/tick.png" />
            </button>
            <a class="action" href="index.php?m=dPcim10&amp;a=code_finder&amp;dialog=1&amp;code={$curr_code->code}">
              <button type="button">
                <img src="modules/dPcim10/images/downarrow.png" />
              </button>
              {$curr_code->code}: {$curr_code->libelle}
            </a>
          </li>
          {/foreach}
        </ul>
      </li>
      {/if}
      </ul>
      {else}
      <strong>Pas de codes associés</strong>
      {/if}
    </td>
  </tr>
</table>