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
  <li id="autocomplete-{{$match->_guid}}" data-id="{{$match->_id}}" data-guid="{{$match->_guid}}">
  {{if $template}}
    {{include file=$template nodebug=true}}
  {{else}}
    {{mb_include module=system template=CMbObject_autocomplete nodebug=true}}
  {{/if}}
  </li>
{{foreachelse}}
  <li>
    <span class="view"></span>
    <span style="font-style: italic;">{{tr}}No result{{/tr}}</span>
  </li>
{{/foreach}}
</ul>