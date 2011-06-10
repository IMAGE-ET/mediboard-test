{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6330 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=float value=left}}
{{if is_array($object->_ref_notes)}}
  <div style="float: {{$float}}" class="noteDiv {{$object->_guid}} initialized">
  {{mb_include module=system template=inc_get_notes_image mode=edit float=left object=$object}}
  {{mb_include module=system template=inc_vw_counter_tip count=$object->_ref_notes|@count}}
  </div>
{{else}}
  <div style="float: {{$float}};" class="noteDiv {{$object->_guid}}">
    <img title="{{tr}}CNote-title-create{{/tr}}" src="images/icons/note_grey.png" width="16" height="16" />
  </div>
{{/if}}
