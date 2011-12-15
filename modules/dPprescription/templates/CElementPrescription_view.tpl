{{* $Id: CPrescriptionLineMix_view.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{include file=CMbObject_view.tpl}}

{{if $object->_ref_constantes_items|@count}}
<table class="tbl">
  <tr>
    <td>
    	<strong>Constantes:</strong>
      {{foreach from=$object->_ref_constantes_items item=_constante name="constantes"}}
			  {{$_constante}}{{if !$smarty.foreach.constantes.last}},{{/if}}
			{{/foreach}}
    </td>
  </tr>
</table>
{{/if}}