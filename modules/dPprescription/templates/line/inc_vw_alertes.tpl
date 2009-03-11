{{assign var=allergie value=$prescription_reelle->_alertes.allergie}}
{{assign var=IPC value=$prescription_reelle->_alertes.IPC}}
{{assign var=interaction value=$prescription_reelle->_alertes.interaction}}
{{assign var=profil value=$prescription_reelle->_alertes.profil}}
{{assign var=code_cip value=$line->code_cip}}

{{assign var=image value=""}}
{{assign var=color value=""}}
  
{{if (array_key_exists($code_cip, $allergie) || array_key_exists($code_cip, $interaction) ||
      array_key_exists($code_cip, $profil)   ||  array_key_exists($code_cip, $IPC))}}
     
  {{assign var=puce_orange value=false}}
  {{assign var=puce_rouge value=false}}
  
  <!-- Allergie -->
  {{if array_key_exists($code_cip, $allergie)}}
    <!-- Alerte faible -->
    {{if $dPconfig.dPprescription.CPrescription.scores.allergie == '1'}}
      {{assign var=puce_orange value=true}}
      
    {{/if}}
    <!-- Alerte importante -->
    {{if $dPconfig.dPprescription.CPrescription.scores.allergie == '2'}}
      {{assign var=puce_rouge value=true}}
    {{/if}}
  {{/if}}
  
  <!-- IPC -->
  {{if array_key_exists($code_cip, $IPC)}}
    <!-- Alerte faible -->
    {{if $dPconfig.dPprescription.CPrescription.scores.IPC == '1'}}
      {{assign var=puce_orange value=true}}
    {{/if}}
    <!-- Alerte importante -->
    {{if $dPconfig.dPprescription.CPrescription.scores.IPC == '2'}}
      {{assign var=puce_rouge value=true}}
    {{/if}}
  {{/if}}
  
  <!-- Interactions -->
  {{if array_key_exists($code_cip, $interaction)}}
	  {{foreach from=$interaction.$code_cip key=toto item=_interaction}}
	    {{assign var=_niveau value=$_interaction.niveau}}
	    {{assign var=niveau value=niv$_niveau}}
	    {{if $dPconfig.dPprescription.CPrescription.scores.interaction.$niveau == '1'}}
	      {{assign var=puce_orange value=true}}
	    {{/if}}
	    {{if $dPconfig.dPprescription.CPrescription.scores.interaction.$niveau == '2'}}
	      {{assign var=puce_rouge value=true}}
	    {{/if}}
	  {{/foreach}}
  {{/if}}
  
  <!-- Profil -->
  {{if array_key_exists($code_cip, $profil)}}
	  {{foreach from=$profil.$code_cip item=_profil}}
	    {{assign var=_niveau value=$_profil.niveau}}
	    {{assign var=niveau value=niv$_niveau}}
	     
	    {{if $dPconfig.dPprescription.CPrescription.scores.profil.$niveau == '1'}}
	      {{assign var=puce_orange value=true}}
	    {{/if}}
	    {{if $dPconfig.dPprescription.CPrescription.scores.profil.$niveau == '2'}}
	      {{assign var=puce_rouge value=true}}
	    {{/if}}
	  {{/foreach}}
  {{/if}}
  
  <!-- S�lection de la puce � afficher -->
  {{if $puce_rouge}}
    {{assign var="image" value="note_red.png"}}
    {{assign var="color" value=#ff7474}}
  {{else}}
    {{if $puce_orange}}
      {{assign var="image" value="note_orange.png"}}
      {{assign var="color" value=#fff288}}
    {{/if}}
  {{/if}}
  
{{/if}}

{{if $image && $color}}
	<img src="images/icons/{{$image}}" title="" alt="" 
			 onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-alertes-line-{{$line->_guid}}")' />
	
	<div id="tooltip-content-alertes-line-{{$line->_guid}}" style="display: none; background-color: {{$color}};">
		{{foreach from=$prescription_reelle->_alertes key=type item=curr_type}}
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