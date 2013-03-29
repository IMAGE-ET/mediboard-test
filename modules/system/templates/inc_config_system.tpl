{{* $Id: configure.tpl 7814 2010-01-12 17:26:57Z rhum1 $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7814 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
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
</script>

<form name="editConfig-system" action="?m=system&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form" style="table-layout: fixed;">
    
    {{mb_include module=system template=inc_config_str var=root_dir size=40}}
    {{mb_include module=system template=inc_config_enum var=instance_role values="prod|qualif"}}
    {{mb_include module=system template=inc_config_str var=mb_id}}
    {{mb_include module=system template=inc_config_str var=system_date}}
    {{mb_include module=system template=inc_config_bool var=debug}}
    {{mb_include module=system template=inc_config_bool var=readonly}}
    {{mb_include module=system template=inc_config_bool var=log_js_errors}}
    {{mb_include module=system template=inc_config_str var=weinre_debug_host}}
    {{mb_include module=system template=inc_config_str var=base_backup_lockfile_path}}
    {{mb_include module=system template=inc_config_str var=offline_time_start}}
    {{mb_include module=system template=inc_config_str var=offline_time_end}}
    {{mb_include module=system template=inc_config_bool var=config_db}}

    <tr>
      <th colspan="2" class="title">
        Sécurité
      </th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=csrf_protection}}
    {{mb_include module=system template=inc_config_str var=csrf_token_lifetime numeric=true}}
    
    <tr>
      <th colspan="2" class="title">
        Compression des scripts et feuilles de style
      </th>
    </tr>
    {{mb_include module=system template=inc_config_enum var=minify_javascript values="0|1"}} {{* |2 *}}
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
    
    {{mb_include module=system template=inc_config_enum var=browser_enable_ie9 m=null values="0|1|2"}}
    
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
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>