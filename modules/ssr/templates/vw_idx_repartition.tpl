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
    Console.debug('Updating séjours');
    return;
    new Url('ssr', 'ajax_sejours_non_repartis') .
      addParam('date', date) . 
      requestUpdate('sejours_non_repartis');
  }
}
</script>

<table class="tbl">
  <tr>
    {{foreach from=$plateaux item=_plateau}}
    <td id="repartition-plateau-{{$_plateau->_id}}" style="width: 25%;">
      <script type="text/javascript">Main.add(Repartition.updatePlateau.curry('{{$_plateau->_id}}'))</script>
    </td>
    {{/foreach}}

    <td id="sejours_non_repartis" style="width: 25%;">
      <script type="text/javascript">Main.add(Repartition.updateSejours)</script>
    </td>
  </tr>
</table>
