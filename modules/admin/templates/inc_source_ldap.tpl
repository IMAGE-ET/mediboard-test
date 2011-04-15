{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main"> 
  <tr>
    <td>
      <form name="edit_source_ldap" action="?" method="post" 
        onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="admin" />
        <input type="hidden" name="dosql" value="do_source_ldap_aed" />
        <input type="hidden" name="source_ldap_id" value="{{$source_ldap->_id}}" />
        <input type="hidden" name="del" value="0" /> 
        <input type="hidden" name="callback" value="refreshSourceLDAP" /> 
           
        <table class="form">        
          <tr>
            <th class="category" colspan="2">
              {{tr}}config-source-ldap{{/tr}}
            </th>
          </tr>
          <tr>
            <th> {{mb_label object=$source_ldap field=name}} </th>
            <td> {{mb_field object=$source_ldap field=name}} </td>
          </tr>
          <tr>
            <th> {{mb_label object=$source_ldap field=host}} </th>
            <td> {{mb_field object=$source_ldap field=host}} </td>
          </tr>
          <tr>
            <th> {{mb_label object=$source_ldap field=port}} </th>
            <td> {{mb_field object=$source_ldap field=port}} </td>
          </tr>
          <tr>
            <th> {{mb_label object=$source_ldap field=rootdn}} </th>
            <td> {{mb_field object=$source_ldap field=rootdn}} </td>
          </tr>
          <tr>
            <th> {{mb_label object=$source_ldap field=bind_rdn_suffix}} </th>
            <td> {{mb_field object=$source_ldap field=bind_rdn_suffix}} </td>
          </tr>
          <tr>
            <th> {{mb_label object=$source_ldap field=ldap_opt_protocol_version}} </th>
            <td> {{mb_field object=$source_ldap field=ldap_opt_protocol_version increment=true form=edit_source_ldap min=2 max=3}} </td>
          </tr>
          <tr>
            <th> {{mb_label object=$source_ldap field=ldap_opt_referrals}} </th>
            <td> {{mb_field object=$source_ldap field=ldap_opt_referrals}} </td>
          </tr>
          <tr>
            <td class="button" colspan="2">
              {{if $source_ldap->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" 
                  onclick="confirmDeletion(this.form,{ajax:1, typeName:'',objName:'{{$source_ldap->_view|smarty:nodefaults|JSAttribute}}', 
                  onComplete: refreshSourceLDAP.curry('{{$source_ldap->name}}')})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{else}}  
                <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
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
    </td>
    <td class="greedyPane">
      <script type="text/javascript">
        LDAP = {
          bind: function (source_ldap_id) {
            var url = new Url("admin", "ajax_tests_ldap");
            url.addParam("source_ldap_id", source_ldap_id);
            url.addParam("ldaprdn", $('ldaprdn').value);
            url.addParam("ldappass", $('ldappass').value);
            url.requestUpdate("utilities-source-ldap-bind-"+source_ldap_id);
          },

          search: function (source_ldap_id) {
            var url = new Url("admin", "ajax_tests_ldap");
            url.addParam("source_ldap_id", source_ldap_id);
            url.addParam("action", "search");
            url.addParam("ldaprdn", $('ldaprdn').value);
            url.addParam("ldappass", $('ldappass').value);
            url.addParam("filter", $('filter').value);
            url.addParam("attributes", $('attributes').value);
            url.requestUpdate("utilities-source-ldap-search-"+source_ldap_id);
          },
        }
      </script>
      <table class="tbl">
        <tr>
          <th class="category" colspan="100">
            {{tr}}utilities-source-ldap{{/tr}}
          </th>
        </tr>
        
        <!-- Test d'authentification -->
        <tr>
          <td class="narrow">
            <table class="tbl">
              <tr>
                <td colspan="2">
                  <button type="button" class="tick" onclick="LDAP.bind('{{$source_ldap->_id}}');">
                    {{tr}}utilities-source-ldap-bind{{/tr}}
                  </button>
                </td>
              </tr>
              <tr>
                <td>DN ou RDN LDAP </td>
                <td><input type="text" name="ldaprdn" id="ldaprdn" value=""/></td>
              </tr>
              <tr>
                <td>Mot de passe associé</td>
                <td><input type="text" name="ldappass" id="ldappass" value=""/></td>
              </tr>
            </table>
          </td>
          <td id="utilities-source-ldap-bind-{{$source_ldap->_id}}" class="text"></td>
        </tr>
        
        <!-- Test de recherche -->
        <tr>
          <td class="narrow">
            <table class="tbl">
              <tr>
                <td colspan="2">
                  <button type="button" class="tick" onclick="LDAP.search('{{$source_ldap->_id}}');">
                    {{tr}}utilities-source-ldap-search{{/tr}}
                  </button>
                </td>
              </tr>
              <tr>
                <td>Filtre de recherche</td>
              </tr>
              <tr>
                <td><textarea name="filter" id="filter">(samaccountname=*)</textarea></td>
              </tr>
              <tr>
                <td>Attributs retournés (ex : mail, sn, cn)</td>
              </tr>
              <tr>
                <td><textarea name="attributes" id="attributes">samaccountname, useraccountcontrol, sn, givenname, mail</textarea>
                </td>
              </tr>
            </table>
          </td>
          <td id="utilities-source-ldap-search-{{$source_ldap->_id}}" class="text"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>