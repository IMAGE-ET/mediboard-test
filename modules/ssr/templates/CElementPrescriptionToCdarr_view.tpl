{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{include file=CMbObject_view.tpl}}

{{assign var=element_prescription_to_cdarr value=$object}}

<table class="tooltip tbl">
  <tr>
    <td>
      <strong>Libelle:</strong>
      {{$element_prescription_to_cdarr->_ref_activite_cdarr->libelle}}
    </td>
  </tr>
	<tr>
		<td>
			<strong>Type:</strong>
      {{$element_prescription_to_cdarr->_ref_activite_cdarr->_ref_type_activite->_view}}
		</td>
	</tr>
</table>
