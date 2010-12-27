<table class="tbl">
    <tr>
      <th>{{mb_title class=CModule field=mod_name}}</th>
      <th>{{mb_title class=CModule field=_view}}</th>
      <th>{{mb_title class=CModule field=mod_type}}</th>
      <th class="narrow">{{tr}}Action{{/tr}}</th>
      {{if $installed}}
      <th>{{mb_title class=CModule field=_dsns}}</th>
      <th class="narrow">{{mb_title class=CModule field=_configable}}</th>
      <th>{{mb_title class=CModule field=mod_version}}</th>
      <th>{{mb_title class=CModule field=mod_active}}</th>
      <th>{{mb_title class=CModule field=mod_ui_active}}</th>
      <th>{{mb_title class=CModule field=mod_ui_order}}</th>
      {{/if}}
      <th>{{mb_title class=CModule field=_dependencies}}</th>
    </tr>
    
    {{foreach from=$object item=mbmodule}}
  
    {{if !$mbmodule->_id}}
    {{assign var=module_name value=$mbmodule->mod_name}}
    {{assign var=cmd value="?m=system&a=domodsql&mod_name=$module_name&cmd"}}
    <tr>
      <td>
        <img src="modules/{{$mbmodule->mod_name}}/images/icon.png" style="height:18px; width:18px; float: right;" />
        <strong>{{$mbmodule->mod_name}}</strong>
      </td>
  
      <td>
        <label title="{{tr}}module-{{$mbmodule->mod_name}}-long{{/tr}}">
          {{tr}}module-{{$mbmodule->mod_name}}-court{{/tr}}
        </label>
      </td>
  
      <td>{{mb_value object=$mbmodule field=mod_type}}</td>
  
      <td>
        {{if $can->admin}}
        <a class="button new action" href="{{$cmd}}=install">
          {{tr}}Install{{/tr}} &gt;
          {{mb_value object=$mbmodule field=_latest}}
        </a>
        {{/if}}
      </td>
      <td class="text">
        {{foreach from=$mbmodule->_dependencies key=num_version item=version}}
          {{foreach from=$version item=dependency}}
            <label style="color: {{if $dependency->verified}}#050{{else}}#900{{/if}}" title="{{$dependency->module}}">
              {{tr}}module-{{$dependency->module}}-court{{/tr}} ({{$dependency->revision}})
              {{if !$smarty.foreach.dependencies.last}},{{/if}} 
            </label>
          {{/foreach}}
        {{/foreach}}
      </td>
    </tr>
  
    {{else}}
    
    {{assign var=module_id value=$mbmodule->_id}}
    {{assign var=cmd value="?m=system&a=domodsql&mod_id=$module_id&cmd"}}
    <tr> 
			{{if $mbmodule->_files_missing}}
			<td colspan="3" class="cancelled">
				Module '{{$mbmodule->mod_name}}' missing
			</td>

      {{else}}
      <td>
        <img src="modules/{{$mbmodule->mod_name}}/images/icon.png" style="height: 18px; width: 18px; float: right;" />
        <strong>{{$mbmodule->mod_name}}</strong>
      </td>
  
      <td>
        <label title="{{tr}}module-{{$mbmodule->mod_name}}-long{{/tr}}">
          {{tr}}module-{{$mbmodule->mod_name}}-court{{/tr}}
        </label>
      </td>
  
      <td>{{mb_value object=$mbmodule field=mod_type}}</td>
      {{/if}}   
  
      <!-- Actions -->
      <td>
        {{if $mbmodule->_upgradable && $can->admin}}
        <a class="button change action" href="{{$cmd}}=upgrade" onclick="return confirm('{{tr}}CModule-confirm-upgrade{{/tr}}')">
          {{tr}}Upgrade{{/tr}} &gt; {{$mbmodule->_latest}}
        </a>
        {{elseif $mbmodule->_upgradable}}
        {{tr}}Out of date{{/tr}} : {{$mbmodule->_latest}}
        {{elseif $mbmodule->mod_type != "core" && $can->admin}}
        <a class="button cancel action"  href="{{$cmd}}=remove" onclick="return confirm('{{tr}}CModule-confirm-deletion{{/tr}}');">
          {{tr}}Remove{{/tr}}
        </a>
        {{else}}
          <div class="info">
            {{tr}}Up to date{{/tr}}
          </div>
        {{/if}}
      </td>
      {{if $installed}}
        <td>
          {{if count($mbmodule->_dsns)}}
          {{foreach from=$mbmodule->_dsns item=dsns_by_status key=status}}
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
          {{if $mbmodule->_configable}}
          <a class="button search action" href="{{$cmd}}=configure">
            {{tr}}Configure{{/tr}}
          </a>
          {{/if}}
        </td>
    
        <td>
          {{mb_value object=$mbmodule field=mod_version}}
        </td>
    
        <td style="text-align: center;" class="narrow">
          <!-- Actif -->
          {{if $can->edit}}
          <input type="checkbox" {{if $can->edit && $mbmodule->mod_type != "core"}}onclick="location.href='{{$cmd}}=toggle'"{{/if}}
          {{if $mbmodule->mod_active}}checked="checked"{{/if}} 
          {{if $mbmodule->mod_type=="core"}}disabled="disabled"{{/if}} />
          {{else}}
          {{mb_value object=$mbmodule field=mod_active}}
          {{/if}}
        </td>
    
        <td style="text-align: center;" class="narrow">
          <!-- Visible -->
          {{if $can->edit}}
          <input type="checkbox" {{if $can->edit && $mbmodule->mod_active}}onclick="location.href='{{$cmd}}=toggleMenu'"{{/if}} 
          {{if $mbmodule->mod_ui_active}}checked="checked"{{/if}}
          {{if !$mbmodule->mod_active}}disabled="disabled"{{/if}}  />
          {{else}}
          {{mb_value object=$mbmodule field=mod_ui_active}}
          {{/if}}
        </td>
        
        <td style="text-align: right;" class="narrow">
          {{$mbmodule->mod_ui_order}}
          {{if $can->edit}}
          <img src="./images/icons/updown.gif" usemap="#map-{{$mbmodule->_id}}" />
          <map name="map-{{$mbmodule->_id}}">
            <area coords="0,0,10,7"  href="{{$cmd}}=moveup" />
            <area coords="0,8,10,14" href="{{$cmd}}=movedn" />
          </map>
          {{/if}}
        </td>
        <td class="text">
          {{foreach from=$mbmodule->_dependencies key=num_version item=version}}
            {{foreach from=$version item=dependency name=dependencies}}
              {{if $mbmodule->mod_version <= $num_version}}
              <label style="color: {{if $dependency->verified}}#050{{else}}#500{{/if}}" title="{{$dependency->module}}">
                {{tr}}module-{{$dependency->module}}-court{{/tr}} ({{$dependency->revision}})
								{{if !$smarty.foreach.dependencies.last}},{{/if}} 
              </label>
              {{/if}}
            {{/foreach}}
          {{/foreach}}
        </td>
      {{/if}}
    </tr>
    {{/if}}
    {{/foreach}}
  </table>