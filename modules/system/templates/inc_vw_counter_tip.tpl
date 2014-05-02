{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=warning value=0}}

{{if $app->user_prefs.showCounterTip && $count && ($count > 1)}}
  {{if $warning}}
    <span class="countertip" style="color: red;">
      <strong>{{$count}}</strong>
    </span>
  {{else}}
    <span class="countertip">
      {{$count}}
    </span>
  {{/if}}
{{/if}}