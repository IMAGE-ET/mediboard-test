{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  {{if $element_prescription->_back.cdarrs|@count}}
    <tr>
      <th colspan="2">Liste des codes Cdarr</th>
    </tr>
    <tr>
      <th>{{mb_label class=CElementPrescriptionToCdarr field=code}}</th>
      <th>{{mb_label class=CElementPrescriptionToCdarr field=commentaire}}</th>
    </tr>
    {{foreach from=$element_prescription->_back.cdarrs item=_element_to_cdarr}}
      <tr>
        <td>
           <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_to_cdarr_id={{$_element_to_cdarr->_id}}">
            {{mb_value object=$_element_to_cdarr field=code}}
           </a>
        </td>
        <td>{{mb_value object=$_element_to_cdarr field=commentaire}}</td>
      </tr>
    {{/foreach}}
  {{/if}}
</table>
