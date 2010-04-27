{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function () {
  Calendar.regField(getForm("selDate").date, null, {noView: true});
});
	
</script>

<table class="main tbl">
	<tr>
		<th class="title" colspan="4">
			Séjours SSR du {{$date|date_format:$dPconfig.date}}
	    <form name="selDate" action="?" method="get">
	      <input type="hidden" name="m" value="{{$m}}" />
	      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
	    </form>
	  </th>
	</tr>
	<tr>
	  <th>{{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way url="?m=$m&tab=vw_sejours_ssr"}}</th>
    <th>{{mb_colonne class="CSejour" field="praticien_id" order_col=$order_col order_way=$order_way url="?m=$m&tab=vw_sejours_ssr"}}</th>
		<th>{{mb_title class="CSejour" field="entree"}}</th>
		<th>{{mb_title class="CSejour" field="sortie"}}</th>
	</tr>
	{{foreach from=$sejours item=_sejour}}
	<tr>
		<td>
		  <a href="?m={{$m}}&tab=vw_aed_sejour_ssr&amp;sejour_id={{$_sejour->_id}}">
		  	{{mb_value object=$_sejour field="patient_id"}}
		  </a>
		</td>
    <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}</td>
    <td>{{mb_value object=$_sejour field="entree"}}</td>
    <td>{{mb_value object=$_sejour field="sortie"}}</td>
	</tr>
	{{foreachelse}}
	<tr>
		<td colspan="4">
			{{tr}}CSejour.none{{/tr}}
		</td>
	</tr>
	{{/foreach}}
</table>