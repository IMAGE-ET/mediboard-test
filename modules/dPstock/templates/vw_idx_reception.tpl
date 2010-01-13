{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7769 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	
function printReception(reception_id, width, height) {
  width = width || 1000;
  height = height || 800;

  var url = new Url("dPstock", "print_reception");
  url.addParam("reception_id", reception_id);
  url.popup(width, height, "Bon de reception");
}

</script>

<table class="main tbl">
	<tr>
		<th colspan="5">Réceptions <small>({{$receptions|@count}})</small></th>
	</tr>
	<tr>
    <th style="width: 1%">{{mb_title class=CProductReception field="reference"}}</th>
    <th>{{mb_title class=CProductReception field="date"}}</th>
		<th>{{mb_title class=CProductReception field="societe_id"}}</th>
		<th>Nombre d'elements</th>
		<th></th>
  </tr>
	{{foreach from=$receptions item=_reception}}
	<tr>
    <td>{{mb_value object=$_reception field="reference"}}</td>
	  <td>{{mb_value object=$_reception field="date"}}</td>
    <td>{{mb_value object=$_reception field="societe_id"}}</td>
		<td>{{$_reception->_count_reception_items}}</td>
		<td style="width: 1%"><button type="button" class="print" onclick="printReception('{{$_reception->_id}}');">Bon de réception</button></td>
	</tr>
	{{/foreach}}
</table>