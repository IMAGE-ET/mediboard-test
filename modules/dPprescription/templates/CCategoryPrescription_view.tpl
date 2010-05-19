{{* $Id: CPrescriptionLineMix_view.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{include file=CMbObject_view.tpl}}

{{if $can->admin}}
<table class="tbl">
	<tr>
		<td class="button">
			<a class="button edit" href="?m=dPprescription&amp;tab=vw_edit_category&amp;category_prescription_id={{$object->_id}}">
        Modifier les éléments
	    </a>
		</td>
	</tr>
</table>
{{/if}}