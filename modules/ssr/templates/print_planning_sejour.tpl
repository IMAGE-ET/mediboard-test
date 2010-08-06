{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=ssr script=planning}}
{{mb_include_script module=ssr script=planification}}

<script type="text/javascript">
Main.add(function(){
  Planification.refreshSejour("{{$sejour->_id}}", false, 800, true, true);
});

</script>

<table class="tbl">
	<tr>
		<th class="title" colspan="2">Kiné Référent</th>
	</tr>
	<tr>
		<td colspan="2">
			{{$sejour->_ref_bilan_ssr->_ref_technicien->_ref_kine->_view}}
		</td>
	</tr>	
	<tr>
		<th class="title" colspan="2">Intervenants</th>
	</tr>
	{{foreach from=$intervenants item=_intervenant_by_elt key=element_id}}
	  {{assign var=element value=$elements.$element_id}}
		<tr>
	  	<th>{{$element->_view}}</th>
		 <td>
		{{foreach from=$_intervenant_by_elt item=_intervenant name="intervenants"}}
		   {{$_intervenant->_view}}
		   {{if !$smarty.foreach.intervenants.last}},{{/if}}
		{{/foreach}}
		</td>
	 </tr> 
	{{/foreach}}
</table>

<div id="planning-sejour"></div>
