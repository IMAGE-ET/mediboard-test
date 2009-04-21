{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
	<tr>
		<th class="title" colspan="3">Etablissement</th>
	</tr>
	<tr>
		<th>{{mb_label object=$etablissement field="group_id"}}</th>
		<th>{{mb_label object=$etablissement field="increment_year"}}</th>
		<th>{{mb_label object=$etablissement field="increment_patient"}}</th>
	</tr>
	{{foreach from=$etablissements item=_item}}
		<tr {{if $_item->_id == $etablissement->_id}}class="selected"{{/if}}>
			<td>
				<a href="?m={{$m}}&amp;tab=view_etablissements&amp;sp_etab_id={{$_item->sp_etab_id}}&amp;" title="Modifier l'element">
					{{$_item->_ref_group->_view}}
				</a>	
			</td>
			<td>{{mb_value object=$_item field="increment_year"}}</td>
			<td>{{mb_value object=$_item field="increment_patient"}}</td>
		</tr>
	{{/foreach}}
</table>