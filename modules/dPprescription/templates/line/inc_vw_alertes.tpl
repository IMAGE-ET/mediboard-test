{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=allergie value=$prescription->_alertes.allergie}}
{{assign var=IPC value=$prescription->_alertes.IPC}}
{{assign var=interaction value=$prescription->_alertes.interaction}}
{{assign var=profil value=$prescription->_alertes.profil}}
{{if @array_key_exists('posoduree', $prescription->_alertes)}}{{assign var=posoduree value=$prescription->_alertes.posoduree}}{{/if}}
{{if @array_key_exists('posoqte', $prescription->_alertes)}}{{assign var=posoqte value=$prescription->_alertes.posoqte}}{{/if}}
{{assign var=code_cip value=$line->code_cip}}

{{assign var=image value=""}}
{{assign var=color value=""}}
  
{{if (@array_key_exists($code_cip, $allergie) || @array_key_exists($code_cip, $interaction) ||
      @array_key_exists($code_cip, $profil)   || @array_key_exists($code_cip, $IPC) ||
      @array_key_exists($code_cip, $posoduree)|| @array_key_exists($code_cip, $posoqte) )}}   
     
  {{assign var=puce_orange value=false}}
  {{assign var=puce_rouge value=false}}
  
  <!-- Allergie -->
  {{if array_key_exists($code_cip, $allergie)}}
    <!-- Alerte faible -->
    {{if $conf.dPprescription.CPrescription.scores.allergie == '1'}}
      {{assign var=puce_orange value=true}}
      
    {{/if}}
    <!-- Alerte importante -->
    {{if $conf.dPprescription.CPrescription.scores.allergie == '2'}}
      {{assign var=puce_rouge value=true}}
    {{/if}}
  {{/if}}
  
  <!-- IPC -->
  {{if array_key_exists($code_cip, $IPC)}}
    <!-- Alerte faible -->
    {{if $conf.dPprescription.CPrescription.scores.IPC == '1'}}
      {{assign var=puce_orange value=true}}
    {{/if}}
    <!-- Alerte importante -->
    {{if $conf.dPprescription.CPrescription.scores.IPC == '2'}}
      {{assign var=puce_rouge value=true}}
    {{/if}}
  {{/if}}
  
  <!-- Interactions -->
  {{if array_key_exists($code_cip, $interaction)}}
	  {{foreach from=$interaction.$code_cip item=_interaction}}
	    {{assign var=_niveau value=$_interaction.niveau}}
	    {{assign var=niveau value=niv$_niveau}}
	    {{if $conf.dPprescription.CPrescription.scores.interaction.$niveau == '1'}}
	      {{assign var=puce_orange value=true}}
	    {{/if}}
	    {{if $conf.dPprescription.CPrescription.scores.interaction.$niveau == '2'}}
	      {{assign var=puce_rouge value=true}}
	    {{/if}}
	  {{/foreach}}
  {{/if}}
  
  <!-- Profil -->
  {{if array_key_exists($code_cip, $profil)}}
	  {{foreach from=$profil.$code_cip item=_profil}}
	    {{assign var=_niveau value=$_profil.niveau}}
	    {{assign var=niveau value=niv$_niveau}}
	     
	    {{if $conf.dPprescription.CPrescription.scores.profil.$niveau == '1'}}
	      {{assign var=puce_orange value=true}}
	    {{/if}}
	    {{if $conf.dPprescription.CPrescription.scores.profil.$niveau == '2'}}
	      {{assign var=puce_rouge value=true}}
	    {{/if}}
	  {{/foreach}}
  {{/if}}
  
  <!-- Posologie -->
  {{if @array_key_exists($code_cip, $posoduree)}}
	  {{foreach from=$posoduree.$code_cip item=_posoduree}}
	    {{assign var=_niveau value=$_posoduree.niveau}}
	    {{assign var=niveau value=niv$_niveau}}
	     
	    {{if $conf.dPprescription.CPrescription.scores.posoduree.$niveau == '1'}}
	      {{assign var=puce_orange value=true}}
	    {{/if}}
	    {{if $conf.dPprescription.CPrescription.scores.posoduree.$niveau == '2'}}
	      {{assign var=puce_rouge value=true}}
	    {{/if}}
	  {{/foreach}}
  {{/if}}
  {{if @array_key_exists($code_cip, $posoqte)}}
	  {{foreach from=$posoqte.$code_cip item=_posoqte}}
	    {{assign var=_niveau value=$_posoqte.niveau}}
	    {{assign var=niveau value=niv$_niveau}}
	     
	    {{if $conf.dPprescription.CPrescription.scores.posoqte.$niveau == '1'}}
	      {{assign var=puce_orange value=true}}
	    {{/if}}
	    {{if $conf.dPprescription.CPrescription.scores.posoqte.$niveau == '2'}}
	      {{assign var=puce_rouge value=true}}
	    {{/if}}
	  {{/foreach}}
  {{/if}}

  <!-- Sélection de la puce à afficher -->
  {{if $puce_rouge}}
    {{assign var="image" value="note_red.png"}}
    {{assign var="color" value=#ff7474}}
				
		<script type="text/javascript">
			{{if $mode_pharma}}
				Main.add( function(){
				  {{if $line instanceof CPrescriptionLineMixItem}}
				    window.alertesLines.add("{{$line->_ref_prescription_line_mix->_guid}}");
	        {{else}}
					  window.alertesLines.add("{{$line->_guid}}");
					{{/if}}
				});
			{{/if}}
		</script>
  {{else}}
    {{if $puce_orange}}
      {{assign var="image" value="note_orange.png"}}
      {{assign var="color" value=#fff288}}
    {{/if}}
  {{/if}}
  
{{/if}}

{{if $image && $color}}
	<img src="images/icons/{{$image}}" onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-alertes-line-{{$line->_guid}}")' />
	
	<div id="tooltip-content-alertes-line-{{$line->_guid}}" style="display: none; background-color: {{$color}};">
		{{foreach from=$prescription->_alertes key=type item=curr_type}}
		  {{if array_key_exists($code_cip, $curr_type)}}
		    <ul>
		    {{foreach from=$curr_type.$code_cip item=_alerte}}
		      <li>
		        <strong>{{tr}}CPrescriptionLineMedicament-alerte-{{$type}}-court{{/tr}} :</strong>
 		        {{$_alerte.libelle}}
		      </li>
		    {{/foreach}}
		    </ul>
		  {{/if}}
		{{/foreach}}
	</div>
{{/if}}