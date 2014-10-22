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

<tr>
  <td style="height: 75px"></td>
  <td></td>
  <td></td>
  {{foreach from=$dates_plan_soin item=_moments}}
    {{foreach from=$_moments item=_moment name=moment}}
      <td class="{{if $smarty.foreach.moment.first}}left_day{{elseif $smarty.foreach.moment.last}}right_day{{/if}}"></td>
    {{/foreach}}
  {{/foreach}}
</tr>