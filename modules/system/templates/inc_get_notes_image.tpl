{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<span class="tooltip-trigger" style="float: {{$float|default:'left'}};"
  onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}', 'objectNotes')"
	{{if $mode == "edit"}}
	  onclick="new Note('{{$object->_guid}}')"
  {{/if}}
  >
  
  {{if count($object->_ref_notes)}}
	  {{if $object->_high_notes}}
	  <img src="images/icons/note_red.png" width="16" height="16" />
	  {{else}}
	  <img src="images/icons/note_green.png" width="16" height="16" />
	  {{/if}}
  {{elseif $mode == "edit"}}
    <img src="images/icons/note_blue.png" width="16" height="16" />
  {{/if}}
</span>