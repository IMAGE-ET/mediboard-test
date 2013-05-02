{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(function(){
    Control.Tabs.create('tabs-configure-ldap', true);
    refreshSources();
  });
  
  refreshSources = function() {
    var url = new Url("admin", "ajax_refresh_source_ldap");
    url.requestUpdate("CSourceLDAP");
  }
  
  var stop = false;
  
  function LDAPMassiveImport(button){
    if(stop) {
      stop=false;
      return;
    }
    var action = $V(button.form.elements.do_import);
    if (!action) {
      stop=true;
    }
    var url = new Url("admin", "ajax_ldap_massive_import");
    url.addParam("do_import", $V(button.form.elements.do_import) ? 1 : 0);
    url.addParam("count", $V(button.form.elements.count));
    url.requestUpdate("ldap-massive-import-search", { onComplete:function() { 
      LDAPMassiveImport(button);
    }} );
  }
  
  function LDAPHexaToRegistry() {
    var url = new Url("admin", "ajax_ldap_hexa_to_registry");
    url.requestUpdate("ldap-hexa-to-registry");
  }
</script>

<table class="main">
  <tr>
    <td style="vertical-align: top; width: 130px">
      <ul id="tabs-configure-ldap" class="control_tabs_vertical">
        <li><a href="#ldap">{{tr}}ldap{{/tr}}</a></li>
        <li><a href="#ldap-user">Utilisateur LDAP</a></li>
        <li><a href="#CSourceLDAP">{{tr}}CSourceLDAP{{/tr}}</a></li>
      </ul>
    </td>
    <td style="vertical-align: top;">
      <div id="ldap" style="display: none;">
        <form name="editConfigLDAP" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
          <input type="hidden" name="dosql" value="do_configure" />
          <input type="hidden" name="m" value="system" />
        
          <table class="form" style="">
            {{assign var="class" value="LDAP"}}
            <tr>
              <th class="title" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
            </tr>
            {{mb_include module=system template=inc_config_bool var=ldap_connection}}
            {{mb_include module=system template=inc_config_bool var=allow_change_password}}
            {{mb_include module=system template=inc_config_bool var=allow_login_as_admin}}
            {{mb_include module=system template=inc_config_str var=ldap_tag}}
            {{mb_include module=system template=inc_config_enum var=object_guid_mode values=hexa|registry}}
            <tr>
              <td class="button" colspan="100">
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
              </td>
            </tr>
          </table>
        </form>
        
        <form name="LDAPMassiveImportSearchForm" action="?" method="get">
          <table class="tbl">
            <tr>
              <th class="title" colspan="2">{{tr}}ldap-massive-import{{/tr}}</th>
            </tr>
            
            <tr>
              <td class="narrow">
                <button type="button" class="tick" onclick="LDAPMassiveImport(this)">
                  {{tr}}ldap-massive-import-search{{/tr}}
                </button>
                <button type="button" class="stop" onclick="stop=true">{{tr}}Stop{{/tr}}</button>
              </td>
              <td rowspan="2" id="ldap-massive-import-search"></td>
            </tr>
            <tr>
              <td class="narrow">
                <label><input type="checkbox" name="do_import" />{{tr}}Import{{/tr}}</label>
                <input type="text" name="count" value="5" size="10"/>
                <script type="text/javascript">
                  Main.add(function () {
                    getForm("LDAPMassiveImportSearchForm")["count"].addSpinner({min:1, max:100, step:1});
                  });
                </script>
              </td>
            </tr>
          </table>
        </form>
        
        <table class="tbl">
          <tr>
            <th class="title" colspan="2">{{tr}}ldap-hexa-to-registry{{/tr}}</th>
          </tr>
          
          <tr>
            <td class="narrow">
              <button type="button" class="tick" onclick="LDAPHexaToRegistry(this)">
                {{tr}}ldap-hexa-to-registry{{/tr}}
              </button>
            </td>
            <td rowspan="2" id="ldap-hexa-to-registry"></td>
          </tr>
        </table>
      </div>
          
     <div id="ldap-user">
        <form name="editConfigLDAPUser" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
          <input type="hidden" name="dosql" value="do_configure" />
          <input type="hidden" name="m" value="system" />
        
          <table class="form">
            <tr>
              <th class="category" colspan="2">
                {{tr}}config-user-source-ldap{{/tr}}
              </th>
            </tr>        
            {{assign var="class" value="LDAP"}}
            {{mb_include module=system template=inc_config_str var=ldap_user}}
            
            {{assign var="var" value="ldap_password"}}
            <th>
              <label title="{{tr}}config-admin-{{$class}}-{{$var}}-desc{{/tr}}">
                {{tr}}config-admin-{{$class}}-{{$var}}{{/tr}}
              </label>  
            </th>
            <td>
              {{mb_ternary test=ldapUser var=value value=$conf.admin.$class.$var other=""}}
              <input type="password" name="admin[{{$class}}][{{$var}}]" value="{{$value}}" />
            </td>
            <tr>
              <td class="button" colspan="100">
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
              </td>
            </tr>
          </table>
        </form>
      </div>
      
      <div id="CSourceLDAP" style="display: none;"></div>
    </td>
  </tr>
</table>