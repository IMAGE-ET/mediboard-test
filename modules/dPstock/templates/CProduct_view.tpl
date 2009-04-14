{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl tooltip">
	<tr>
		<th colspan="10">Produit {{$object->code}}</th>
	</tr>
	<tr>
		<td>
			<strong>{{mb_title object=$object field=name}}</strong> : {{mb_value object=$object field=name}}<br />
			<strong>{{mb_title object=$object field=description}}</strong> : {{mb_value object=$object field=description}}<br />
			<strong>{{mb_title object=$object field=societe_id}}</strong> : {{mb_value object=$object field=societe_id}}<br />
			<strong>{{mb_title object=$object field=category_id}}</strong> : {{mb_value object=$object field=category_id}}<br />
			<strong>{{mb_title object=$object field=renewable}}</strong> : {{mb_value object=$object field=renewable}}<br />
			<strong>{{mb_title object=$object field=_unique_usage}}</strong> : {{mb_value object=$object field=_unique_usage}}
		</td>
	</tr>
</table>
                  