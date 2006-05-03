{literal}
<script type="text/javascript">
function pageMain() {
  {/literal}
  regRedirectPopupCal("{$date}", "index.php?m={$m}&tab={$tab}&date=");
  {literal}
}
</script>
{/literal}
<table class="main">
  <tr>
    <td>
      <form name="typeVue" action="?m={$m}" method="get">
      <input type="hidden" name="m" value="{$m}" />
      <label for="vue" title="Choisir un type de vue">Type de vue:</label>
      <select name="vue" onchange="submit()">
        <option value="0" {if $vue == 0} selected="selected"{/if}>Tout afficher</option>
        <option value="1" {if $vue == 1} selected="selected"{/if}>Ne pas afficher les validés</option>
      </select>
      </form>
    </td>
    <th>
    {$date|date_format:"%A %d %B %Y"}
    <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
    
    </th>
  <tr>
    <td colspan="3">
      <table width="100%">
        <tr>
          <td>
            <table class="tbl">
              <tr>
                <th class="title" colspan="6">
                  Confirmation des déplacements ({$deplacements|@count})
                </th>
              </tr>
              <tr>
                <th>Confirmation</th>
                <th><a href="index.php?m={$m}&amp;tab={$tab}&amp;typeOrder=0">Patient</a></th>
                <th>Praticien</th>
                <th><a href="index.php?m={$m}&amp;tab={$tab}&amp;typeOrder=1">Origine</a></th>
                <th>Destination</th>
                <th>Heure prévue</th>
              </tr>
              {foreach from=$deplacements item=curr_sortie}
              <tr>
                <td>
                <form name="editFrm{$curr_sortie->affectation_id}" action="?m={$m}" method="post">
                <input type="hidden" name="m" value="{$m}" />
                <input type="hidden" name="del" value="0" />
                <input type="hidden" name="dosql" value="do_affectation_aed" />
                <input type="hidden" name="affectation_id" value="{$curr_sortie->affectation_id}" />
                {if $curr_sortie->confirme}
                <input type="hidden" name="confirme" value="0" />
                <input type="hidden" name="effectue" value="0" />
                <button type="submit">
                <img src="modules/{$m}/images/cross.png" alt="Annuler" title="Annuler le déplacement" />
                Annuler le déplacement
                </button>
                {else}
                <input type="hidden" name="confirme" value="1" />
                <input type="hidden" name="effectue" value="1" />
                <button type="submit">
                <img src="modules/{$m}/images/tick.png" alt="Confirmer" title="Confirmer le déplacement" />
                Confirmer le déplacement
                </button>
                {/if}
                </form>
                </td>
                {if $curr_sortie->confirme}
                <td class="text" style="background-image:url(modules/{$m}/images/ray.gif); background-repeat:repeat;">
                {else}
                <td class="text">
                {/if}
                  <b>{$curr_sortie->_ref_operation->_ref_pat->_view}</b>
                </td>
                <td class="text" style="background:#{$curr_sortie->_ref_operation->_ref_chir->_ref_function->color}">
                  {$curr_sortie->_ref_operation->_ref_chir->_view}
                </td>
                <td class="text">
                  {$curr_sortie->_ref_lit->_ref_chambre->_ref_service->nom} -
                  {$curr_sortie->_ref_lit->_ref_chambre->nom} -
                  {$curr_sortie->_ref_lit->nom}
                </td>
                <td class="text">
                  {$curr_sortie->_ref_next->_ref_lit->_ref_chambre->_ref_service->nom} -
                  {$curr_sortie->_ref_next->_ref_lit->_ref_chambre->nom} -
                  {$curr_sortie->_ref_next->_ref_lit->nom}
                </td>
                <td>{$curr_sortie->sortie|date_format:"%H h %M"}</td>
              </tr>
              {/foreach}
            </table>
          </td>
          <td>
            <table class="tbl">
              <tr>
                <th class="title" colspan="5">
                  Confirmation des sorties ({$sortiesComp|@count} hospis - {$sortiesAmbu|@count} ambus)
                </th>
              </tr>
              <tr>
                <th>Confirmation</th>
                <th><a href="index.php?m={$m}&amp;tab={$tab}&amp;typeOrder=0">Patient</a></th>
                <th>Praticien</th>
                <th><a href="index.php?m={$m}&amp;tab={$tab}&amp;typeOrder=1">Service</a></th>
                <th>Chambre</th>
              </tr>
              <tr><th colspan="5">Hospitalisations complètes</th></tr>
              {foreach from=$sortiesComp item=curr_sortie}
              <tr>
                <td>
                <form name="editFrm{$curr_sortie->affectation_id}" action="?m={$m}" method="post">
                <input type="hidden" name="m" value="{$m}" />
                <input type="hidden" name="del" value="0" />
                <input type="hidden" name="dosql" value="do_affectation_aed" />
                <input type="hidden" name="affectation_id" value="{$curr_sortie->affectation_id}" />
                {if $curr_sortie->confirme}
                <input type="hidden" name="confirme" value="0" />
                <button type="submit">
                <img src="modules/{$m}/images/cross.png" alt="Annuler" title="Annuler la sortie" />
                Annuler la sortie
                </button>
                {else}
                <input type="hidden" name="confirme" value="1" />
                <button type="submit">
                <img src="modules/{$m}/images/tick.png" alt="Confirmer" title="Confirmer la sortie" />
                Confirmer la sortie
                </button>
                {/if}
                </form>
                </td>
                {if $curr_sortie->confirme}
                <td class="text" style="background-image:url(modules/{$m}/images/ray.gif); background-repeat:repeat;">
                {else}
                <td class="text">
                {/if}
                  <b>{$curr_sortie->_ref_operation->_ref_pat->_view}</b>
                </td>
                <td class="text" style="background:#{$curr_sortie->_ref_operation->_ref_chir->_ref_function->color}">
                  {$curr_sortie->_ref_operation->_ref_chir->_view}
                </td>
                <td class="text">
                  {$curr_sortie->_ref_lit->_ref_chambre->_ref_service->nom} -
                  {$curr_sortie->_ref_lit->_ref_chambre->nom} -
                  {$curr_sortie->_ref_lit->nom}
                </td>
                <td>{$curr_sortie->sortie|date_format:"%H h %M"}</td>
              </tr>
              {/foreach}
              <tr><th colspan="5">Ambulatoires</th></tr>
              {foreach from=$sortiesAmbu item=curr_sortie}
              <tr>
                <td>
                <form name="editFrm{$curr_sortie->affectation_id}" action="?m={$m}" method="post">
                <input type="hidden" name="m" value="{$m}" />
                <input type="hidden" name="del" value="0" />
                <input type="hidden" name="dosql" value="do_affectation_aed" />
                <input type="hidden" name="affectation_id" value="{$curr_sortie->affectation_id}" />
                {if $curr_sortie->confirme}
                <input type="hidden" name="confirme" value="0" />
                <button type="submit">
                <img src="modules/{$m}/images/cross.png" alt="Annuler" title="Annuler la sortie" />
                Annuler la sortie
                </button>
                {else}
                <input type="hidden" name="confirme" value="1" />
                <button type="submit">
                <img src="modules/{$m}/images/tick.png" alt="Confirmer" title="Confirmer la sortie" />
                Confirmer la sortie
                </button>
                {/if}
                </form>
                </td>
                {if $curr_sortie->confirme}
                <td class="text" style="background-image:url(modules/{$m}/images/ray.gif); background-repeat:repeat;">
                {else}
                <td class="text">
                {/if}
                  <b>{$curr_sortie->_ref_operation->_ref_pat->_view}</b>
                </td>
                <td class="text" style="background:#{$curr_sortie->_ref_operation->_ref_chir->_ref_function->color}">
                  {$curr_sortie->_ref_operation->_ref_chir->_view}
                </td>
                <td class="text">
                  {$curr_sortie->_ref_lit->_ref_chambre->_ref_service->nom} -
                  {$curr_sortie->_ref_lit->_ref_chambre->nom} -
                  {$curr_sortie->_ref_lit->nom}
                </td>
                <td>{$curr_sortie->sortie|date_format:"%H h %M"}</td>
              </tr>
              {/foreach}
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>