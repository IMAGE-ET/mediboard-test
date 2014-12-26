{{*
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<h2>Exploration de données</h2>

<script>
  Main.add(function() {
    Calendar.regField(getForm('getDate').date);
  });
</script>

<form name="getDate" method="get">
  <input type="text" name="date" value="{{$dnow}}" style="display: none;"/>
</form>

<p>
<button onclick="Datamining.mine('CDailySalleOccupation', 'mine', $V(getForm('getDate').date))" class="change">Mine</button>
<button onclick="Datamining.mine('CDailySalleOccupation', 'remine', $V(getForm('getDate').date))" class="change">Remine</button>
<button onclick="Datamining.mine('CDailySalleOccupation', 'postmine', $V(getForm('getDate').date))" class="change">Postmine</button>
</p>