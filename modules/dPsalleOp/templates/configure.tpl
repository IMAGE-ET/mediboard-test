<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">

  {{mb_include module=system template=inc_config_bool var=mode_anesth}}  
  {{mb_include module=system template=inc_config_bool var=enable_surveillance_perop}}  

  <tr>
    <th class="title" colspan="6">Listes déroulantes des timings</th>
  </tr>
  
  {{assign var="var" value="max_sub_minutes"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td>
      <input type="text" name="{{$m}}[{{$var}}]" value="{{$conf.$m.$var}}"/> 
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
      <input type="text" name="{{$m}}[{{$var}}]" value="{{$conf.$m.$var}}"/> 
    </td>             
  </tr>

  {{assign var="class" value="COperation"}}
  
  <tr>
    <th class="title" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
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
        <option value="never" {{if $conf.$m.$class.$var == "never"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-never{{/tr}}</option>
        <option value="oneday" {{if $conf.$m.$class.$var == "oneday"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-oneday{{/tr}}</option>
        <option value="button" {{if $conf.$m.$class.$var == "button"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-button{{/tr}}</option>
        <option value="facturation" {{if $conf.$m.$class.$var == "facturation"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-facturation{{/tr}}</option>
      </select>
    </td>             
  </tr>

  {{assign var="class" value="CActeCCAM"}}
  <tr>
    <th class="title" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>
  {{mb_include module=system template=inc_config_bool var=contraste}}
  {{mb_include module=system template=inc_config_bool var=alerte_asso}}
  {{mb_include module=system template=inc_config_bool var=tarif}}
  {{mb_include module=system template=inc_config_bool var=restrict_display_tarif}}  
  {{mb_include module=system template=inc_config_bool var=codage_strict}}
  {{mb_include module=system template=inc_config_bool var=openline}}
  {{mb_include module=system template=inc_config_bool var=modifs_compacts}}
  {{mb_include module=system template=inc_config_bool var=commentaire}}
  {{mb_include module=system template=inc_config_bool var=signature}}
  {{mb_include module=system template=inc_config_bool var=envoi_actes_salle}}
  {{mb_include module=system template=inc_config_bool var=envoi_motif_depassement}}

  {{assign var="class" value="CDossierMedical"}}
  <tr>
    <th class="title" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>
  {{mb_include module=system template=inc_config_bool var=DAS}}  
  
  {{assign var="class" value="CDailyCheckList"}}
  <tr>
    <th class="title" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>
  {{mb_include module=system template=inc_config_bool var=active}}
  {{mb_include module=system template=inc_config_bool var=active_salle_reveil}}
  
  <tr>
    <th class="title" colspan="2">Cocher la bonne réponse par défaut dans les checklists de : </th>
  </tr>
	<tr>
		<td colspan="2">
			<div class="small-info">
				Choisir "Oui" signifie que la réponse cochée par défaut est celle qui serait choisie si le point à vérifier est positif.
				<br />
				<strong>Attention, une réponse positive peut être "Non" si par exemple la question est "Risque de saignement important".</strong>
			</div>
		</td>
	</tr>
  {{mb_include module=system template=inc_config_bool var=default_good_answer_COperation}}
  {{mb_include module=system template=inc_config_bool var=default_good_answer_CSalle}}
  {{mb_include module=system template=inc_config_bool var=default_good_answer_CBlocOperatoire}}
  
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>

</table>

</form>