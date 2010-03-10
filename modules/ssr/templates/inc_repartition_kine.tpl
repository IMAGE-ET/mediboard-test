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
	
	<tbody id="sejours-kine-{{$kine_id}}">

	</tbody>

</table>
