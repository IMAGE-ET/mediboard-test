{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $line->_can_view_form_ide}}
  <input name="ide_domicile" type="checkbox" {{if $line->ide_domicile}}checked="checked"{{/if}} onchange="submitIDE('{{$line->_id}}',this.checked)"  />
  {{mb_label object=$line field="ide_domicile"}}
{{elseif !$line->_protocole}}
  {{mb_label object=$line field="ide_domicile"}}:
  {{if $line->ide_domicile}}Oui{{else}}Non{{/if}} 
{{/if}}
