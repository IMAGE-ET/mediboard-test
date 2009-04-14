{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<span class="tooltip-trigger" style="float:left;"
  onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}', 'objectNotes')"
  onclick="new Note().create('{{$object->_class_name}}', '{{$object->_id}}');">
  {{if $notes|@count}}
  {{if $high}}
  <img alt="Ecrire une note" src="images/icons/note_red.png" />
  {{else}}
  <img alt="Ecrire une note" src="images/icons/note_green.png" />
  {{/if}}
  {{else}}
  <img alt="Ecrire une note" src="images/icons/note_blue.png" />
  {{/if}}
</span>