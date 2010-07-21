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
    <th id="technicien-" class="title text">
      <script type="text/javascript">
        Repartition.registerTechnicien('','{{$readonly}}');
      </script>
      Séjours non-répartis
      <small class="count">(-)</small>			
		</th>
  </tr>
	
  <tbody id="sejours-technicien-">
  </tbody>

	
</table>
