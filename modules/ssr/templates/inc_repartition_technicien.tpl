{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl ssr-technicien">
	  
  {{assign var=conge value=$_technicien->_ref_conge_date}}
  <tr {{if $conge->_id}} class="ssr-kine-conges" {{/if}}>
    <th class="text" id="technicien-{{$technicien_id}}">
      <script type="text/javascript">
        Repartition.registerTechnicien('{{$technicien_id}}','{{$readonly}}')
			</script>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_technicien->_fwd.kine_id}}
      <small class="count">(-)</small>
 		</th>
	</tr>
	{{if $conge->_id}} 
  <tr class="ssr-kine-conges">
    <td>
    	<strong onmouseover="ObjectTooltip.createEx(this, '{{$conge->_guid}}')">
    		{{$conge}}
    	</strong>
    </td>
  </tr>
	{{/if}}
	
	<tbody id="sejours-technicien-{{$technicien_id}}">

	</tbody>

</table>
