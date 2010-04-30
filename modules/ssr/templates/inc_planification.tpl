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
Main.add(Planification.showWeek);
</script>

<table class="main">
  <col style="width: 50%;" />
  
  <tr>
    <td id="week-changer" colspan="2"></td>
  </tr>

	<tr>
		<td id="planning-sejour"></td>
    <td id="activites-sejour"></td>
	</tr>
	
  <tr>
  	<td style="padding: 2px;">
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
