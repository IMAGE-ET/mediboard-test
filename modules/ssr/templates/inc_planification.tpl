{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
PlanningEquipement = {
  show: function(id) {
	  new Url("ssr", "ajax_planning_equipement") .
	    addParam("equipement_d", id) .
	    requestUpdate("planning-equipement");
  },
	hide: function() {
	  $("planning-equipement").update("");
	}
}	
	
PlanningTechnicien = {
  show: function(id) {
	  new Url("ssr", "ajax_planning_technicien") .
	    addParam("technicien_id", id || "11") .
	    requestUpdate("planning-technicien");
  },
  hide: function() {
    $("planning-technicien").update("");
  }
} 

Main.add(function() {
  new Url("ssr", "ajax_planning_patient") .
	  addParam("patient_id", "{{$sejour->patient_id}}") .
	  requestUpdate("planning-patient");

  new Url("ssr", "ajax_planner_sejour") .
    addParam("sejour_id", "{{$sejour->_id}}") .
    requestUpdate("planner-sejour");
})

</script>

<table class="main">
	<tr>
		<td style="width: 50%; height: 240px; padding: 0 20px;" id="planning-patient"></td>
    <td style="width: 50%; height: 240px; padding: 0 20px;" id="planner-sejour"   ></td>
	</tr>
	<tr><td colspan="10"><hr/></td></tr>
  <tr>
  	<td style="width: 50%; height: 240px; padding: 0 20px;" id="planning-technicien"></td>
    <td style="width: 50%; height: 240px; padding: 0 20px;" id="planning-equipement"></td>
  </tr>
  
</table>
