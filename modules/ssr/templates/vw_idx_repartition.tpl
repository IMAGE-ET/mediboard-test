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
	
	registerPlateau: function (plateau_id) {
	  Main.add(Repartition.updatePlateau.curry(plateau_id));
	},
	
  updateSejours: function() {
    Console.trace('Updating séjours');
    return;
    new Url('ssr', 'ajax_sejours_non_repartis') .
      addParam('date', date) . 
      requestUpdate('sejours_non_repartis');
  },

  registerSejours: function () {
    Main.add(Repartition.updatePlateau);
  }
}
</script>

<table class="main">
	<tr>
		<td>

      {{foreach from=$plateaux item=_plateau}}
			<table class="tbl">
			  <tr>
			    <td id="repartition-plateau-{{$_plateau->_id}}" style="width: 25%;">
			      <script type="text/javascript">Repartition.registerPlateau('{{$_plateau->_id}}')</script>
						<div class="small-info">{{$_plateau}}</div>
			    </td>
			  </tr>
			</table>			
      {{/foreach}}
			
		</td>
	
    <td id="sejours_non_repartis" style="width: 250px;">
      <script type="text/javascript">Repartition.registerSejours()</script>
      <div class="small-info">Séjours non répartis</div>
    </td>
  </tr>
</table>
