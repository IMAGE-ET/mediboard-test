{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $mode != "view" || count($object->_ref_notes)}}
<span style="float: {{$float|default:'left'}};"
  onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}', 'objectNotes')"
	{{if $mode == "edit"}}
	  onclick="Note.create('{{$object->_guid}}')"
  {{/if}}
  >
  
  {{if count($object->_ref_notes)}}
    {{if $object->_degree_notes == "high"}}
    <img src="images/icons/note_red.png" width="16" height="16" />
    {{elseif $object->_degree_notes == "medium"}}
    <img src="images/icons/note_orange.png" width="16" height="16" />
    {{elseif $object->_degree_notes == "low"}}
    <img src="images/icons/note_green.png" width="16" height="16" />
  {{/if}}
  {{elseif $mode == "edit"}}
    <img src="images/icons/note_transparent.png" width="16" height="16" />
  {{/if}}
</span>
{{/if}}