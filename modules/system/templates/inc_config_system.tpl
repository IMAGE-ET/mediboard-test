{{* $Id: configure.tpl 7814 2010-01-12 17:26:57Z rhum1 $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7814 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  Main.add(function() {
    var input = getForm("editConfig-system")["migration[limit_date]"];
    input.className = "date";
    input.type = "hidden";
    Calendar.regField(input);

    input = getForm("editConfig-system")["system_date"];
    input.className = "date";
    input.type = "hidden";
    Calendar.regField(input);

    input = getForm("editConfig-system")["offline_time_start"];
    input.className = "time";
    input.type = "hidden";
    Calendar.regField(input);

    input = getForm("editConfig-system")["offline_time_end"];
    input.className = "time";
    input.type = "hidden";
    Calendar.regField(input);
  });

  migrateConfigs = function() {
    if (!confirm("Voulez-vous vraiment migrer les configurations en base de données ?")) {
      return;
    }

    var url = new Url("system", "ajax_migrate_configs");
    url.requestUpdate("migration_config_db");
  }
</script>

<form name="editConfig-system" action="?" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  <table class="form" style="table-layout: fixed;">
    
    {{mb_include module=system template=inc_config_str var=root_dir size=40}}
    {{mb_include module=system template=inc_config_enum var=instance_role values="prod|qualif"}}
    {{mb_include module=system template=inc_config_str var=mb_id}}
    {{mb_include module=system template=inc_config_str var=mb_oid}}
    {{mb_include module=system template=inc_config_str var=servers_ip size=40}}
    {{mb_include module=system template=inc_config_str var=system_date}}
    {{mb_include module=system template=inc_config_bool var=debug}}
    {{mb_include module=system template=inc_config_bool var=readonly}}
    {{mb_include module=system template=inc_config_bool var=offline_non_admin}}

    {{mb_include module=system template=inc_config_str var=weinre_debug_host}}
    {{mb_include module=system template=inc_config_str var=base_backup_lockfile_path}}
    {{mb_include module=system template=inc_config_str var=offline_time_start}}
    {{mb_include module=system template=inc_config_str var=offline_time_end}}
    {{mb_include module=system template=inc_config_bool var=config_db}}

    {{if $conf.config_db}}
      <tr>
        <th></th>
        <td id="migration_config_db">
          <button type="button" onclick="migrateConfigs()" class="send">Migrer les configurations</button>
        </td>
      </tr>
    {{/if}}

    {{*mb_include module=system template=inc_config_bool var=access_logs_buffer*}}
    {{mb_include module=system template=inc_config_str var=dataminer_limit numeric=true}}
    {{mb_include module=system template=inc_config_str var=aio_output_path size=50}}

    <tr>
      <th colspan="2" class="title">
        {{tr}}common-Logging{{/tr}}
      </th>
    </tr>

    {{mb_include module=system template=inc_config_bool var=log_js_errors}}
    {{mb_include module=system template=inc_config_bool var=error_logs_in_db}}
    {{mb_include module=system template=inc_config_bool var=log_datasource_metrics}}
    {{mb_include module=system template=inc_config_bool var=log_access}}
    {{mb_include module=system template=inc_config_str var=human_long_request_level numeric=true}}
    {{mb_include module=system template=inc_config_str var=bot_long_request_level numeric=true}}
    
    <tr>
      <th colspan="2" class="title">
        Sécurité
      </th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=csrf_protection}}
    {{mb_include module=system template=inc_config_str var=csrf_token_lifetime numeric=true}}
    {{mb_include module=system template=inc_config_bool var=purify_text_input}}
    
    <tr>
      <th colspan="2" class="title">
        Compression des scripts et feuilles de style
      </th>
    </tr>
    {{mb_include module=system template=inc_config_enum var=minify_javascript values="0|1"}}
    {{mb_include module=system template=inc_config_enum var=minify_css values="1|2"}}
    
    <tr>
      <th colspan="2" class="title">
        Fusion des objets
      </th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=alternative_mode}}
    {{mb_include module=system template=inc_config_bool var=merge_prevent_base_without_idex}}
    
    <tr>
      <th colspan="2" class="title">
        Paramètres réseau
      </th>
    </tr>
    {{assign var="m" value="system"}}
    {{mb_include module=system template=inc_config_str var=reverse_proxy}}
    {{mb_include module=system template=inc_config_str var=website_url size=40}}
    
    <tr>
      <th colspan="2" class="title">
        {{tr}}config-browser_compat{{/tr}}
      </th>
    </tr>
    {{foreach from=$browser_compat key=_browser item=_versions}}
    <tr>
      <th>
        <label for="browser_compat.{{$_browser}}" title="{{tr}}browser.{{$_browser}}{{/tr}}">
          {{tr}}browser.{{$_browser}}{{/tr}}
        </label>
      </th>
      <td>
        <select name="browser_compat[{{$_browser}}]">
          {{foreach from=$_versions key=_value item=_label}}
            {{assign var=_version value=$_value|is_numeric|ternary:$_label:$_value}}
            
            <option value="{{$_version}}"
                    {{if $conf.browser_compat.$_browser == $_version}}selected="selected"{{/if}}>
              {{$_label}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    {{/foreach}}
    
    <tr>
      <th colspan="2" class="title">
        Mode migration
      </th>
    </tr>
  
    {{assign var="m" value="migration"}}
    {{mb_include module=system template=inc_config_bool var=active}}
    {{mb_include module=system template=inc_config_str var=intranet_url size=40}}
    {{mb_include module=system template=inc_config_str var=extranet_url size=40}}
    {{mb_include module=system template=inc_config_str var=limit_date}}

    <tr>
      <th colspan="2" class="title">
        Mode esclave
      </th>
    </tr>

    {{assign var="m" value=""}}
    {{mb_include module=system template=inc_config_num var=enslaving_ratio}}

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>

  </table>
</form>

{{mb_include module=system template=configure_dsn dsn=slave}}
