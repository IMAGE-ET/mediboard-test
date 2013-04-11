{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=admin script=preferences  ajax=true}}

<table class="main">
  <tr>
    <td class="narrow">
    
<ul id="tab-modules" class="control_tabs_vertical" style="width: 20em;">
  {{foreach from=$prefs key=module item=_prefs}}
  {{if $_prefs && ($module == "common" || $module|module_active)}}  
  <li>
    <a href="#module-{{$module}}" style="line-height: 24px;">
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
</script>

    </td><td>

<form name="form-edit-preferences" action="?m=admin{{if !$ajax}}&amp;{{$actionType}}={{$action}}{{/if}}" method="post" onsubmit="return Preferences.onSubmitAll(this)">

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
    <th class="category" {{if $can->admin}} colspan="2" {{/if}} >
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
  <tbody style="display: none" id="module-{{$module}}">

  {{mb_include template=inc_pref spec=enum var=LOCALE values=$locales value_locale_prefix="language."}}
  {{mb_include template=inc_pref spec=enum var=UISTYLE values=$styles value_locale_prefix="style."}}
  {{mb_include template=inc_pref spec=enum var=MenuPosition values="top|left"}}
  {{if is_dir("./mobile")}}
    {{mb_include template=inc_pref spec=bool var=MobileUI}}
  {{/if}}
  {{mb_include template=inc_pref spec=module var=DEFMODULE}}
  {{mb_include template=inc_pref spec=bool var=touchscreen}}
  {{mb_include template=inc_pref spec=enum var=tooltipAppearenceTimeout values="short|medium|long" value_locale_prefix=""}}
  {{mb_include template=inc_pref spec=enum var=autocompleteDelay values="short|medium|long" value_locale_prefix=""}}
  {{mb_include template=inc_pref spec=bool var=showCounterTip}}
  {{mb_include template=inc_pref spec=bool var=showLastUpdate}}
  {{mb_include template=inc_pref spec=enum var=textareaToolbarPosition values="right|left"}}
  {{mb_include template=inc_pref spec=bool var=planning_dragndrop}}
  {{mb_include template=inc_pref spec=bool var=planning_resize}}
  {{if $conf.session_handler == "zebra"}}
    {{mb_include template=inc_pref spec=enum var=sessionLifetime values=$session_lifetime_enum}}
  {{/if}}
  
  </tbody>
  
  {{foreach from=$prefs key=module item=_prefs}}
    {{if $module != "common" && $module|module_active}}
    <tbody style="display: none;" id="module-{{$module}}">
      {{mb_include module=$module template=preferences}}
    </tbody>
    {{/if}}
  {{/foreach}}
    
  <tr>
    <td class="button" colspan="5">
      <button type="submit" class="submit singleclick">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>
    
    </td>
  </tr>
</table>
