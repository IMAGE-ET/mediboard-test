<form name="changeuser" action="?m=admin&amp;{{$actionType}}={{$action}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_preference_aed" />
<input type="hidden" name="pref_user" value="{{$user_id}}" />
<input type="hidden" name="del" value="0" />

{{if $tab && $can->edit && $user_id}}
<a href="?m={{$m}}&amp;tab=edit_prefs&amp;user_id=0" class="button edit">
  Editer les préférences par défaut
</a>
{{/if}}
<table class="form">
  <tr>
    <th colspan="2" class="title">
      {{tr}}User preferences{{/tr}} : {{if $user_id}}{{$user->_view}}{{else}}{{tr}}Default{{/tr}}{{/if}}
    </th>
  </tr>

  <!-- Tous modules confondus -->
  {{assign var="module" value="GENERALE"}}

  {{assign var="var" value="LOCALE"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}-desc{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]" class="text" size="1">
        {{foreach from=$locales|smarty:nodefaults item=currLocale key=keyLocale}}
        <option value="{{$keyLocale}}" {{if $keyLocale==$prefsUser.$module.$var}}selected="selected"{{/if}}>
          {{tr}}language.{{$currLocale}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="UISTYLE"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">
        {{tr}}pref-{{$var}}{{/tr}}{{tr}}{{/tr}}
      </label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]" class="text" size="1">
        {{foreach from=$styles|smarty:nodefaults item=currStyles key=keyStyles}}
        <option value="{{$keyStyles}}" {{if $keyStyles==$prefsUser.$module.$var}}selected="selected"{{/if}}>
          {{tr}}{{$currStyles}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="MenuPosition"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">
        {{tr}}pref-{{$var}}{{/tr}}
      </label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="top"  {{if $prefsUser.$module.$var == "top"  }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-top{{/tr}}</option>
        <option value="left" {{if $prefsUser.$module.$var == "left" }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-left{{/tr}}</option>
      </select>
    </td>
  </tr>
    
  {{assign var="var" value="DEFMODULE"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]" class="text" size="1">
        {{foreach from=$modules|smarty:nodefaults item=currModule key=keyModule}}
        <option value="{{$currModule->mod_name}}" {{if $currModule->mod_name==$prefsUser.$module.$var}}selected="selected"{{/if}}>
          {{tr}}module-{{$currModule->mod_name}}-court{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="touchscreen"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>{{tr}}bool.1{{/tr}}</option>
      </select>
    </td>
  </tr>
  
  {{assign var="module" value="dPpatients"}}
  <!-- Préférences pour le module {{$module}} -->
  {{if $prefsUser.$module}}  

  {{assign var="var" value="DEPARTEMENT"}}
  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}}</th>
  </tr>
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <input type="text" name="pref_name[{{$var}}]" value="{{$prefsUser.$module.$var}}" maxlength="3" size="4" class="num minMax|0|999"/>
    </td>
  </tr>
  {{/if}}
  
  {{assign var="module" value="dPcabinet"}}
  {{if $prefsUser.$module}}
  
  {{assign var="var" value="AFFCONSULT"}}
  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}}</th>
  </tr>

  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-0{{/tr}}</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="MODCONSULT"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-0{{/tr}}</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="GestionFSE"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>{{tr}}bool.1{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="InterMaxDir"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">
        {{tr}}pref-{{$var}}{{/tr}}
      </label>
    </th>
    <td>
      <input class="str" type="text" size="40" name="pref_name[{{$var}}]" value="{{$prefsUser.$module.$var}}" />
    </td>
  </tr>
  
  {{assign var="var" value="VitaleVisionDir"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="VitaleVision">
        Répertoire du fichier généré par Vitale Vision
      </label>
    </th>
    <td>
		<input class="str" type="text" size="40" name="pref_name[{{$var}}]" value="{{$prefsUser.$module.$var}}" />
    </td>
  </tr>
  
  {{assign var="var" value="VitaleVision"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="VitaleVision">
        Préferer l'utilisation de Vitale Vision à LogicMax
      </label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>{{tr}}bool.1{{/tr}}</option>
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="DossierCabinet"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="dPcabinet" {{if $prefsUser.$module.$var == "dPcabinet" }}selected="selected"{{/if}}>{{tr}}module-dPcabinet-court{{/tr}}</option>
        <option value="dPpatients"{{if $prefsUser.$module.$var == "dPpatients"}}selected="selected"{{/if}}>{{tr}}module-dPpatients-court{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="DefaultPeriod"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="day"   {{if $prefsUser.$module.$var == "day"  }}selected="selected"{{/if}}>{{tr}}Period.day{{/tr}}</option>
        <option value="week"  {{if $prefsUser.$module.$var == "week" }}selected="selected"{{/if}}>{{tr}}Period.week{{/tr}}</option>
        <option value="month" {{if $prefsUser.$module.$var == "month"}}selected="selected"{{/if}}>{{tr}}Period.month{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="simpleCabinet"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0" {{if $prefsUser.$module.$var == 0 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-0{{/tr}}</option>
        <option value="1" {{if $prefsUser.$module.$var == 1 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="ccam_consultation"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0" {{if $prefsUser.$module.$var == 0 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-0{{/tr}}</option>
        <option value="1" {{if $prefsUser.$module.$var == 1 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="view_traitement"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0" {{if $prefsUser.$module.$var == 0 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-0{{/tr}}</option>
        <option value="1" {{if $prefsUser.$module.$var == 1 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="autoCloseConsult"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0" {{if $prefsUser.$module.$var == 0 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-0{{/tr}}</option>
        <option value="1" {{if $prefsUser.$module.$var == 1 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="resumeCompta"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0" {{if $prefsUser.$module.$var == 0 }}selected="selected"{{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1" {{if $prefsUser.$module.$var == 1 }}selected="selected"{{/if}}>{{tr}}bool.1{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="showDatesAntecedents"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0" {{if $prefsUser.$module.$var == 0 }}selected="selected"{{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1" {{if $prefsUser.$module.$var == 1 }}selected="selected"{{/if}}>{{tr}}bool.1{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{/if}}
  
  {{assign var="module" value="dPcabinet"}}
  {{if $prefsUser.$module}}
  
  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}} - Anesth</th>
  </tr>

  {{assign var="var" value="AUTOADDSIGN"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>{{tr}}bool.1{{/tr}}</option>
      </select>
    </td>
  </tr>
  {{/if}}
  
  {{assign var="module" value="dPcompteRendu"}}
  {{if $prefsUser.$module}}
  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}}</th>
  </tr>
  {{assign var="var" value="saveOnPrint"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0" {{if $prefsUser.$module.$var == 0 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-0{{/tr}}</option>
        <option value="1" {{if $prefsUser.$module.$var == 1 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-1{{/tr}}</option>
        <option value="2" {{if $prefsUser.$module.$var == 2 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-2{{/tr}}</option>
      </select>
    </td>
  </tr>
  {{/if}}
  
  {{assign var="module" value="dPhospi"}}
  {{if $prefsUser.$module}}
  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}}</th>
  </tr>
  {{assign var="var" value="ccam_sejour"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0" {{if $prefsUser.$module.$var == 0 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-0{{/tr}}</option>
        <option value="1" {{if $prefsUser.$module.$var == 1 }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>
  {{/if}}

  {{assign var="module" value="system"}}
  {{if $prefsUser.$module}}  
  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}}</th>
  </tr>

  {{assign var="var" value="INFOSYSTEM"}}
  <tr>
  	<th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
  	<td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>{{tr}}bool.1{{/tr}}</option>
      </select>
    </td>
  </tr>
  {{/if}}
  
  {{assign var="module" value="dPplanningOp"}}
  {{if $prefsUser.$module}}  
  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}}</th>
  </tr>

  {{assign var="var" value="mode_dhe"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-0{{/tr}}</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>
  {{/if}}
  
  {{assign var="module" value="dPprescription"}}
  {{if $prefsUser.$module}}  
  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}}</th>
  </tr>

  {{assign var="var" value="mode_readonly"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-0{{/tr}}</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button" colspan="2">
      <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</form>