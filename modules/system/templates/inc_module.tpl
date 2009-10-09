<table class="tbl">
    <tr>
      <th>{{mb_title class=CModule field=mod_name}}</th>
      <th>{{mb_title class=CModule field=_view}}</th>
      <th>{{mb_title class=CModule field=mod_type}}</th>
      <th>{{tr}}Action{{/tr}}</th>
      {{if $installe}}
      <th>{{mb_title class=CModule field=_dsns}}</th>
      <th>{{mb_title class=CModule field=_configable}}</th>
      <th>{{mb_title class=CModule field=mod_version}}</th>
      <th>{{mb_title class=CModule field=mod_active}}</th>
      <th>{{mb_title class=CModule field=mod_ui_active}}</th>
      <th colspan="2">{{mb_title class=CModule field=mod_ui_order}}</th>
      {{/if}}
    </tr>
    
    {{foreach from=$object item=mbmodule}}
  
    {{if !$mbmodule->_id}}
    {{assign var=module_name value=$mbmodule->mod_name}}
    {{assign var=cmd value="?m=system&a=domodsql&mod_name=$module_name&cmd"}}
    <tr>
      <td>
        <img src="modules/{{$mbmodule->mod_name}}/images/icon.png" style="height:18px; width:18px; float: right;" alt="?" />
        <strong>{{$mbmodule->mod_name}}</strong>
      </td>
  
      <td>
        <label title="{{tr}}module-{{$mbmodule->mod_name}}-long{{/tr}}">
          {{tr}}module-{{$mbmodule->mod_name}}-court{{/tr}}
        </label>
      </td>
  
      <td>{{mb_value object=$mbmodule field=mod_type}}</td>
  
      <td colspan="10">
        {{if $can->edit}}
        <a class="button new action" href="{{$cmd}}=install">
          {{tr}}Install{{/tr}} &gt;
          {{mb_value object=$mbmodule field=_latest}}
        </a>
        {{/if}}
      </td>
    </tr>
  
    {{else}}
    {{assign var=module_id value=$mbmodule->_id}}
    {{assign var=cmd value="?m=system&a=domodsql&mod_id=$module_id&cmd"}}
    <tr> 
      <td>
        <img src="modules/{{$mbmodule->mod_name}}/images/icon.png" style="height: 18px; width: 18px; float: right;" alt="?" />
        <strong>{{$mbmodule->mod_name}}</strong>
      </td>
  
      <td>
        <label title="{{tr}}module-{{$mbmodule->mod_name}}-long{{/tr}}">
          {{tr}}module-{{$mbmodule->mod_name}}-court{{/tr}}
        </label>
      </td>
  
      <td>{{mb_value object=$mbmodule field=mod_type}}</td>
  
      <!-- Actions -->
      <td>
        {{if $mbmodule->_upgradable}}
        <a class="button change action" href="{{$cmd}}=upgrade" onclick="return confirm('{{tr}}CModule-confirm-upgrade{{/tr}}')">
          {{tr}}Upgrade{{/tr}} &gt; {{$mbmodule->_latest}}
        </a>
  
        {{elseif $mbmodule->mod_type != "core" && $can->edit}}
        <a class="button cancel action"  href="{{$cmd}}=remove" onclick="return confirm('{{tr}}CModule-confirm-deletion{{/tr}}');">
          {{tr}}Remove{{/tr}}
        </a>
        {{/if}}
      </td>
      
      {{if $installe}}
        <td>
          {{if count($mbmodule->_dsns)}}
          {{foreach from=$mbmodule->_dsns item=dsns_by_status key=status}}
          {{foreach from=$dsns_by_status item=dsn}}
          <div class="
            {{if $status == 'uptodate'}}message{{/if}}
            {{if $status == 'obsolete'}}warning{{/if}}
            {{if $status == 'unavailable'}}error{{/if}}">{{$dsn}}</div>
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
    
        <td style="text-align: center; width: 1%;">
          <!-- Actif -->
          <input type="checkbox" {{if $can->edit && $mbmodule->mod_type != "core"}}onclick="location.href='{{$cmd}}=toggle'"{{/if}}
          {{if $mbmodule->mod_active}}checked="checked"{{/if}} 
          {{if $mbmodule->mod_type=="core"}}disabled="disabled"{{/if}} />
        </td>
    
        <td style="text-align: center; width: 1%;">
          <input type="checkbox" {{if $can->edit && $mbmodule->mod_active}}onclick="location.href='{{$cmd}}=toggleMenu'"{{/if}} 
          {{if $mbmodule->mod_ui_active}}checked="checked"{{/if}}
          {{if !$mbmodule->mod_active}}disabled="disabled"{{/if}}  />
        </td>
        
        <td style="text-align: right; width: 1%;">
          {{$mbmodule->mod_ui_order}}
          <img alt="updown" src="./images/icons/updown.gif" usemap="#map-{{$mbmodule->_id}}" />
          {{if $can->edit}}
          <map name="map-{{$mbmodule->_id}}">
            <area coords="0,0,10,7"  href="{{$cmd}}=moveup" />
            <area coords="0,8,10,14" href="{{$cmd}}=movedn" />
          </map>
          {{/if}}
        </td>
      {{/if}}
    </tr>
    {{/if}}
    {{/foreach}}
  </table>