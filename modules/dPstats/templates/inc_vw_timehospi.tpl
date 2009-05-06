{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th rowspan="2">Praticien</th>
    <th rowspan="2">Type hospi</th>
    <th rowspan="2">CCAM</th>
    <th rowspan="2">Nombre d'interventions</th>
    <th colspan="2">Durée d'hospitalisation</th>
  </tr>
  <tr>
    <th>Moyenne</th>
    <th>Ecart-type</th>
  </tr>
  {{foreach from=$listTemps item=curr_temps}}
  <tr>
    <td>Dr {{$curr_temps->_ref_praticien->_view}}</td>
    <td>{{tr}}CSejour.type.{{$curr_temps->type}}{{/tr}}</td>
    <td>{{$curr_temps->ccam}}</td>
    <td>{{$curr_temps->nb_sejour}}</td>
    <td>{{$curr_temps->duree_moy|string_format:"%.2f"}} jours</td>
    <td><i>{{if $curr_temps->duree_ecart}}{{$curr_temps->duree_ecart|string_format:"%.2f"}} jours{{else}}-{{/if}}</i></td>
  </tr>
  {{/foreach}}
  
  <tr>
    <th colspan="3">Total</th>
    <td>{{$total.nbSejours}}</td>
    <td>{{$total.duree_moy|string_format:"%.2f"}} jours</td>
    <td>-</td>
  </tr>
</table>