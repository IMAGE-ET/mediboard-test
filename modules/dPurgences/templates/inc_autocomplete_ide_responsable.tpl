{{*
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<ul>
  {{foreach from=$matches item=match}}
    <li id="match-{{$match->_id}}" data-id="{{$match->_id}}" data-name="{{$match->_view}}">
      <strong class="view">{{$match->_view|emphasize:$keywords}}</strong><br />
    </li>
    {{foreachelse}}
    <li style="text-align: left;"><span class="informal">{{tr}}No result{{/tr}}</span></li>
  {{/foreach}}
</ul>