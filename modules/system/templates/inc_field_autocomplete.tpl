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
  <li id="autocomplete-{{$match->_guid}}">
  {{if $template}}
    {{include file=$template nodebug=true}}
  {{else}}
    {{* Do not add carriage returns or it will add whitespace in the input *}}
    <span class="view">{{if $show_view}}{{$match->_view}}{{else}}{{$match->$f|emphasize:$input}}{{/if}}</span>
  {{/if}}
  </li>
{{foreachelse}}
  <li>
    <span class="view"></span>
    <span style="font-style: italic;">{{tr}}No result{{/tr}}</span>
  </li>
{{/foreach}}
</ul>