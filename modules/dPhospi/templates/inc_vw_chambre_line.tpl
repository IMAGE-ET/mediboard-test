{{*
 * $Id$
 *  
 * @category Hospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<tr>
  <td class="narrow" style="text-align:center">
    {{if $_chambre->rank}}
      <div class="rank">
        {{mb_value object=$_chambre field=rank}}
      </div>
    {{/if}}
  </td>
  <td class="narrow" style="text-align:center">
    <a href="#" onclick="Infrastructure.addeditChambre('{{$_chambre->_id}}', 'services', {{$_service->_id}})">
      {{mb_value object=$_chambre field=nom}}
    </a>
  </td>

  {{if $_chambre->annule}}
    <td class="cancelled">
      {{mb_title object=$_chambre field=annule}}
    </td>
  {{else}}
    <td class="narrow compact" style="text-align:center">
      {{foreach from=$_chambre->_ref_lits item=_lit}}
        <span {{if $_lit->annule}}class="cancelled"{{/if}}>
          {{mb_value object=$_lit field=nom}} -
          {{if $_lit->nom_complet}}
            {{mb_value object=$_lit field=nom_complet}}
          {{/if}}
          {{if $_lit->rank}}
            ({{mb_value object=$_lit field=rank}})
          {{/if}}
        </span>
        <br/>
      {{/foreach}}
    </td>
  {{/if}}
  <td class="compact" style="text-align:center">
    {{mb_value object=$_chambre field=caracteristiques}}
  </td>
  <td class="narrow compact" style="text-align:center">
    {{mb_value object=$_chambre field=lits_alpha}}
  </td>
  <td class="narrow compact" style="text-align:center">
    {{mb_value object=$_chambre field=is_waiting_room}}
  </td>
  <td class="narrow compact" style="text-align:center">
    {{mb_value object=$_chambre field=is_examination_room}}
  </td>
  <td class="narrow compact" style="text-align:center">
    {{mb_value object=$_chambre field=is_sas_dechoc}}
  </td>
</tr>