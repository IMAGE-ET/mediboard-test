<!-- Nom / Type -->
{{if $_mb_module->_files_missing}}
  <td colspan="3" class="cancelled">
    Module '{{$_mb_module->mod_name}}' missing
  </td>
{{else}}
  <td class="narrow">
    <img src="modules/{{$_mb_module->mod_name}}/images/icon.png" style="height:18px; width:18px; float: right;" 
      title="{{$_mb_module->mod_name}}" />
  </td>

  <td>
    <label title="{{tr}}module-{{$_mb_module->mod_name}}-long{{/tr}}">
      {{if $installed}}
      <a href="?m={{$_mb_module->mod_name}}">
      {{/if}}  
         <strong>{{tr}}module-{{$_mb_module->mod_name}}-court{{/tr}}</strong>
      {{if $installed}}
      </a>
      {{/if}}
    </label>
  </td>

  <td>{{mb_value object=$_mb_module field=mod_type}}</td>
{{/if}}   

<!-- Actions -->
<td>
  {{if $_mb_module->_too_new}}
  <div class="warning">
    {{tr}}Module-_too_new-msg{{/tr}} ({{$_mb_module->_latest}})
  </div>
  {{elseif $_mb_module->_upgradable && $can->admin}}
    <form name="formUpdateModule-{{$module_id}}" method="post" class="upgrade" data-id="{{$module_id}}" data-dependencies="{{$_mb_module->_dependencies_not_verified}}"
          {{if $_mb_module->mod_type != "core"}} onsubmit="return Module.updateOne(this)" {{/if}}>
      <input type="hidden" name="dosql" value="do_manage_module" />
      <input type="hidden" name="m" value="system" /> 
      {{if $_mb_module->mod_type != "core"}}       
        <input type="hidden" name="ajax" value="1" />
      {{/if}}
      <input type="hidden" name="mod_id" value="{{$module_id}}" />
      <input type="hidden" name="cmd" value="upgrade" />
      
      <button class="button change submit upgrade oneclick" type="submit" {{* onclick="return confirm('{{tr}}CModule-confirm-upgrade{{/tr}}')" *}}>
        {{tr}}Upgrade{{/tr}} &gt;
        {{$_mb_module->_latest}}
      </button>
    </form>
  {{elseif $_mb_module->_upgradable}}
    {{tr}}Out of date{{/tr}} : {{$_mb_module->_latest}}
  {{elseif $_mb_module->mod_type != "core" && $can->admin}}
    <form name="formDeleteModule-{{$_mb_module->mod_name}}" method="post">
      <input type="hidden" name="dosql" value="do_manage_module" />
      <input type="hidden" name="m" value="system" />
      <input type="hidden" name="cmd" value="remove" />
      <input type="hidden" name="mod_id" value="{{$module_id}}" />
      
      <button class="button cancel submit" type="submit" disabled="true" onclick="return confirm('{{tr}}CModule-confirm-deletion{{/tr}}');">
        {{tr}}Remove{{/tr}}
      </button>
    </form>
  {{else}}
    <div class="info">
      {{tr}}Up to date{{/tr}}
    </div>
  {{/if}}
</td>

<!-- SD / Config. / Vers. / Actif / Visible / Dépendances -->
{{if $installed}}
  <td>
    {{if count($_mb_module->_dsns)}}
    {{foreach from=$_mb_module->_dsns item=dsns_by_status key=status}}
    {{foreach from=$dsns_by_status item=dsn}}
    <div class="
      {{if $status == 'uptodate'}}info{{/if}}
      {{if $status == 'obsolete'}}warning{{/if}}
      {{if $status == 'unavailable'}}error{{/if}}">
      {{$dsn}}
    </div>
    {{/foreach}}
    {{/foreach}}
    {{/if}}
  </td>

  <td>
    <!-- Configure -->
    {{if $_mb_module->_configable}}
      <a class="button search action" href="?m={{$_mb_module->mod_name}}&a=configure">
        {{tr}}Configure{{/tr}}
      </a>
    {{/if}}
  </td>

  <td>
    <!-- Version -->
    <div {{if $_mb_module->_too_new}} class="warning" {{/if}}>
      {{mb_value object=$_mb_module field=mod_version}}
    </div>
  </td>

  <td style="text-align: center;" class="narrow">
    <!-- Actif -->
    {{if $can->edit}}
      <form name="formActifModule-{{$module_id}}" method="post" onsubmit="return onSubmitFormAjax(this, Module.refresh.curry('{{$module_id}}'))">
        <input type="hidden" name="dosql" value="do_manage_module" />
        <input type="hidden" name="m" value="system" />        
        <input type="hidden" name="ajax" value="1" />
        <input type="hidden" name="mod_id" value="{{$module_id}}" />
        <input type="hidden" name="cmd" value="toggle" />
        
        <input type="checkbox" {{if $can->edit && $_mb_module->mod_type != "core"}}onclick="this.form.onsubmit();"{{/if}}
          {{if $_mb_module->mod_active}}checked="checked"{{/if}} 
          {{if $_mb_module->mod_type=="core"}}disabled="disabled"{{/if}} />
      </form>
    {{else}}
      {{mb_value object=$_mb_module field=mod_active}}
    {{/if}}
  </td>

  <td style="text-align: center;" class="narrow">
    <!-- Visible -->
    {{if $can->edit}}
      <form name="formVisibleModule-{{$module_id}}" method="post" onsubmit="return onSubmitFormAjax(this, Module.refresh.curry('{{$module_id}}'))">
        <input type="hidden" name="dosql" value="do_manage_module" />
        <input type="hidden" name="m" value="system" />        
        <input type="hidden" name="ajax" value="1" />
        <input type="hidden" name="mod_id" value="{{$module_id}}" />
        <input type="hidden" name="cmd" value="toggleMenu" />
        
        <input type="checkbox" {{if $can->edit && $_mb_module->mod_active}}onclick="this.form.onsubmit();"{{/if}}
          {{if $_mb_module->mod_ui_active}}checked="checked"{{/if}}
          {{if !$_mb_module->mod_active}}disabled="disabled"{{/if}}  />
      </form>
    {{else}}
      {{mb_value object=$_mb_module field=mod_ui_active}}
    {{/if}}
  </td>
  
  <td style="text-align: right;" class="narrow">
    <!-- Order -->
    {{if $can->edit}}
      <form name="formOrderModule-{{$module_id}}" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="dosql" value="do_manage_module" />
        <input type="hidden" name="m" value="system" />        
        <input type="hidden" name="ajax" value="1" />
        <input type="hidden" name="mod_id" value="{{$module_id}}" />
        <input type="hidden" name="cmd" value="" />
        
        <img src="./images/icons/updown.gif" usemap="#map-{{$_mb_module->_id}}" />
        <map name="map-{{$_mb_module->_id}}">
          <area coords="0,0,10,7"  href="#1" onclick="$V(this.up('form').cmd, 'moveup'); Module.moveRowUp(this.up('tr'));   this.up('form').onsubmit();" />
          <area coords="0,8,10,14" href="#1" onclick="$V(this.up('form').cmd, 'movedn'); Module.moveRowDown(this.up('tr')); this.up('form').onsubmit();" />
        </map>
      </form>
    {{/if}}
  </td>
  
  <td class="text">
    <!-- Dépendances -->
    {{foreach from=$_mb_module->_dependencies key=num_version item=version}}
      {{foreach from=$version item=dependency name=dependencies}}
        {{if $_mb_module->mod_version <= $num_version}}
        <label style="color: {{if $dependency->verified}}#050{{else}}#500{{/if}}" title="{{$dependency->module}}">
          {{tr}}module-{{$dependency->module}}-court{{/tr}} ({{$dependency->revision}})
          {{if !$smarty.foreach.dependencies.last}},{{/if}} 
        </label>
        {{/if}}
      {{/foreach}}
    {{/foreach}}
  </td>
{{/if}}