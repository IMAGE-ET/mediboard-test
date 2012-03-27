 <script type="text/javascript">
  refreshModule = function(mod_id) {
    var url = new Url('system', 'ajax_refresh_module'); 
    url.addParam("mod_id", mod_id);
    url.requestUpdate('mod_'+mod_id, { onComplete: updateInstalledControlTabs } ); 
  }
  

  updateInstalledControlTabs = function() {
    if ($('installed').select('button.upgrade').length == 0) {
      $$('a[href=#installed]')[0].removeClassName("wrong");
    }  
  }

  moveRow = function(tr, cmd) {
    switch (cmd) {
      case "moveup" :
        if (tr.previous() == tr.up().firstDescendant()) {
          return;
        }
        tr.previous().insert({before:tr});
      break;    
      
      case "movedn" :
        tr.next().insert({after:tr});
      break;
    }
  }

  updateAll = function() {
    $('installed').select('form.upgrade').sort(function(form){ return form.get("dependencies"); }).reverse().invoke("onsubmit");
  }
</script>

{{if $installed}}
<div style="text-align: right">
  {{if $upgradable && $coreModules|@count == 0}}
    <button class="change oneclick" onclick="updateAll()">
      Mettre à jour tous les modules
    </button>
  {{/if}}

  <button class="tick" onclick="$('installed').select('button.cancel').invoke('enable');">
    Activer la suppression
  </button>
</div>
{{/if}}

<table class="tbl">
  <tr>
    <th colspan="2">{{mb_title class=CModule field=_view}}</th>
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
  
  {{foreach from=$object item=_mb_module}}
    {{if !$_mb_module->_id}}
      {{assign var=module_name value=$_mb_module->mod_name}}
      
      <tr id="mod_{{$_mb_module->mod_name}}">
        <td class="narrow">
          <img src="modules/{{$_mb_module->mod_name}}/images/icon.png" style="height:18px; width:18px; float: right;" 
            title="{{$_mb_module->mod_name}}" />
        </td>
    
        <td>
          <label title="{{tr}}module-{{$_mb_module->mod_name}}-long{{/tr}}">
            <strong>{{tr}}module-{{$_mb_module->mod_name}}-court{{/tr}}</strong>
          </label>
        </td>
    
        <td>{{mb_value object=$_mb_module field=mod_type}}</td>
    
        <td>
          {{if $can->admin}}
            <form name="formInstallModule-{{$_mb_module->mod_name}}" method="post">
              <input type="hidden" name="dosql" value="do_manage_module" />
              <input type="hidden" name="m" value="system" />
              <input type="hidden" name="cmd" value="install" />
              <input type="hidden" name="mod_name" value="{{$module_name}}" />
              
              <button class="button new submit" type="submit">
                {{tr}}Install{{/tr}} &gt;
                {{mb_value object=$_mb_module field=_latest}}
              </button>
            </form>
          {{/if}}
        </td>
        <td class="text">
          {{foreach from=$_mb_module->_dependencies key=num_version item=version}}
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
      {{assign var=module_id value=$_mb_module->_id}}
      <tr id="mod_{{$module_id}}">
        {{mb_include template=inc_module}}   
      </tr>
    {{/if}}
  {{foreachelse}}
    <tr>
      <td colspan="5" class="empty">{{tr}}CModule.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>