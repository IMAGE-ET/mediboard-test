{{* $Id: CMbObject_view.tpl 7128 2009-10-26 17:25:30Z rhum1 $ *}}

{{*
  * @package Mediboard
  * @subpackage system
  * @version $Revision: 7128 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{assign var=spec value=$object->_specs.$prop}}
{{assign var=value value=$object->$prop}}

{{if $spec->show !== "0"}} 
{{if $prop.0 != "_" || $spec->show}}
{{if $value || $spec->show}}
  <strong>{{mb_label object=$object field=$prop}}</strong> :

  {{if $spec instanceof CRefSpec}}
    <span onmouseover="ObjectTooltip.createEx(this,'{{$spec->class}}-{{$value}}');">
    {{mb_value object=$object field=$prop}}
    </span>

  {{elseif $spec instanceof CHtmlSpec}}
    {{$value|count_words}} mots

  {{elseif $spec instanceof CTextSpec}}
    {{$value|truncate|nl2br}}
	
  {{else}}
    {{mb_value object=$object field=$prop}}

  {{/if}}
	
  <br />
{{/if}}
{{/if}}
{{/if}}
