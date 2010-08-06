{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<script type="text/javascript">
Main.add(function() {
  var sejours_count = {{$sejours_count|json}};
  var link = $('tabs-replacement').down('a[href=#{{$type}}s]');
	link.down('small').update('('+sejours_count+')');
	link.setClassName('wrong', sejours_count != 0);
})
</script>

<table class="tbl">
  {{foreach from=$sejours key=plage_conge_id item=_sejours}}
	  <tr>
	    <th colspan="5" class="title text">
	    	{{assign var=plage_conge value=$plages_conge.$plage_conge_id}}
			  Séjours pendant les congés de 
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$plage_conge->_ref_user}}
			</th>
		</tr>
		<tr>
	    <th colspan="2">{{mb_title class="CSejour" field="patient_id"}}</th>
	    <th>{{mb_title class="CSejour" field="entree"}}</th>
	    <th>{{mb_title class="CSejour" field="sortie"}}</th>
			<th>Evts SSR</th>
    </tr>
	  {{foreach from=$_sejours item=_sejour}}
		  {{assign var=sejour_id value=$_sejour->_id}}
      {{assign var=key value="$plage_conge_id-$sejour_id"}}
			
      <tr id="replacement-{{$type}}-{{$_sejour->_id}}">
		    <td colspan="2" class="text {{if $_sejour->_ref_replacement->_id && $type == "kine"}} arretee {{/if}}">
					{{assign var=patient value=$_sejour->_ref_patient}}
				  <big class="CPatient-view" 
					  onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}};')" 
					  onclick="refreshReplacement('{{$_sejour->_id}}','{{$plage_conge_id}}','{{$type}}'); this.up('tr').addUniqueClassName('selected');" >
						{{$patient}}
					</big> 
				  <br />
					{{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP}}
          {{$patient->_age}} ans
		    </td>
		    <td style="text-align: center;">
		      {{mb_value object=$_sejour field=entree format=$dPconfig.date}}
		      <div style="text-align: left; opacity: 0.6;">{{$_sejour->_entree_relative}}j</div>
		    </td>
		    <td style="text-align: center;">
		      {{mb_value object=$_sejour field=sortie format=$dPconfig.date}}
		      <div style="text-align: right; opacity: 0.6;">{{$_sejour->_sortie_relative}}j</div>
		    </td>
				<td style="text-align: center;">
					{{assign var=sejour_id value=$_sejour->_id}}
					{{assign var=key value="$plage_conge_id-$sejour_id"}}
					{{$count_evts.$key}}
				</td>
		  </tr>
			
    {{/foreach}}
	{{foreachelse}}
	<tr>
	  <th class="title">
	  	Séjours
	  </th>
	</tr>
	<tr>
    <td colspan="10">
      <em>{{tr}}CSejour.none{{/tr}}</em>
    </td>
	</tr>
	{{/foreach}}
</table>