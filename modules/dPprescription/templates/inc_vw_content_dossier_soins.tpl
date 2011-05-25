{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	
Main.add(function(){
 PlanSoins.togglePeriodNavigation();
});

</script>

<table class="tbl" id="plan_soin">
	<tbody id="tbody_date">
	{{if $prescription->_ref_lines_med_for_plan|@count || $prescription->_ref_lines_elt_for_plan|@count || 
	     $prescription->_ref_prescription_line_mixes_for_plan_by_type|@count || $prescription->_ref_injections_for_plan|@count || $conf.dPprescription.CPrescription.show_inscription}}
	  <tr>
	  	{{if $conf.dPprescription.CPrescription.show_categories_plan_soins}}
	    <th rowspan="2" class="title">Catégorie</th>
      {{/if}}
	    <th rowspan="2" class="title">Libellé</th>
	    <th rowspan="2" class="title">Posologie</th>
			
      {{foreach from=$count_composition_dossier key=_date item=_hours_by_moment}}
        {{foreach from=$_hours_by_moment key=moment_journee item=_count}}
				  {{if $composition_dossier|@count == 1}}
					  {{assign var=view_poste value="Journée"}}
					{{else}}
            {{assign var=tab_poste value='-'|explode:$moment_journee}}
            {{assign var=num_poste value=$tab_poste|@end}}
            {{assign var=libelle_poste value="Libelle poste $num_poste"}}
            {{assign var=view_poste value=$configs.$libelle_poste}}
					{{/if}}
	        <th class="{{$_date}}-{{$moment_journee}} title" colspan="{{$_count}}">

	          <a href="#1" onclick="PlanSoins.showBefore()" class="prevPeriod" style="float: left" onmousedown="periodicalBefore = new PeriodicalExecuter(PlanSoins.showBefore, 0.2);" onmouseup="periodicalBefore.stop();">
	            <img src="images/icons/prev.png" alt="&lt;"/>
	          </a>        
	          <a href="#1" onclick="PlanSoins.showAfter()" class="nextPeriod" style="float: right" onmousedown="periodicalAfter = new PeriodicalExecuter(PlanSoins.showAfter, 0.2);" onmouseup="periodicalAfter.stop();">
	            <img src="images/icons/next.png" alt="&gt;" />
	          </a>     
	          <strong>
	          
						{{assign var=key_borne value="$_date-$moment_journee"}}	
						{{assign var=bornes_poste value=$bornes_composition_dossier.$key_borne}}

	            <a href="#1" onclick="PlanSoins.selColonne('{{$_date}}-{{$moment_journee}}')">
	              {{$view_poste}} du 
								{{if $bornes_poste.min|iso_date != $bornes_poste.max|iso_date}}
								  {{$bornes_poste.min|date_format:"%d/%m"}} au {{$bornes_poste.max|date_format:"%d/%m"}}
								{{else}}
								  {{$_date|date_format:"%d/%m"}}
								{{/if}}
	            </a>
	          </strong>
	        </th>
	      {{/foreach}} 
	    {{/foreach}}
	    <th colspan="2" class="title">Sign.</th>
	  </tr>
	  <tr>
	    <th></th>
			{{if $conf.dPprescription.CPrescription.manual_planif}}
			  <th>x</th>
			{{/if}}
	    {{foreach from=$tabHours key=_date item=_hours_by_moment}}
	      {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
	        {{foreach from=$_dates key=_date_reelle item=_hours}}
	          {{foreach from=$_hours key=_heure_reelle item=_hour}}
	            <th class="{{$_date}}-{{$moment_journee}}" 
	                style='width: 50px; text-align: center; 
	              {{if array_key_exists("$_date_reelle $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}
								{{if $now|date_format:"%Y-%m-%d %H" == "$_date_reelle $_hour"}}background-color: #fff;{{/if}}'>
	              <a href="#1" onclick="PlanSoins.selColonne('{{$_date_reelle}}-{{$_hour}}');">{{$_hour}}h</a>
	              {{if array_key_exists("$_date $_hour:00:00", $operations)}}
	                {{assign var=_hour_op value="$_date $_hour:00:00"}}
	                <a style="color: white; font-weight: bold; font-style: normal;" href="#" title="Intervention à {{$operations.$_hour_op|date_format:'%Hh%M'}}">Interv.</a>
	              {{/if}}
	            </th>   
	          {{/foreach}}
	        {{/foreach}}
	      {{/foreach}}
	    {{/foreach}}
	    <th></th>
	    <th>Dr</th>
	    <th>Ph</th>
	  </tr>
	{{/if}}
	</tbody>
	
	<!-- Affichage des prescription_line_mixes -->
	<tbody id="_aerosol" style="display:none;"></tbody>
	<tbody id="_alimentation" style="display:none;"></tbody>
	<tbody id="_oxygene" style="display:none;"></tbody>
	<tbody id="_perfusion" style="display:none;"></tbody>
	<!-- Affichage des injectables -->
	<tbody id="_inj" style="display: none;"></tbody>
	<!-- Affichage des medicaments -->
	<tbody id="_med" style="display: none;"></tbody>      
	<!-- Affichage des elements -->
	{{foreach from=$prescription->_ref_lines_elt_for_plan key=name_chap item=elements_chap name="foreach_element"}}
	  {{if !$smarty.foreach.foreach_element.first}}
	    </tbody>
	  {{/if}}
	  <tbody id="_cat-{{$name_chap}}" style="display: none;">
	{{/foreach}}
	</tbody>
	{{if $conf.dPprescription.CPrescription.show_inscription}}
		<!-- Affichage des inscriptions du personnel soignant -->
		<tbody id="_inscription" style="display: none;"></tbody>
	{{/if}}
</table>