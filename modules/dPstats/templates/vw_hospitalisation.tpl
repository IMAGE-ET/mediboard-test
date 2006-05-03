<table class="main">
  <tr>
    <td>
      <form name="hospitalisation" action="index.php" method="get">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="4" class="category">Occupation des lits</th>
        </tr>
        <tr>
          <th>Début:</th>
          <td><input type="text" name="debutact" value="{$debutact}" /></td>
          <th>Service:</th>
          <td>
            <select name="service_id">
              <option value="0">&mdash; Tous les services</option>{foreach from=$listServices item=curr_service}
              <option value="{$curr_service->service_id}" {if $curr_service->service_id == $service_id}selected="selected"{/if}>
                {$curr_service->nom}
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
          <th colspan="2"/>
          <th>Type d'hospitalisation:</th>
          <td>
            <select name="type_adm">
              <option value="0">&mdash; Tous les types d'hospi</option>
              <option value="1" {if $type_adm == "1"}selected="selected"{/if}>Hospi complètes + ambu</option>
              {foreach from=$listHospis item=curr_hospi}
              <option value="{$curr_hospi.code}" {if $curr_hospi.code == $type_adm}selected="selected"{/if}>
                {$curr_hospi.code}
              </option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="4" class="button"><button type="submit">Go</button></td>
        </tr>
        <tr>
          <td colspan="4"><i>Note : le nombre d'admissions par type d'hospitalisation avant le 16 novembre 2005 est en dessous de la réalité dû à un mauvais remplissage des dates d'admission par certains cabinets</i></td>
        </tr>
        <tr>
          <td colspan="4" class="button">
            <img src='?m=dPstats&amp;a=graph_patparservice&amp;suppressHeaders=1&amp;debut={$debutact}&amp;fin={$finact}&amp;service_id={$service_id}&amp;prat_id={$prat_id}&amp;type_adm={$type_adm}' />
            <img src='?m=dPstats&amp;a=graph_patpartypehospi&amp;suppressHeaders=1&amp;debut={$debutact}&amp;fin={$finact}&amp;service_id={$service_id}&amp;prat_id={$prat_id}&amp;type_adm={$type_adm}'' />
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>