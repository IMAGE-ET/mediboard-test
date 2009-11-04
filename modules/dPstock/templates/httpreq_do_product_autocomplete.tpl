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
      <small>{{$match->code}} - {{$match->name}}  {{$match->description|@truncate:25}}</small>
    </li>
  {{/foreach}}
</ul>