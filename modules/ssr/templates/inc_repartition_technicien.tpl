{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">

  <tr>
    <th id="technicien-{{$technicien_id}}">
      <script type="text/javascript">
        Repartition.registerTechnicien('{{$technicien_id}}')
			</script>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_technicien->_fwd.kine_id}}
		</th>
	</tr>
	
	{{assign var=conge value=$_technicien->_ref_conge_date}}
	{{if $conge->_id}} 
  <tr>
    <td class="ssr-kine-conges">
    	<strong onmouseover="ObjectTooltip.createEx(this, '{{$conge->_guid}}')">
    		{{$conge}}
    	</strong>
    </td>
  </tr>
	{{/if}}
	
	<tbody id="sejours-technicien-{{$technicien_id}}">

	</tbody>

</table>
