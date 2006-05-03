<table class="main">
  <tr>
    <td>
      <form name="bloc" action="index.php" method="get">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="5" class="category">Moyenne des temps opératoires</th>
        </tr>
        <tr>
          <th>Acte CCAM:</th>
          <td>
            <input type="text" name="codeCCAM" value="{$codeCCAM}" />
            (% pour grouper toutes les interventions)
          </td>
          <th rowspan="2">
            Intervalle:
          </th>
          <td rowspan="2">
            <input type="radio" name="intervalle" value="0" {if $intervalle == 0}checked="checked"{/if} />
            Dernier mois
            <br />
            <input type="radio" name="intervalle" value="1" {if $intervalle == 1}checked="checked"{/if} />
            6 dernier mois
          </td>
          <td rowspan="2">
            <input type="radio" name="intervalle" value="2" {if $intervalle == 2}checked="checked"{/if} />
            Dernière année
            <br />
            <input type="radio" name="intervalle" value="3" {if $intervalle == 3}checked="checked"{/if} />
            Pas d'intervalle
          </td>
        </tr>
        <tr>
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
          <td colspan="5" class="button"><button type="submit">Go</button></td>
        </tr>
      </table>
      </form>
      <table class="tbl">
        <tr>
          <th rowspan="2">Praticien</th>
          <th rowspan="2">CCAM</th>
          <th rowspan="2">Nombre d'interventions</th>
          <th rowspan="2">Estimation de durée</th>
          <th colspan="2">Occupation de salle</th>
          <th colspan="2">Durée d'intervention</th>
        </tr>
        <tr>
          <th>Moyenne</th>
          <th>Ecart-type</th>
          <th>Moyenne</th>
          <th>Ecart-type</th>
        </tr>
        {foreach from=$listOps item=curr_op}
        <tr>
          <td>Dr. {$curr_op.user_last_name} {$curr_op.user_first_name}</td>
          <td>{$curr_op.ccam}</td>
          <td>{$curr_op.total}</td>
          {if $curr_op.estimation > $curr_op.duree_bloc}
          <td style="background-color: #aaf;">
          {elseif $curr_op.estimation < $curr_op.duree_operation}
          <td style="background-color: #faa;">
          {else}
          <td style="background-color: #afa;">
          {/if}
            {$curr_op.estimation|date_format:"%Hh%M"}
          </td>
          <td>{$curr_op.duree_bloc|date_format:"%Hh%M"}</td>
          <td><i>{$curr_op.ecart_bloc|date_format:"%Hh%M"}</i></td>
          <td>{$curr_op.duree_operation|date_format:"%Hh%M"}</td>
          <td><i>{$curr_op.ecart_operation|date_format:"%Hh%M"}</i></td>
        </tr>
        {/foreach}
        {if $total.total}
        <tr>
          <th colspan="2">Total</th>
          <td><strong>{$total.total}</strong></td>
          {if $total.estimation > $total.duree_bloc}
          <td style="background-color: #44f;">
          {elseif $total.estimation < $total.duree_operation}
          <td style="background-color: #f44;">
          {else}
          <td style="background-color: #4f4;">
          {/if}
            <strong>{$total.estimation|date_format:"%Hh%M"}</strong>
          </td>
          <td><strong>{$total.duree_bloc|date_format:"%Hh%M"}</strong></td>
          <td><strong><i>{$total.ecart_bloc|date_format:"%Hh%M"}</i></strong></td>
          <td><strong>{$total.duree_operation|date_format:"%Hh%M"}</strong></td>
          <td><strong><i>{$total.ecart_operation|date_format:"%Hh%M"}</i></strong></td>
        </tr>
        {/if}
      </table>
    </td>
  </tr>
</table>