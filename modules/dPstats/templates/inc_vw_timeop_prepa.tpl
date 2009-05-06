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
    <th rowspan="2">Nombre de préparation</th>
    <th rowspan="2">Nombre de plages</th>
    <th colspan="2">Durée des pauses</th>
  </tr>
  <tr>
    <th>Moyenne</th>
    <th>Ecart-type</th>
  </tr>
  {{foreach from=$listTemps item=curr_temps}}
  <tr>
    <td>Dr {{$curr_temps->_ref_praticien->_view}}</td>
    <td>{{$curr_temps->nb_prepa}}</td>
    <td>{{$curr_temps->nb_plages}}</td>
    <td>{{$curr_temps->duree_moy|date_format:"%Mmin %Ss"}}</td>
    <td>{{$curr_temps->duree_ecart|date_format:"%Mmin %Ss"}}</td>
  </tr>
  {{/foreach}}
  <tr>
    <th>Total</th>
    <td>{{$total.nbPrep}}</td>
    <td>{{$total.nbPlages}}</td>
    <td>{{$total.moyenne|date_format:"%Mmin %Ss"}}</td>
    <td>-</td>
  </tr>
  
</table>