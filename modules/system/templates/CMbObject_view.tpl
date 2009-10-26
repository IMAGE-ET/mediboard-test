{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th>{{$object}}</th>
  </tr>
  <tr>
    <td>
      {{foreach from=$object->_specs key=name item=spec}}
        {{if $name.0 != "_" || @$show_derived}}
        {{if $name != "_view" && $name != "_shortview" || @$show_views}}
        {{if $object->$name != "" || @$show_empty}}
          <strong>{{mb_label object=$object field=$name}}</strong> :
          {{if $spec instanceof CRefSpec}}
          	<span onmouseover="ObjectTooltip.createEx(this,'{{$spec->class}}-{{$object->$name}}');">
	          {{mb_value object=$object field=$name}}
	          </span>
	        {{else}}
	        	{{if $spec instanceof CHtmlSpec}}
	        	  {{$object->$name|count_words}} mots
	          {{else}}
	          {{mb_value object=$object field=$name}}
	          {{/if}}
          {{/if}}
          <br />
        {{/if}}
        {{/if}}
        {{/if}}
      {{/foreach}}
    </td>
  </tr>
</table>