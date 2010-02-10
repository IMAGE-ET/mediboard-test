{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Repartition = {
  date: null,
  updatePlateau: function(plateau_id) {
	  Console.debug(plateau_id, 'Updating plateau');
		return;
	  new Url('ssr', 'ajax_repartition_plateau') .
      addParam('plateau_id', plateau_id) . 
      addParam('date', date) . 
			requestUpdate('repartition-plateau-'+plateau_id);
	},
	updateSejours: function() {
    Console.debug(plateau_id, 'Updating séjours');
    return;
    new Url('ssr', 'ajax_sejours_non_repartis') .
      addParam('date', date) . 
      requestUpdate('sejours_non_repartis');
	}
}
</script>

<table class="main">
	<tr>
		<td style="width: 100%;">
		  {{foreach $plateaux as $_plateau}}
			<script type="text/javascript">Main.add(Repartition.updatePlateau.curry('{{$_plateau->_id}}')</script>
			<div id="repartition-plateau-{{$_plateau->_id}}">
      <h1>{{$_plateau}}</h1>
			</div>
			{{/foreach}}
		</td>

    <td id="sejours_non_repartis" style="width: 250px;">
		</td>
  </tr>
</table>
