{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=perfusion_id value=$_perfusion->_id}}
<tr>
  <!-- Affichage du libelle -->
  <td class="text"  style="border: 1px solid #ccc;">
    {{$_perfusion->_view}}
	</td>  
  <!-- Affichage des prises -->
  <td class="text" style="border: 1px solid #ccc;">
    <ul>
 	   {{foreach from=$_perfusion->_ref_lines item=_line}}
 	     <li>{{$_line->_view}}</li>
 	   {{/foreach}}
 	  </ul>
  </td>
  <!-- Affichage du praticien responsable de la ligne -->
  <td class="text" style="text-align: center">
  {{$_perfusion->_ref_praticien->_view}}
  </td>
  <!-- Affichage des signatures de la ligne -->
  <td style="border-left: 1px solid #ccc; text-align: center">
    {{if !$_perfusion->signature_prat && !$_perfusion->signature_pharma}}
	    DP
    {{else}}
	    {{if !$_perfusion->signature_prat}}D{{/if}}
	    {{if !$_perfusion->signature_pharma}}P{{/if}}
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
						  {{if $_perfusion->_debut > $_date_hour || ($_perfusion->_fin <= $_date_hour)}}
						    <img src="images/icons/gris.gif" />
						  {{/if}}
				    </td>
	        {{/foreach}}
	      {{/foreach}}
	    {{/foreach}}
	  {{/foreach}}
</tr>