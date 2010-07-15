{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $tab && $can->edit && $user_id}}
<a href="?m={{$m}}&amp;tab=edit_prefs&amp;user_id=0" class="button edit">
  Modifier les préférences par défaut
</a>
{{/if}}

<table class="main">
  <tr>
    <td style="width: 0.1%">
    
<ul id="tab-modules" class="control_tabs_vertical" style="width: 20em;">
  {{foreach from=$prefsUser key=module item=prefs}}
  {{if $prefs}}  
  <li>
  	<a href="#{{$module}}">
	  	{{tr}}module-{{$module}}-court{{/tr}}
	  	<small>({{$prefs|@count}})</small> 
	  </a>
	 </li>
	 {{/if}}
	{{/foreach}}
</ul>

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tab-modules', true);
});
</script>

    </td><td>

<form name="form-edit-preferences" action="?m=admin{{if !$ajax}}&amp;{{$actionType}}={{$action}}{{/if}}" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="dosql" value="do_preference_aed" />
<input type="hidden" name="m" value="admin" />
<input type="hidden" name="user_id" value="{{$user_id}}" />

<table class="form">
  <col style="width: 50%;" />
  <col style="width: 50%;" />
  
  <tr>
    <th colspan="2" class="title">
      {{tr}}User preferences{{/tr}} : {{if $user_id}}{{$user->_view}}{{else}}{{tr}}Default{{/tr}}{{/if}}
    </th>
  </tr>

  <!-- Tous modules confondus -->
  {{assign var="module" value="common"}}
	<tbody style="display: none" id="{{$module}}">

  {{assign var="var" value="LOCALE"}}
  <tr>
    <th>
      <label for="pref[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}-desc{{/tr}}</label>
    </th>
    <td>
      <select name="pref[{{$var}}]" class="text" size="1">
        {{foreach from=$locales item=currLocale key=keyLocale}}
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
      <label for="pref[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">
        {{tr}}pref-{{$var}}{{/tr}}{{tr}}{{/tr}}
      </label>
    </th>
    <td>
      <select name="pref[{{$var}}]" class="text" size="1">
        {{foreach from=$styles item=currStyles key=keyStyles}}
        <option value="{{$keyStyles}}" {{if $keyStyles==$prefsUser.$module.$var}}selected="selected"{{/if}}>
          {{$currStyles}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="MenuPosition"}}
  <tr>
    <th>
      <label for="pref[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">
        {{tr}}pref-{{$var}}{{/tr}}
      </label>
    </th>
    <td>
      <select name="pref[{{$var}}]">
        <option value="top"  {{if $prefsUser.$module.$var == "top"  }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-top{{/tr}}</option>
        <option value="left" {{if $prefsUser.$module.$var == "left" }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-left{{/tr}}</option>
      </select>
    </td>
  </tr>
    
  {{assign var="var" value="DEFMODULE"}}
  <tr>
    <th>
      <label for="pref[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref[{{$var}}]" class="text" size="1">
        {{foreach from=$modules item=_module}}
          {{assign var=mod_name value=$_module->mod_name}}
          
          <option value="{{$mod_name}}" {{if $mod_name == $prefsUser.$module.$var}}selected="selected"{{/if}} style="font-weight: bold;">
            {{tr}}module-{{$mod_name}}-court{{/tr}}
          </option>
          
          {{foreach from=$_module->_tabs item=_tab}}
            <option value="{{$mod_name}}-{{$_tab}}" {{if "$mod_name-$_tab" == $prefsUser.$module.$var}}selected="selected"{{/if}} style="padding-left: 1em;">
              {{tr}}mod-{{$_module->mod_name}}-tab-{{$_tab}}{{/tr}}
            </option>
          {{/foreach}}
        {{/foreach}}
      </select>
    </td>
  </tr>
  

  {{mb_include template=inc_pref_bool var=touchscreen}}
  
  {{assign var="var" value="tooltipAppearenceTimeout"}}
  <tr>
    <th>
      <label for="pref[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref[{{$var}}]">
        <option value="short"  {{if $prefsUser.$module.$var == "short" }}selected="selected"{{/if}}>{{tr}}Short {{/tr}}</option>
        <option value="medium" {{if $prefsUser.$module.$var == "medium"}}selected="selected"{{/if}}>{{tr}}Medium{{/tr}}</option>
        <option value="long"   {{if $prefsUser.$module.$var == "long"  }}selected="selected"{{/if}}>{{tr}}Long  {{/tr}}</option>
      </select>
    </td>
  </tr>
  
  {{mb_include template=inc_pref_bool var=showLastUpdate}}
  
  </tbody>
  
  {{assign var="module" value="dPpatients"}}
  {{if $prefsUser.$module}}  
	<tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->

  {{assign var="var" value="DEPARTEMENT"}}
  <tr>
    <th>
      <label for="pref[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <input type="text" name="pref[{{$var}}]" value="{{$prefsUser.$module.$var}}" maxlength="3" size="4" class="num min|0 max|999"/>
    </td>
  </tr>

  {{mb_include template=inc_pref_bool var=GestionFSE}}

  {{assign var="var" value="InterMaxDir"}}
  <tr>
    <th>
      <label for="pref[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">
        {{tr}}pref-{{$var}}{{/tr}}
      </label>
    </th>
    <td>
      <input class="str" type="text" size="40" name="pref[{{$var}}]" value="{{$prefsUser.$module.$var}}" />
    </td>
  </tr>
  
  {{assign var="var" value="VitaleVisionDir"}}
  <tr>
    <th>
      <label for="pref[{{$var}}]" title="VitaleVision">
        Répertoire du fichier généré par Vitale Vision
      </label>
    </th>
    <td>
    <input class="str" type="text" size="40" name="pref[{{$var}}]" value="{{$prefsUser.$module.$var}}" />
    </td>
  </tr>
  
  {{mb_include template=inc_pref_bool var=VitaleVision}}
  
  {{mb_include template=inc_pref_bool var=vCardExport}}

	</tbody>
  {{/if}}
	  
  {{assign var="module" value="dPcabinet"}}
  {{if $prefsUser.$module}}  
	<tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->
  
  {{mb_include template=inc_pref_bool var=AUTOADDSIGN}}
  {{mb_include template=inc_pref_enum var=AFFCONSULT values="0|1"}}
  {{mb_include template=inc_pref_enum var=MODCONSULT values="0|1"}}
  {{mb_include template=inc_pref_bool var=dPcabinet_show_program}}
	  
  {{assign var="var" value="DossierCabinet"}}
  <tr>
    <th>
      <label for="pref[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref[{{$var}}]">
        <option value="dPcabinet" {{if $prefsUser.$module.$var == "dPcabinet" }}selected="selected"{{/if}}>{{tr}}module-dPcabinet-court{{/tr}}</option>
        <option value="dPpatients"{{if $prefsUser.$module.$var == "dPpatients"}}selected="selected"{{/if}}>{{tr}}module-dPpatients-court{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="DefaultPeriod"}}
  <tr>
    <th>
      <label for="pref[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref[{{$var}}]">
        <option value="day"   {{if $prefsUser.$module.$var == "day"  }}selected="selected"{{/if}}>{{tr}}Period.day{{/tr}}</option>
        <option value="week"  {{if $prefsUser.$module.$var == "week" }}selected="selected"{{/if}}>{{tr}}Period.week{{/tr}}</option>
        <option value="month" {{if $prefsUser.$module.$var == "month"}}selected="selected"{{/if}}>{{tr}}Period.month{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{mb_include template=inc_pref_enum var=simpleCabinet values="0|1"}}
  {{mb_include template=inc_pref_enum var=ccam_consultation values="0|1"}}
  {{mb_include template=inc_pref_enum var=view_traitement values="0|1"}}
  {{mb_include template=inc_pref_bool var=autoCloseConsult}}
  {{mb_include template=inc_pref_bool var=resumeCompta}}
  {{mb_include template=inc_pref_bool var=showDatesAntecedents}}
  {{mb_include template=inc_pref_bool var=pratOnlyForConsult}}


	</tbody>
  {{/if}}
    
  {{assign var="module" value="dPcompteRendu"}}
  {{if $prefsUser.$module}}  
	<tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->

  {{mb_include template=inc_pref_enum var=saveOnPrint values="0|1|2"}}
  
  {{mb_include template=inc_pref_enum var=choicepratcab values="prat|cab|group"}}
  
  </tbody>
  {{/if}}
  
  {{assign var="module" value="dPhospi"}}
  {{if $prefsUser.$module}}  
	<tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->
  
  {{mb_include template=inc_pref_enum var=ccam_sejour values="0|1"}}
  
  </tbody>
  {{/if}}

  {{assign var="module" value="system"}}
  {{if $prefsUser.$module}}  
	<tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->

  {{mb_include template=inc_pref_bool var=INFOSYSTEM}}
  {{mb_include template=inc_pref_bool var=showTemplateSpans}}
    
  </tbody>
  {{/if}}
  
  {{assign var="module" value="dPplanningOp"}}
  
  {{if $prefsUser.$module}}
	<tbody style="display: none" id="{{$module}}">
	  
  {{mb_include template=inc_pref_enum var=mode_dhe values="0|1"}}
  {{mb_include template=inc_pref_bool var=dPplanningOp_listeCompacte}}
  
  </tbody>
  {{/if}}
  
  {{assign var="module" value="dPprescription"}}
  {{if $prefsUser.$module}}  
	<tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->

  {{mb_include template=inc_pref_enum var=easy_mode values="0|1"}}
  {{mb_include template=inc_pref_enum var=show_transmissions_form values="0|1"}}

  </tbody>
  {{/if}}
  
  {{assign var="module" value="dPurgences"}}
  {{if $prefsUser.$module}}  
  <tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->

  {{mb_include template=inc_pref_enum var=defaultRPUSort values="ccmu|_patient_id|_entree"}}
  {{mb_include template=inc_pref_bool var=showMissingRPU}}

  </tbody>
  {{/if}}
  
  {{assign var="module" value="ssr"}}
  {{if $prefsUser.$module}}  
  <tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->

  {{mb_include template=inc_pref_bool var=ssr_planning_dragndrop}}
  {{mb_include template=inc_pref_bool var=ssr_planning_resize}}

  </tbody>
  {{/if}}
  
  <tr>
    <td class="button" colspan="2">
      <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>
    
    </td>
  </tr>
</table>