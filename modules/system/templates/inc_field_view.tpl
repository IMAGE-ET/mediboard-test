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
    {{if $prop == $object->_spec->key}}
      {{$object->$prop}}
    {{else}}
  	  {{assign var=ref value=$object->_fwd.$prop}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$ref->_guid}}');">
        {{$ref}}
      </span>
    {{/if}}

  {{elseif $spec instanceof CHtmlSpec}}
    {{$value|count_words}} mots

  {{elseif $spec instanceof CTextSpec}}
    {{* FIXME: smarty:nodefault is required because HTML entities are double escaped *}}
	  {{$value|smarty:nodefaults|truncate:40}}
		
  {{else}}
    {{mb_value object=$object field=$prop}}

  {{/if}}
	
  <br />
{{/if}}
{{/if}}
{{/if}}
