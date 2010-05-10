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
    <th id="kine-{{$kine_id}}">
      <script type="text/javascript">
        Repartition.registerKine('{{$kine_id}}')
			</script>
    	{{$_technicien}}
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
	
	<tbody id="sejours-kine-{{$kine_id}}">

	</tbody>

</table>
