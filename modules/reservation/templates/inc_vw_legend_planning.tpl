{{*
 * $Id$
 *  
 * @category Reservation
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<style>
  #legend_resa_planning tr td {
    padding: 10px;
  }
</style>

<table id="legend_resa_planning" class="tbl">
  <tr>
    <th class="category" colspan="2">Type de plage</th>
  </tr>
  <tr>
    <td style="background: #{{$conf.dPhospi.colors.comp}}; width:50%;"></td><td>{{tr}}CService.type_sejour.comp{{/tr}}</td>
  </tr>
  <tr>
    <td style="background:#{{$conf.dPhospi.colors.ambu}}"></td><td>{{tr}}CService.type_sejour.ambu{{/tr}}</td>
  </tr>
  <tr>
    <td style="background: #{{$conf.dPhospi.colors.recuse}};border-left:solid 3px #3b5aff !important;"></td><td>{{tr}}CSejour.recuse.-1{{/tr}}</td>
  </tr>
  <tr>
    <td style="background:url('images/icons/ray.gif') #23425D!important;" class="hatching"></td><td>Temps pré/post Operatoire</td>
  </tr>
  <tr>
    <td style="background:#{{$conf.dPhospi.colors.annule}}; opacity:0.6" class="hatching"></td><td>{{tr}}Cancelled{{/tr}}</td>
  </tr>
  <tr>
    <th class="category" colspan="2">En/Hors Plage</th>
  </tr>
  <tr>
    <td style="border-right:dotted 3px red"></td><td>Hors plage</td>
  </tr>
  <tr>
    <td style="border-right:dotted 3px #1dff00"></td><td>En plage</td>
  </tr>
</table>