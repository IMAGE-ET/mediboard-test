{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Affichage des allergies -->
{{if array_key_exists('alle', $antecedents)}}
  {{assign var=allergies value=$antecedents.alle}}
  {{if $allergies|@count}}
   <img src="images/icons/warning.png" title="" alt="Allergies" onmouseover="ObjectTooltip.createDOM(this, 'allergies{{$sejour_id}}')" />
 
   <div id="allergies{{$sejour_id}}" style="text-align:left; display: none;">
     <strong>Allergies</strong>
     <ul>
      {{foreach from=$allergies item=allergie}}
      <li>
      {{if $allergie->date}}
 	      {{$allergie->date|date_format:"%d/%m/%Y"}}:
	    {{/if}} 
		  	{{$allergie->rques}}
	    </li>
	    {{/foreach}}
   </ul>   
   </div>   
  {{/if}}
{{/if}}

<!-- Affichage des autres antecedents -->
 {{if $dossier_medical->_count_antecedents}}
  <img src="images/icons/antecedents.gif" title="" alt="Antécédents" onmouseover="ObjectTooltip.createDOM(this, 'antecedents{{$sejour_id}}')" />
  
   <div id="antecedents{{$sejour_id}}" style="text-align:left;  display: none;">
     <ul>
      {{foreach from=$antecedents key=name item=cat}}
      {{if $name != "alle" && $cat|@count}}
      <li>
      <strong>{{tr}}CAntecedent.type.{{$name}}{{/tr}}</strong>
      <ul>
      {{foreach from=$cat item=ant}}
      <li>
      {{if $ant->date}}
 	      {{$ant->date|date_format:"%d/%m/%Y"}}:
	    {{/if}} 
		  	{{$ant->rques}}
	    </li>
	    {{/foreach}}
	    </ul>
	    </li>
	    {{/if}}
	    {{/foreach}}
   </ul>   
   </div>  
 {{/if}}
 
