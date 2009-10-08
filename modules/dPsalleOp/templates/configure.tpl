<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">

	{{mb_include module=system template=inc_config_bool var=mode_anesth}}  

  <tr>
    <th class="category" colspan="6">Listes déroulantes des timings</th>
  </tr>
  
  {{assign var="var" value="max_sub_minutes"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td>
      <input type="text" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}"/> 
    </td>             
  </tr>
  
  {{assign var="var" value="max_add_minutes"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td>
      <input type="text" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}"/> 
    </td>             
  </tr>

  {{assign var="class" value="COperation"}}
  
  <tr>
    <th class="category" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>

	{{mb_include module=system template=inc_config_bool var=mode}}  
	{{mb_include module=system template=inc_config_bool var=modif_salle}}  
  
  {{assign var="var" value="modif_actes"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td>
    	<select class="str" name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="never" {{if $dPconfig.$m.$class.$var == "never"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-never{{/tr}}</option>
        <option value="oneday" {{if $dPconfig.$m.$class.$var == "oneday"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-oneday{{/tr}}</option>
        <option value="button" {{if $dPconfig.$m.$class.$var == "button"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-button{{/tr}}</option>
				<option value="facturation" {{if $dPconfig.$m.$class.$var == "facturation"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-facturation{{/tr}}</option>
      </select>
    </td>             
  </tr>

  {{assign var="class" value="CActeCCAM"}}
  
  <tr>
    <th class="category" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>
  
	{{mb_include module=system template=inc_config_bool var=contraste}}  
	{{mb_include module=system template=inc_config_bool var=alerte_asso}}  
	{{mb_include module=system template=inc_config_bool var=tarif}}  
	{{mb_include module=system template=inc_config_bool var=openline}}  
	{{mb_include module=system template=inc_config_bool var=modifs_compacts}}  
	{{mb_include module=system template=inc_config_bool var=commentaire}}  
	{{mb_include module=system template=inc_config_bool var=signature}}  

  {{assign var="class" value="CDossierMedical"}}
  <tr>
    <th class="category" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>
  
	{{mb_include module=system template=inc_config_bool var=DAS}}  
  
  {{assign var="class" value="CReveil"}}
  <tr>
    <th class="category" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>
  
	{{mb_include module=system template=inc_config_bool var=multi_tabs_reveil}}  

	{{assign var="class" value="CDailyCheckList"}}
  <tr>
    <th class="category" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>
  
	{{mb_include module=system template=inc_config_bool var=active}}
  {{mb_include module=system template=inc_config_bool var=active_salle_reveil}}
  
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>

</table>

</form>