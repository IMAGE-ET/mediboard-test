<script type="text/javascript">

checkValue = function(field1, field2){
	if((parseInt(field1.value, '10') + 1) < 10){
	  field2.value = "0"+(parseInt(field1.value, '10') + 1);
	} else {
    field2.value = parseInt(field1.value, '10') + 1;
  }
}

popupVoies = function(){
  url = new Url;
  url.setModuleAction("dPprescription", "vw_voies");
  url.popup(300,700,"Voies");
}


</script>


<!-- Variables de configuration -->
<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">
  
  {{assign var="class" value="CPrescription"}}
  {{assign var="var" value="add_element_category"}}
  <tr>
   <th class="category" colspan="6">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
   </th>
  </tr>
  <tr>  
    <td colspan="6" style="text-align: center">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>
  
  {{assign var="var" value="time_print_ordonnance"}}
  <tr>
    <th class="category" colspan="6">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
  </tr>
  <tr>  
    <td colspan="6" style="text-align: center">
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listHours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_hour}}
        </option>
      {{/foreach}}
      </select>
      heures
    </td>             
  </tr>
  
  <!-- Gestion des scores de prescription -->
  {{assign var="var" value="scores"}}
  <tr>
    <th class="title" colspan="6">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
  </tr>
	<tr>
    <th colspan="6" class="category">
       {{tr}}config-{{$m}}-{{$class}}-{{$var}}-interaction{{/tr}}
    </th>
  </tr>
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][interaction][niv1]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-interaction-niv1{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-interaction-niv1{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][interaction][niv1]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.interaction.niv1}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.interaction.niv1}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.interaction.niv1}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
	<tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][interaction][niv2]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-interaction-niv2{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-interaction-niv2{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][interaction][niv2]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.interaction.niv2}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.interaction.niv2}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.interaction.niv2}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
	<tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][interaction][niv3]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-interaction-niv3{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-interaction-niv3{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][interaction][niv3]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.interaction.niv3}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.interaction.niv3}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.interaction.niv3}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
	<tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][interaction][niv4]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-interaction-niv4{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-interaction-niv4{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][interaction][niv4]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.interaction.niv4}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.interaction.niv4}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.interaction.niv4}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
	<tr>
    <th colspan="6" class="category">
       {{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil{{/tr}}
    </th>
  </tr>
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][profil][niv0]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil-niv0{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil-niv0{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][profil][niv0]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.profil.niv0}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.profil.niv0}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.profil.niv0}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
	<tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][profil][niv1]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil-niv1{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil-niv1{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][profil][niv1]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.profil.niv1}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.profil.niv1}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.profil.niv1}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
	<tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][profil][niv2]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil-niv2{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil-niv2{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][profil][niv2]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.profil.niv2}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.profil.niv2}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.profil.niv2}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
	<tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][profil][niv9]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil-niv9{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil-niv9{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][profil][niv9]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.profil.niv9}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.profil.niv9}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.profil.niv9}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
	<tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][profil][niv30]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil-niv30{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil-niv30{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][profil][niv30]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.profil.niv30}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.profil.niv30}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.profil.niv30}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
	<tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][profil][niv39]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil-niv39{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil-niv39{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][profil][niv39]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.profil.niv39}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.profil.niv39}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.profil.niv39}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
	<tr>
    <th colspan="6" class="category">
      Autres
    </th>
  </tr>
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][IPC]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-IPC{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-IPC{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][IPC]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.IPC}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.IPC}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.IPC}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
	<tr>
	  <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][allergie]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-allergie{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-allergie{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][allergie]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.allergie}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.allergie}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.allergie}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
	<tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][hors_livret]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-hors_livret{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-hors_livret{{/tr}}
      </label>  
    </th>
	  <td colspan="3" style="text-align: center">
	    <select name="{{$m}}[{{$class}}][{{$var}}][hors_livret]">
        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.hors_livret}} selected="selected" {{/if}}>0</option>
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.hors_livret}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.hors_livret}} selected="selected" {{/if}}>2</option>
      </select>
	  </td>
	</tr>
  <tr>
    <th class="category" colspan="10">Affichage des voies disponibles</th>
  </tr>
  <tr>
    <td colspan="10" style="text-align: center;">
      <button class="search" onclick="popupVoies()" type="button">Afficher les voies</button>
    </td>
  </tr>
  
  {{assign var="class" value="CCategoryPrescription"}}
  <tr>
   <th class="title" colspan="6">
      <label for="{{$m}}[{{$class}}]" title="{{tr}}config-{{$m}}-{{$class}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}{{/tr}}
      </label>    
   </th>
  </tr>
  <tr>
    <th class="category" colspan="3">Impression ordonnance</th>
    <th class="category" colspan="3">Unité de prise</th>
  </tr>
  {{include file="inc_configure_chapitre.tpl" var=dmi}}
  {{include file="inc_configure_chapitre.tpl" var=anapath}}
  {{include file="inc_configure_chapitre.tpl" var=biologie}}
  {{include file="inc_configure_chapitre.tpl" var=imagerie}}
  {{include file="inc_configure_chapitre.tpl" var=consult}}
  {{include file="inc_configure_chapitre.tpl" var=kine}}
  {{include file="inc_configure_chapitre.tpl" var=soin}}
  {{include file="inc_configure_chapitre.tpl" var=dm}}
  
  {{assign var="class" value="CMomentUnitaire"}}
  {{assign var="var" value="principaux"}}
  <tr>
   <th class="category" colspan="6">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
   </th>
  </tr>
  <tr>  
    <td colspan="6" style="text-align: center">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>
  
  {{assign var="class" value="CPrisePosologie"}}
  <tr>
   <th class="category" colspan="6">
      <label for="{{$m}}[{{$class}}]" title="{{tr}}config-{{$m}}-{{$class}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}{{/tr}}
      </label>    
   </th>
  </tr>

  {{assign var="var" value="heures"}}
  <tr>  
	  <th><strong>Tous les</strong></th>
	  <td colspan="3" style="text-align: center">
	    à <input type="text" name="{{$m}}[{{$class}}][{{$var}}][tous_les]" value="{{$dPconfig.$m.$class.$var.tous_les}}" /> heures
	  </td>
	  <td colspan="2" />             
  </tr>
  <tr>  
	  <th><strong>1 fois par jour</strong></th>
	  <td colspan="3" style="text-align: center">
	    à <input type="text" name="{{$m}}[{{$class}}][{{$var}}][fois_par][1]" value="{{$dPconfig.$m.$class.$var.fois_par.1}}" /> heures
	  </td>   
	  <td colspan="2" />              
  </tr>
  <tr>  
	  <th><strong>2 fois par jour</strong></th>
	  <td colspan="3" style="text-align: center">
	    à <input type="text" name="{{$m}}[{{$class}}][{{$var}}][fois_par][2]" value="{{$dPconfig.$m.$class.$var.fois_par.2}}" /> heures
	  </td>
	  <td colspan="2" />                 
  </tr>
  <tr>  
	  <th><strong>3 fois par jour</strong></th>
	  <td colspan="3" style="text-align: center">
	    à <input type="text" name="{{$m}}[{{$class}}][{{$var}}][fois_par][3]" value="{{$dPconfig.$m.$class.$var.fois_par.3}}" /> heures
	  </td>  
	  <td colspan="2" />               
  </tr>
  <tr>  
	  <th><strong>4 fois par jour</strong></th>
	  <td colspan="3" style="text-align: center">
	    à <input type="text" name="{{$m}}[{{$class}}][{{$var}}][fois_par][4]" value="{{$dPconfig.$m.$class.$var.fois_par.4}}" /> heures
	  </td>  
	  <td colspan="2" />               
  </tr>
  <tr>  
	  <th><strong>5 fois par jour</strong></th>
	  <td colspan="3" style="text-align: center">
	    à <input type="text" name="{{$m}}[{{$class}}][{{$var}}][fois_par][5]" value="{{$dPconfig.$m.$class.$var.fois_par.5}}" /> heures
	  </td>  
	  <td colspan="2" />               
  </tr>
  <tr>  
	  <th><strong>6 fois par jour</strong></th>
	  <td colspan="3" style="text-align: center">
	    à <input type="text" name="{{$m}}[{{$class}}][{{$var}}][fois_par][6]" value="{{$dPconfig.$m.$class.$var.fois_par.6}}" /> heures
	  </td>  
	  <td colspan="2" />               
  </tr>

  <!-- Gestion des horaires matin/soir/nuit -->
  <tr>  
	  <th><strong>Matin</strong></th>
	  <td colspan="3" style="text-align: center">
	    De 
	    <select name="{{$m}}[{{$class}}][{{$var}}][matin][min]"
	    				onchange="checkValue(this, document.forms.editConfig['{{$m}}[{{$class}}][{{$var}}][nuit][max]'])">
	      {{foreach from=$listHours item=_hour}}
	        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var.matin.min}}selected="selected"{{/if}}>{{$_hour}}h</option>
	      {{/foreach}}
	    </select>
	    à 
	    <select name="{{$m}}[{{$class}}][{{$var}}][matin][max]" 
	    				onchange="checkValue(this, document.forms.editConfig['{{$m}}[{{$class}}][{{$var}}][soir][min]'])">
	      {{foreach from=$listHours item=_hour}}
	        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var.matin.max}}selected="selected"{{/if}}>{{$_hour}}h</option>
	      {{/foreach}}
	    </select>
	  </td>      
	  <td colspan="2" />           
  </tr>
  
  <tr>  
	  <th><strong>Soir</strong></th>
	  <td colspan="3" style="text-align: center">  
	    De 
	    <select name="{{$m}}[{{$class}}][{{$var}}][soir][min]"
	    				onchange="checkValue(this, document.forms.editConfig['{{$m}}[{{$class}}][{{$var}}][matin][max]'])">
	      {{foreach from=$listHours item=_hour}}
	        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var.soir.min}}selected="selected"{{/if}}>{{$_hour}}h</option>
	      {{/foreach}}
	    </select>
	    à 
	    <select name="{{$m}}[{{$class}}][{{$var}}][soir][max]" 
	    				onchange="checkValue(this, document.forms.editConfig['{{$m}}[{{$class}}][{{$var}}][nuit][min]'])">
	      {{foreach from=$listHours item=_hour}}
	        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var.soir.max}}selected="selected"{{/if}}>{{$_hour}}h</option>
	      {{/foreach}}
	    </select>
	  </td> 
	  <td colspan="2" />            
  </tr>
  
  <tr>  
	  <th><strong>Nuit</strong></th>
	  <td colspan="3" style="text-align: center">  
	    De 
	    <select name="{{$m}}[{{$class}}][{{$var}}][nuit][min]"
	    				onchange="checkValue(this, document.forms.editConfig['{{$m}}[{{$class}}][{{$var}}][soir][max]'])">
	      {{foreach from=$listHours item=_hour}}
	        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var.nuit.min}}selected="selected"{{/if}}>{{$_hour}}h</option>
	      {{/foreach}}
	    </select>
	    à 
	    <select name="{{$m}}[{{$class}}][{{$var}}][nuit][max]"
	   				  onchange="checkValue(this, document.forms.editConfig['{{$m}}[{{$class}}][{{$var}}][matin][min]'])">
	      {{foreach from=$listHours item=_hour}}
	        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var.nuit.max}}selected="selected"{{/if}}>{{$_hour}}h</option>
	      {{/foreach}}
	    </select>
	  </td>         
	  <td colspan="2" />    
  </tr>
  
      
    
    
  {{assign var="var" value="semaine"}}
  <tr>  
	  <th><strong>1 fois par semaine</strong></th>
	  <td colspan="3" style="text-align: center">
	    <input type="text" size="35" name="{{$m}}[{{$class}}][{{$var}}][1]" value="{{$dPconfig.$m.$class.$var.1}}" />
	  </td>  
	  <td colspan="2" />               
  </tr>
  <tr>  
	  <th><strong>2 fois par semaine</strong></th>
	  <td colspan="3" style="text-align: center">
	    <input type="text" size="35" name="{{$m}}[{{$class}}][{{$var}}][2]" value="{{$dPconfig.$m.$class.$var.2}}" />
	  </td>  
	  <td colspan="2" />               
  </tr>
  <tr>  
	  <th><strong>3 fois par semaine</strong></th>
	  <td colspan="3" style="text-align: center">
	    <input type="text" size="35" name="{{$m}}[{{$class}}][{{$var}}][3]" value="{{$dPconfig.$m.$class.$var.3}}" />
	  </td>  
	  <td colspan="2" />               
  </tr>
  <tr>  
	  <th><strong>4 fois par semaine</strong></th>
	  <td colspan="3" style="text-align: center">
	    <input type="text" size="35" name="{{$m}}[{{$class}}][{{$var}}][4]" value="{{$dPconfig.$m.$class.$var.4}}" />
	  </td>  
	  <td colspan="2" />               
  </tr>
  
  {{assign var="var" value="select_poso_bcb"}}
  <tr>
    <th class="category" colspan="6">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
  </tr>
  <tr>  
    <td colspan="6" style="text-align: center">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>
  
  
  <!-- CSpObjectHandler --> 
  {{assign var=col value="object_handlers"}}
  {{assign var=class value="CPrescriptionLineHandler"}}
  <tr>
    <th class="category" colspan="10">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  <tr>
    <th colspan="2">
      <label for="{{$col}}[{{$class}}]" title="{{tr}}config-{{$col}}-{{$class}}{{/tr}}">
        {{tr}}config-{{$col}}-{{$class}}{{/tr}}
      </label>  
    </th>
    <td colspan="4">
      <select class="bool" name="{{$col}}[{{$class}}]">
        <option value="0" {{if 0 == @$dPconfig.$col.$class}} selected="selected" {{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1" {{if 1 == @$dPconfig.$col.$class}} selected="selected" {{/if}}>{{tr}}bool.1{{/tr}}</option>
      </select>
    </td>
  </tr>  
  
  {{assign var=class value="CAdministration"}}
  {{assign var=var value="hors_plage"}}
  <tr>
    <th class="category" colspan="10">{{tr}}{{$class}}{{/tr}} en dehors des plages prevues</th>
  </tr>
  <tr>  
    <td colspan="6" style="text-align: center">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

<hr />

<!-- Imports/Exports -->

<script type="text/javascript">

function startAssociation(){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_do_add_table_association");
  url.requestUpdate("do_add_association");
}

function exportElementsPrescription(){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_export_elements_prescription");
  url.requestUpdate("export_elements_prescription");
}

function importElementsPrescription(){
  var url = new Url;
  url.setModuleAction("dPprescription", "import_elements_prescription");
  url.popup(700, 500, "export_elements_prescription");
}

function updateVoie(){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_update_voie");
  url.requestUpdate("update_voie");
}

</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="2">Opération d'imports et exports</th>
  </tr>

  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startAssociation()" >Importer la table de gestion de donnees</button></td>
    <td id="do_add_association"></td>
  </tr>

  <tr>
    <td><button class="tick" onclick="exportElementsPrescription()" >Exporter les éléments de prescriptions</button></td>
    <td id="export_elements_prescription"></td>
  </tr>

  <tr>
    <td colspan="2"><button class="tick" onclick="importElementsPrescription()" >Importer les éléments de prescriptions</button></td>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="updateVoie()">Mettre à jour la voie pour les lignes de medicaments</button></td>
    <td id="update_voie"></td>
  </tr>
</table>
