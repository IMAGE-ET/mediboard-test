{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  {{foreach from=$matches item=match}}
    <li id="match-{{$match->_id}}">
      <strong class="view">{{$match->_view|emphasize:$keywords}}</strong><br />
      <small>
        {{if $match->code}}
          {{$match->code|emphasize:$keywords}} - 
        {{/if}}
        {{$match->description|@truncate:40|emphasize:$keywords}}
      </small>
    </li>
  {{/foreach}}
</ul>