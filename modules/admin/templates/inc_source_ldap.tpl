{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main form"> 
  <tr>
    <td>
      <form name="edit_source_ldap-{{$number}}" action="?" method="post" 
        onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="admin" />
        <input type="hidden" name="dosql" value="do_source_ldap_aed" />
        <input type="hidden" name="source_ldap_id" value="{{$source_ldap->_id}}" />
        <input type="hidden" name="del" value="0" /> 
        <input type="hidden" name="callback" value="refreshSources" /> 
           
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
            <th> {{mb_label object=$source_ldap field=priority}} </th>
            <td> {{mb_field object=$source_ldap field=priority increment=true form="edit_source_ldap-$number"}} </td>
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
                  onComplete: refreshSources.curry('{{$source_ldap->name}}')})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{else}}  
                <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
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
          }
        }
      </script>
      <table class="main form">
        <tr>
          <th class="category" colspan="2">
            {{tr}}utilities-source-ldap{{/tr}}
          </th>
        </tr>
        
        <!-- Test d'authentification -->
        <tr>
          <td colspan="2">
            <button type="button" class="tick" onclick="LDAP.bind('{{$source_ldap->_id}}');">
              {{tr}}utilities-source-ldap-bind{{/tr}}
            </button>
          </td>
        </tr>
        <tr>
          <th>DN ou RDN LDAP </th>
          <td><input type="text" name="ldaprdn" id="ldaprdn" value=""/></td>
        </tr>
        <tr>
          <th>Mot de passe associé</th>
          <td><input type="text" name="ldappass" id="ldappass" value=""/></td>
        </tr>
        <tr>
          <td colspan="2" id="utilities-source-ldap-bind-{{$source_ldap->_id}}" class="text"></td>
        </tr>
        
        <!-- Test de recherche -->
        <tr>
          <td colspan="2">
            <button type="button" class="tick" onclick="LDAP.search('{{$source_ldap->_id}}');">
              {{tr}}utilities-source-ldap-search{{/tr}}
            </button>
          </td>
        </tr>
        <tr>
          <td colspan="2">Filtre de recherche</td>
        </tr>
        <tr>
          <td colspan="2"><textarea name="filter" id="filter">(samaccountname=*)</textarea></td>
        </tr>
        <tr>
          <td colspan="2">Attributs retournés (ex : mail, sn, cn)</td>
        </tr>
        <tr>
          <td colspan="2"><textarea name="attributes" id="attributes">samaccountname, useraccountcontrol, sn, givenname, mail</textarea></td>
				</tr>
				<tr>
          <td id="utilities-source-ldap-search-{{$source_ldap->_id}}" class="text" colspan="2"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>