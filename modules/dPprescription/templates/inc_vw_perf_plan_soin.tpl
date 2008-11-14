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
  {{foreach from=$dates item=date}}
		{{foreach from=$tabHours.$date key=_real_hour item=_hour name="foreach_date"}}
		  <td style="padding: 0; width: 0.5cm; border: 1px solid #ccc;
		             {{if $smarty.foreach.foreach_date.first}}border-left: 1px solid black;{{/if}}
		             {{if $smarty.foreach.foreach_date.last}}border-right: 1px solid black;{{/if}}text-align: center">
			  {{if $_perfusion->_debut > $_real_hour || ($_perfusion->_fin < $_real_hour)}}
			    <img src="images/icons/gris.gif" />
			  {{/if}}
	  </td>
		{{/foreach}}
  {{/foreach}}
</tr>