{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	
Main.add(function(){
	var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
	url.addParam("patient_id", {{$operation->_ref_sejour->patient_id}});
	url.addParam("context_guid", "{{$operation->_ref_sejour->_guid}}");
	url.addParam("selection[]", ["pouls", "ta", "frequence_respiratoire", "score_sedation", "spo2"]);
	url.addParam("date_min", "{{$operation->_datetime_reel}}");
	url.addParam("date_max", "{{$operation->_datetime_reel_fin}}");
	url.addParam("print", 1);
	url.requestUpdate("constantes");
});
	
</script>

<table class="tbl">
	<tr>
	  <th>Fiche d'intervention anesthésie</th>
	</tr>
	{{foreach from=$perops item=_perop}}
	<tr>
	  <td>
	    {{$_perop->_view}}
		</td>
	</tr>
	{{/foreach}}
</table>

<div id="constantes"></div>