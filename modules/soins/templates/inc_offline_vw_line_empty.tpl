{{*
 * $Id$
 *  
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<tbody class="line_print">
  <tr>
    <td style="height: 75px"></td>
    <td></td>
    <td></td>
    {{if $mode_dupa || $mode_lite}}
      <td></td>
    {{/if}}

    {{foreach from=$dates_plan_soin item=_moments}}
      {{foreach from=$_moments item=_moment name=moment}}
        <td class="{{if $smarty.foreach.moment.first}}left_day{{elseif $smarty.foreach.moment.last}}right_day{{/if}}"></td>
      {{/foreach}}
    {{/foreach}}
  </tr>
</tbody>