{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=prescription_line_mix_id value=$_prescription_line_mix->_id}}
<tr>
  <!-- Affichage du libelle -->
  <td class="text"  style="border: 1px solid #ccc;">
    {{$_prescription_line_mix->_view}}
	</td>  
  <!-- Affichage des prises -->
  <td class="text" style="border: 1px solid #ccc;">
    <ul>
 	   {{foreach from=$_prescription_line_mix->_ref_lines item=_line}}
 	     <li style="padding: 5px 0px">{{$_line->_ucd_view}} ({{$_line->_posologie}})</li>
 	   {{/foreach}}
 	  </ul>
  </td>
  <!-- Affichage du praticien responsable de la ligne -->
  <td class="text" style="text-align: center">
  {{$_prescription_line_mix->_ref_praticien->_view}}
  </td>
  <!-- Affichage des signatures de la ligne -->
  <td style="border-left: 1px solid #ccc; text-align: center">
    {{if !$_prescription_line_mix->signature_prat && !$_prescription_line_mix->signature_pharma}}
	    DP
    {{else}}
	    {{if !$_prescription_line_mix->signature_prat}}D{{/if}}
	    {{if !$_prescription_line_mix->signature_pharma}}P{{/if}}
    {{/if}}
  </td>
  <!-- Affichage des heures de prises des medicaments -->
  {{foreach from=$tabHours key=_date item=_hours_by_moment}}
    {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
      {{foreach from=$_dates key=_date_reelle item=_hours}}
        {{foreach from=$_hours key=_heure_reelle item=_hour}} 
            {{assign var=_date_hour value="$_date_reelle $_heure_reelle"}}	
					  <td style="padding: 0; width: 0.5cm; border: 1px solid #ccc;
					             text-align: center">
						  {{if $_prescription_line_mix->_debut > $_date_hour || ($_prescription_line_mix->_fin <= $_date_hour)}}
						    <img src="images/icons/gris.gif" />
						  {{else}}
						    {{if isset($_prescription_line_mix->_prises_prevues.$_date.$_hour|smarty:nodefaults)}}
			
								  
							    {{foreach from=$_prescription_line_mix->_ref_lines item=_perf_line name="foreach_perf_line"}}
						<div style="padding: 10px 0px">
								    {{$_perf_line->_quantite_administration}} {{$_perf_line->_unite_administration}}
								 </div>
								 
								 
								   
								  {{/foreach}}
								 
							  {{/if}}
						  {{/if}}
				    </td>
	        {{/foreach}}
	      {{/foreach}}
	    {{/foreach}}
	  {{/foreach}}
</tr>