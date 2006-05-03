<table class="main">
  <tr>
    <td>
      <form name="bloc" action="index.php" method="get">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="4" class="category">Activité du bloc opératoire</th>
        </tr>
        <tr>
          <th>Début:</th>
          <td><input type="text" name="debutact" value="{$debutact}" /></td>
          <th>Salle:</th>
          <td>
            <select name="salle_id">
              <option value="0">&mdash; Toutes les salles</option>
              {foreach from=$listSalles item=curr_salle}
              <option value="{$curr_salle->id}" {if $curr_salle->id == $salle_id}selected="selected"{/if}>
                {$curr_salle->nom}
              </option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <th>Fin:</th>
          <td><input type="text" name="finact" value="{$finact}" /></td>
          <th>Praticien:</th>
          <td>
            <select name="prat_id">
              <option value="0">&mdash; Tous les praticiens</option>
              {foreach from=$listPrats item=curr_prat}
              <option value="{$curr_prat->user_id}" {if $curr_prat->user_id == $prat_id}selected="selected"{/if}>
                {$curr_prat->_view}
              </option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <th>Acte CCAM:</th>
          <td><input type="text" name="codeCCAM" value="{$codeCCAM}" /></td>
          <td colspan="2" class="button"><button type="submit">Go</button></td>
        </tr>
        <tr>
          <td colspan="4" class="button">
            <img src='?m=dPstats&amp;a=graph_activite&amp;suppressHeaders=1&amp;debut={$debutact}&amp;fin={$finact}&amp;salle_id={$salle_id}&amp;prat_id={$prat_id}&amp;codeCCAM={$codeCCAM}' />
            {if $prat_id}
              <img src='?m=dPstats&amp;a=graph_praticienbloc&amp;suppressHeaders=1&amp;debut={$debutact}&amp;fin={$finact}&amp;salle_id={$salle_id}&amp;prat_id={$prat_id}&amp;codeCCAM={$codeCCAM}' />
            {else}
              <img src='?m=dPstats&amp;a=graph_patjoursalle&amp;suppressHeaders=1&amp;debut={$debutact}&amp;fin={$finact}&amp;salle_id={$salle_id}&amp;prat_id={$prat_id}&amp;codeCCAM={$codeCCAM}' />
            {/if}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>