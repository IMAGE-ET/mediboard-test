{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add( function(){
  prepareForm("editPerf{{$perfusion->_id}}");
} );

</script>
{{assign var=perfusion_id value=$perfusion->_id}}
<form name="editPerf{{$perfusion->_id}}" method="post" action="?">
  <input type="hidden" name="dosql" value="do_perfusion_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="perfusion_id" value="{{$perfusion_id}}" />
	<table class="form">
	  <tr>
	    <th class="category text">
	    <ul>
	    {{foreach from=$perfusion->_ref_lines item=_perf_line name="foreach_perf"}}
	      <li>{{$_perf_line->_view}}</li>
	    {{/foreach}}
	    </ul>
	    
	    {{$perfusion->_view}}
	    </th>
	  </tr>
	  <tr>
	    <td class="date">
			  {{if $perfusion->date_pose}}
			    {{mb_label object=$perfusion field=date_pose}}
			    {{mb_field object=$perfusion field=date_pose form="editPerf$perfusion_id" onchange="submitTiming();" register=true canNull=false}}
			    {{mb_field object=$perfusion field=time_pose form="editPerf$perfusion_id" onchange="submitTiming();" register=true canNull=false}}
			    <button type="button" class="notext cancel" onclick="this.form.date_pose.value = ''; this.form.time_pose.value = ''; submitTiming();"></button>
			  {{else}}
			    <input type="hidden" name="date_pose" value="" />
			    <input type="hidden" name="time_pose" value="" />
			    <button type="button" class="submit" onclick="this.form.date_pose.value = 'current'; this.form.time_pose.value = 'current'; submitTiming();">{{tr}}CPerfusion-date_pose{{/tr}}</button>
			  {{/if}}
			</td>
	  </tr>
	  <tr>
	    <td class="date">
			  {{if $perfusion->date_retrait}}
			    {{mb_label object=$perfusion field=date_retrait}}
			    {{mb_field object=$perfusion field=date_retrait form="editPerf$perfusion_id" onchange="submitTiming();" register=true canNull=false}}
			    {{mb_field object=$perfusion field=time_retrait form="editPerf$perfusion_id" onchange="submitTiming();" register=true canNull=false}}
			    <button type="button" class="notext cancel" onclick="this.form.date_retrait.value = ''; this.form.time_retrait.value = ''; submitTiming();"></button>
			  {{else}}
			    <input type="hidden" name="date_retrait" value="" />
			    <input type="hidden" name="time_retrait" value="" />
			    <button type="button" class="submit" onclick="this.form.date_retrait.value = 'current'; this.form.time_retrait.value = 'current'; submitTiming();">{{tr}}CPerfusion-date_retrait{{/tr}}</button>
			  {{/if}}
			</td>
	  </tr>
	</table>
</form>