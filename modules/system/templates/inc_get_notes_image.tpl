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
	  onclick="new Note().create('{{$object->_class_name}}', '{{$object->_id}}');"
  {{/if}}
  >
  
  {{if count($object->_ref_notes)}}
	  {{if $object->_high_notes}}
	  <img alt="Ecrire une note" src="images/icons/note_red.png" />
	  {{else}}
	  <img alt="Ecrire une note" src="images/icons/note_green.png" />
	  {{/if}}
  {{elseif $mode == "edit"}}
  <img alt="Ecrire une note" src="images/icons/note_blue.png" />
  {{/if}}
</span>