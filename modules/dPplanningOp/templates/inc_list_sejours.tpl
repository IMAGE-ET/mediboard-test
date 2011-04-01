{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table style="width: 100%; border-collapse:collapse;">
  {{if $patient->_ref_sejours}}
    {{foreach from=$patient->_ref_sejours item=object}}
      {{mb_include module=dPpatients template=inc_vw_elem_dossier}}
    {{/foreach}}
  {{else}}
    <tr>
      <td class="empty">
        {{tr}}CSejour.none{{/tr}}
      </td>
    </tr>
  {{/if}}
</table>
