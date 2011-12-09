{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $prescription->type != "sejour"}}
	{{if $line->_can_view_form_ald}}
	  <input name="ald" type="checkbox" {{if $line->ald}}checked="checked"{{/if}} onchange="submitALD('{{$line->_class}}','{{$line->_id}}',this.checked)"  />
	  {{mb_label object=$line field="ald"}}
	{{elseif !$line->_protocole && $line->ald}}
	  <strong>{{mb_label object=$line field="ald"}}</strong>
	{{/if}}
{{/if}}