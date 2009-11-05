{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $view_field == 1}}
  {{assign var=f value=$field}}
{{else}}
  {{assign var=f value=$view_field}}
{{/if}}
<ul>
{{foreach from=$matches item=match}}
  {{* Do not add carriage returns or it will add whitespace in the input *}}
  <li id="{{$match->_id}}">{{if $show_view}}{{$match->_view}}{{else}}{{$match->$f|emphasize:$input}}{{/if}}</li>
{{foreachelse}}
  <li><span class="informal">Aucun résultat</span></li>
{{/foreach}}
</ul>