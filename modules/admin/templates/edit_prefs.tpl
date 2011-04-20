{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  <tr>
    <td class="narrow">
    
<ul id="tab-modules" class="control_tabs_vertical" style="width: 20em;">
  {{foreach from=$prefs key=module item=_prefs}}
  {{if $_prefs}}  
  <li>
  	<a href="#{{$module}}" style="line-height: 24px;">
      {{if $module != "common"}}
        <img src="modules/{{$module}}/images/icon.png" width="24" style="float: left;" />
      {{/if}}
	  	{{tr}}module-{{$module}}-court{{/tr}}
	  	<small>({{$_prefs|@count}})</small> 
	  </a>
	 </li>
	 {{/if}}
	{{/foreach}}
</ul>

<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tab-modules', true));
Preferences.onSubmit = function(form) {
  return onSubmitFormAjax(form, {onComplete: Preferences.refresh || Prototype.emptyFunction});
}
</script>

    </td><td>

<form name="form-edit-preferences" action="?m=admin{{if !$ajax}}&amp;{{$actionType}}={{$action}}{{/if}}" method="post" onsubmit="return Preferences.onSubmit(this)">

<input type="hidden" name="dosql" value="do_preference_aed" />
<input type="hidden" name="m" value="admin" />
<input type="hidden" name="user_id" value="{{$user->_id}}" />

<table class="form">
  <col style="width: 40%;" />
  {{if $user_id != "default"}} 
    <col style="width: 15%;" />
		{{if !$user->template}}
	  <col style="width: 15%;" />
		{{/if}}
	  <col style="width: 30%;" />
  {{else}}
    <col style="width: 40%;" />	
  {{/if}}
	  
  <tr>
    <th colspan="4" class="title">
      {{tr}}User preferences{{/tr}}
    </th>
	</tr>
	<tr>
    <th class="category">
      {{tr}}Preference{{/tr}}
    </th>
    <th class="category">
			{{if $can->admin && $user_id != "default"}}
			<a href="?m={{$m}}&amp;tab=edit_prefs&amp;user_id=default" class="button edit">
        {{tr}}Default{{/tr}}
			</a>
			{{else}}
        {{tr}}Default{{/tr}}
			{{/if}}

    </th>

    {{if $user_id != "default"}} 

    {{if !$user->template}}
    <th class="category">
      {{tr}}User template{{/tr}} :
      <br />
      {{if $can->edit && $prof->_id}}
      <a href="?m={{$m}}&amp;tab=edit_prefs&amp;user_id={{$prof->_id}}" class="button edit">
        {{$prof}}
      </a>
			{{else}}
        {{if $prof->_id}}{{$prof}}{{else}}{{tr}}None{{/tr}}{{/if}}
      {{/if}}
    </th>
		{{/if}}
		
    <th class="category">
      {{tr}}{{$user->template|ternary:"User template":"CUser"}}{{/tr}} :
      <br/>{{if $user->_id}}{{$user}}{{else}}{{tr}}None{{/tr}}{{/if}}
    </th>
		
		{{/if}}
  </tr>

  <!-- Tous modules confondus -->
  {{assign var="module" value="common"}}
	<tbody style="display: none" id="{{$module}}">

  {{mb_include template=inc_pref spec=enum var=LOCALE values=$locales value_locale_prefix="language."}}
  {{mb_include template=inc_pref spec=enum var=UISTYLE values=$styles value_locale_prefix="style."}}
  {{mb_include template=inc_pref spec=enum var=MenuPosition values="top|left"}}
  {{mb_include template=inc_pref spec=module var=DEFMODULE}}
  {{mb_include template=inc_pref spec=bool var=touchscreen}}
  {{mb_include template=inc_pref spec=enum var=tooltipAppearenceTimeout values="short|medium|long" value_locale_prefix=""}}
  {{mb_include template=inc_pref spec=enum var=autocompleteDelay values="short|medium|long" value_locale_prefix=""}}
  {{mb_include template=inc_pref spec=bool var=showLastUpdate}}
  
  </tbody>
  
  {{assign var="module" value="dPpatients"}}
  {{if $prefs.$module}}
  <tbody style="display: none" id="{{$module}}">

  {{mb_include template=inc_pref spec=bool var=GestionFSE}}
  {{mb_include template=inc_pref spec=str  var=InterMaxDir}}
  {{mb_include template=inc_pref spec=str  var=VitaleVisionDir}}
  {{mb_include template=inc_pref spec=bool var=VitaleVision}}
  {{mb_include template=inc_pref spec=bool var=vCardExport}}

	</tbody>
  {{/if}}
	  
  {{assign var="module" value="dPcabinet"}}
  {{if $prefs.$module}}  
	<tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->
  
  {{mb_include template=inc_pref spec=bool var=AUTOADDSIGN}}
  {{mb_include template=inc_pref spec=enum var=AFFCONSULT values="0|1"}}
  {{mb_include template=inc_pref spec=enum var=MODCONSULT values="0|1"}}
  {{mb_include template=inc_pref spec=bool var=dPcabinet_show_program}}
	  
  {{mb_include template=inc_pref spec=enum var=DossierCabinet values="dPcabinet|dPpatients"}}
  {{mb_include template=inc_pref spec=enum var=DefaultPeriod values="day|week|month" value_locale_prefix="Period."}}
  {{mb_include template=inc_pref spec=enum var=simpleCabinet values="0|1"}}
  {{mb_include template=inc_pref spec=enum var=ccam_consultation values="0|1"}}
  {{mb_include template=inc_pref spec=enum var=view_traitement values="0|1"}}
  {{mb_include template=inc_pref spec=bool var=autoCloseConsult}}
  {{mb_include template=inc_pref spec=bool var=resumeCompta}}
  {{mb_include template=inc_pref spec=bool var=showDatesAntecedents}}
  {{mb_include template=inc_pref spec=bool var=pratOnlyForConsult}}

	</tbody>
  {{/if}}
    
  {{assign var="module" value="dPcompteRendu"}}
  {{if $prefs.$module}}  
	<tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->

  {{mb_include template=inc_pref spec=enum var=saveOnPrint values="0|1|2"}}
  {{mb_include template=inc_pref spec=enum var=choicepratcab values="prat|cab|group"}}
  {{mb_include template=inc_pref spec=enum var=listDefault values="ulli|br|inline"}}
  {{mb_include template=inc_pref spec=str  var=listBrPrefix}}
  {{mb_include template=inc_pref spec=str  var=listInlineSeparator}}
  {{mb_include template=inc_pref spec=bool var=aideTimestamp}}
  {{mb_include template=inc_pref spec=bool var=aideOwner}}
  {{mb_include template=inc_pref spec=bool var=aideFastMode}}
  {{mb_include template=inc_pref spec=bool var=aideAutoComplete}}
  {{mb_include template=inc_pref spec=bool var=aideShowOver}}
  {{mb_include template=inc_pref spec=bool var=pdf_and_thumbs}}
  {{mb_include template=inc_pref spec=bool var=mode_play}}
  </tbody>
  {{/if}}

  {{assign var="module" value="dPfiles"}}
  {{if $prefs.$module}}
	  <tbody style="display: none" id="{{$module}}">
	  <!-- Préférences pour le module {{$module}} -->
	    {{mb_include template=inc_pref spec=str  var=directory_to_watch}}
	    {{mb_include template=inc_pref spec=bool var=debug_yoplet}}
	  </tbody>
  {{/if}}

  {{assign var="module" value="dPhospi"}}
  {{if $prefs.$module}}  
	<tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->
  
  {{mb_include template=inc_pref spec=enum var=ccam_sejour values="0|1"}}
  
  </tbody>
  {{/if}}

  {{assign var="module" value="system"}}
  {{if $prefs.$module}}  
	<tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->

  {{mb_include template=inc_pref spec=bool var=INFOSYSTEM}}
  {{mb_include template=inc_pref spec=bool var=showTemplateSpans}}
    
  </tbody>
  {{/if}}
  
  {{assign var="module" value="dPplanningOp"}}
  
  {{if $prefs.$module}}
	<tbody style="display: none" id="{{$module}}">
	  
  {{mb_include template=inc_pref spec=enum var=mode_dhe values="0|1"}}
  {{mb_include template=inc_pref spec=bool var=dPplanningOp_listeCompacte}}
  
  </tbody>
  {{/if}}
  
  {{assign var="module" value="dPprescription"}}
  {{if $prefs.$module}}  
	<tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->

  {{mb_include template=inc_pref spec=enum var=easy_mode values="0|1"}}
  {{mb_include template=inc_pref spec=enum var=show_transmissions_form values="0|1"}}
  {{mb_include template=inc_pref spec=enum var=hide_old_lines values="0|1"}}
	
  </tbody>
  {{/if}}
  
  {{assign var="module" value="dPurgences"}}
  {{if $prefs.$module}}  
  <tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->

  {{mb_include template=inc_pref spec=enum var=defaultRPUSort values="ccmu|_patient_id|_entree"}}
  {{mb_include template=inc_pref spec=bool var=showMissingRPU}}

  </tbody>
  {{/if}}
  
  {{assign var="module" value="ssr"}}
  {{if $prefs.$module}}  
  <tbody style="display: none" id="{{$module}}">
  <!-- Préférences pour le module {{$module}} -->

  {{mb_include template=inc_pref spec=bool var=ssr_planning_dragndrop}}
  {{mb_include template=inc_pref spec=bool var=ssr_planning_resize}}
  {{mb_include template=inc_pref spec=num  var=ssr_planification_duree}}
  {{mb_include template=inc_pref spec=bool var=ssr_planification_show_equipement}}

  </tbody>
  {{/if}}
  
  <tr>
    <td class="button" colspan="4">
      <button type="submit" class="submit singleclick">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>
    
    </td>
  </tr>
</table>
