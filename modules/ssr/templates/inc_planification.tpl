{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=ssr script=planification}}

<script type="text/javascript">

onCompleteShowWeek = function(){
  Planification.refreshSejour();
  PlanningTechnicien.show();
  PlanningEquipement.show();
  Planification.refreshActivites();
}

Main.add(Planification.showWeek);

printPlanningSejour = function(){
  var url = new Url("ssr", "print_planning_sejour");
  url.addParam("sejour_id", "{{$sejour->_id}}");
  url.popup("700","700","Planning du patient");
}


</script>

<table class="main">
  <col style="width: 50%;" />
  
  <tr>
    <td id="week-changer" colspan="2"></td>
  </tr>

	<tr>
		<td>
			<div style="position: relative;">
			  <div style="position: absolute; top: 0px; right: 0px;">
          <button type="button" class="print notext" onclick="printPlanningSejour();"/>
        </div>
				<div id="planning-sejour"></div>
			</div>
		</td>
    <td id="activites-sejour"></td>
	</tr>
	
  <tr>
  	<td>
			<div style="position: relative;">
				<div style="position: absolute; top: 0px; right: 0px;">
			    <button type="button" class="change notext" onclick="PlanningTechnicien.toggle();"/>
				</div>
				<div id="planning-technicien"></div>
		  </div>
		</td>
    <td id="planning-equipement"></td>
  </tr>
</table>
