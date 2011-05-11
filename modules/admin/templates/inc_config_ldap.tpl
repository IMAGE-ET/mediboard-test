{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-configure-ldap', true));
  
  refreshSourceLDAP = function(source_ldap_id) {
    var url = new Url("admin", "ajax_refresh_source_ldap");
    url.addParam("source_ldap_id", source_ldap_id);
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
</script>

<table class="main">
  <tr>
    <td style="vertical-align: top; width: 100px">
      <ul id="tabs-configure-ldap" class="control_tabs_vertical">
        <li><a href="#ldap">{{tr}}ldap{{/tr}}</a></li>
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
      </div>
      
      <div id="CSourceLDAP" style="display: none;">
        {{mb_include template=inc_source_ldap}}
      </div>
    </td>
  </tr>
</table>