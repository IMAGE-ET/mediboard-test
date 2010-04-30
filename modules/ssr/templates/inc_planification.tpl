{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=ssr script=planification}}

<table class="main">
  <col style="width: 50%;" />
  
	<tr>
		<td style="padding: 3px;" id="planning-sejour"></td>
    <td style="padding: 3px;" id="activites-sejour"></td>
	</tr>
	
  <tr>
  	<td style="padding: 3px;">
		<div style="position: relative;">
			<div style="position: absolute; top: 0px; right: 0px;">
		    <button type="button" class="change notext" onclick="PlanningTechnicien.toggle();"/>
			</div>
			<div id="planning-technicien"></div>
	</div>
		</td>
    <td style="padding: 3px;" id="planning-equipement"></td>
  </tr>
</table>
