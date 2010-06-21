{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  {{foreach from=$matches item=match}}
    <li id="match-{{$match->_id}}" class="lotnumber|{{$lot_number}}">
      <strong>{{$match->_view|emphasize:$keywords}}</strong><br />
      <small>
        <span style="float: right; color: #666;">
          {{$match->_available_quantity}} dispo
        </span>
        
        {{if $match->code}}
          {{$match->code|emphasize:$keywords}} - 
        {{/if}}
        {{$match->description|@truncate:40|emphasize:$keywords}}
      </small>
      <span style="display: none;" class="view">
        {{if $match->code}}[{{$match->code}}] {{/if}}{{$match->_view}}
      </span>
    </li>
  {{foreachelse}}
    <li></li>
  {{/foreach}}
</ul>