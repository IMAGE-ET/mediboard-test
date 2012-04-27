{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl" style="min-width:100px;">
  <tr>
    <th>
      Allergies <small>({{$allergies|@count}})</small>
    </th>
  </tr>
  {{foreach from=$allergies item=_allergie}}
  <tr>
    <td>
      {{if $_allergie->date}}
        {{$_allergie->date|date_format:"%d/%m/%Y"}}:
      {{/if}} 
      {{$_allergie->rques}}
    </td>
  </tr>
  {{/foreach}}
</table>