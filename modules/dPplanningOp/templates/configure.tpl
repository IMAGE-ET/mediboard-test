<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">
  <tr>
    <th class="category" colspan="6">Option de la DHE simplifiée</th>
  </tr>
  
  {{assign var="class" value="CSejour"}}
  {{assign var="var"   value="easy_service"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class thcolspan="3" tdcolspan="3"}}
  {{assign var="var"   value="easy_chambre_simple"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class thcolspan="3" tdcolspan="3"}}
  
  {{assign var="class" value="COperation"}}
  {{assign var="var"   value="easy_horaire_voulu"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class thcolspan="3" tdcolspan="3"}}
  {{assign var="var"   value="easy_materiel"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class thcolspan="3" tdcolspan="3"}}
  {{assign var="var"   value="easy_remarques"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class thcolspan="3" tdcolspan="3"}}
  {{assign var="var"   value="easy_regime"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class thcolspan="3" tdcolspan="3"}}

  {{assign var="class" value="COperation"}}
  <tr>
    <th class="category" colspan="6">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="verif_cote"}}
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>
    </th>
    <td colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]_1">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]_0">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>           
  </tr>
  
  {{assign var="var" value="horaire_voulu"}}
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>
  
  {{assign var="var" value="duree_deb"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listHours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_hour}}
        </option>
      {{/foreach}}
      </select>
    </td>

    {{assign var="var" value="hour_urgence_deb"}}
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listHours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_hour}}
        </option>
      {{/foreach}}
      </select>
    </td>

    {{assign var="var" value="min_intervalle"}}
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listInterval item=_interval}}
        <option value="{{$_interval}}"{{if $_interval == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_interval}}
        </option>
      {{/foreach}}
      </select>
    </td>

  </tr>
  <tr>

    {{assign var="var" value="duree_fin"}}
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listHours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_hour}}
        </option>
      {{/foreach}}
      </select>
    </td>

    {{assign var="var" value="hour_urgence_fin"}}
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listHours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_hour}}
        </option>
      {{/foreach}}
      </select>
    </td>
    
    <td colspan="2" />
  </tr>
	
  {{assign var="var" value="delete_only_admin"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class thcolspan="3" tdcolspan="3"}}
	
  {{assign var="class" value="CSejour"}}
  <tr>
    <th class="category" colspan="6">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="patient_id"}}
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]_1">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]_0">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>
  
  {{assign var="var" value="modif_SHS"}}
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]_1">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]_0">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>

  <tr>
    {{assign var="var" value="heure_deb"}}
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listHours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_hour}}
        </option>
      {{/foreach}}
      </select>
    </td>

    {{assign var="var" value="heure_fin"}}
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listHours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_hour}}
        </option>
      {{/foreach}}
      </select>
    </td>

    {{assign var="var" value="min_intervalle"}}
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listInterval item=_interval}}
        <option value="{{$_interval}}"{{if $_interval == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_interval}}
        </option>
      {{/foreach}}
      </select>
    </td>
  </tr>  
  
  {{assign var="var" value="service_id_notNull"}}
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
    </th>
    <td colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]_1">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]_0">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>
	
	{{assign var="var" value="delete_only_admin"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class thcolspan="3" tdcolspan="3"}}
	
  
  <tr>
    <th class="category" colspan="6">Heure par defaut du séjour</th>
  </tr>
  
  <tr>
    <th class="category" colspan="3">Heure d'entree</th>
    <th class="category" colspan="3">Heure de sortie</th>
  </tr>
  
  <tr>
    <td colspan="3">
      Heure d'entree la veille
      {{assign var="class" value="CSejour"}}
      {{assign var="var" value="heure_entree_veille"}}
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listHours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_hour}}
        </option>
      {{/foreach}}
      </select>
  
      Heure d'entree le jour meme
      {{assign var="var" value="heure_entree_jour"}}
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listHours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_hour}}
        </option>
      {{/foreach}}
      </select>
    </td>
  
    <td colspan="3">
      Heure de sortie ambulatoire
      {{assign var="var" value="heure_sortie_ambu"}}
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listHours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_hour}}
        </option>
      {{/foreach}}
      </select>

      Heure de sortie autre (>=1 jour)
      {{assign var="var" value="heure_sortie_autre"}}
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$listHours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
          {{$_hour}}
        </option>
      {{/foreach}}
      </select>
    </td> 
  </tr>
  
  {{assign var="class" value="CSejour"}}
  {{assign var="var"   value="sortie_prevue"}}
  <tr>
    <th class="category" colspan="6">{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}</th>
  </tr>
    
  {{foreach from=$dPconfig.$m.$class.$var key=type item=value}}
  <tr>
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}][{{$type}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$type}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$type}}{{/tr}}
      </label>     
    </th>
    <td colspan="3">
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}][{{$type}}]">
        <option value="04"  {{if "04" == $dPconfig.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 04h</option>
        <option value="24" {{if "24" == $dPconfig.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 24h</option>
      </select>
    </td>
  </tr>
  {{/foreach}}
  
  <tr>
    <th class="category" colspan="6">Blocage des objets</th>
  </tr>

  <tr>
    <th class="category" colspan="3">Blocage des interventions</th>
    <th class="category" colspan="3">Blocage des séjours</th>
  </tr>
  
  <tr>
    {{assign var="class" value="COperation"}}
    {{assign var="var" value="locked"}}
    <td colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>     
    {{assign var="class" value="CSejour"}}
    {{assign var="var" value="locked"}}
    <td colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/>
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>
  
  <tr>
    <th class="category" colspan="100">Tag pour les numéros de dossier</th>
  </tr>
  
  <tr>
    {{assign var="var" value="tag_dossier"}}
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <input class="str" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>
  
  <tr>
    {{assign var="var" value="tag_dossier_pa"}}
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <input class="str" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>
  
  <tr>
    {{assign var="var" value="tag_dossier_cancel"}}
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <input class="str" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>
  
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</form>

{{include file=inc_configure_actions.tpl}}