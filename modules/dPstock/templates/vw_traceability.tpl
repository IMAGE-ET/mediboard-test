{{* $Id$ *}}

{{*  
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

<table class="main tbl">
  <tr>
    <th>{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
  </tr>
  {{foreach from=$list item=curr_deliv}}
  <tr>
    <td>{{mb_value object=$curr_deliv field=date_dispensation}}</td>
  </tr>
  {{/foreach}}
</table>