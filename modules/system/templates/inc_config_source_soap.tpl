{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main"> 
  <tr>
    <td>
      <form name="editSourceSoap" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: refreshExchangeSource.curry('{{$exchange_source_name}}', '{{$type}}') } )">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_source_soap_aed" />
        <input type="hidden" name="source_soap_id" value="{{$object->_id}}" />
        <input type="hidden" name="del" value="0" /> 
        <input type="hidden" name="name" value="{{$exchange_source_name}}" />  
        
        <table class="form">
          <tr>
            <th class="category" colspan="100">
              {{tr}}config-source-soap{{/tr}}
            </th>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="name"}}</th>
            <td><input type="text" readonly="readonly" name="name" value="{{$exchange_source_name}}" /></td>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="host"}}</th>
            <td>{{mb_field object=$object field="host"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="user"}}</th>
            <td>{{mb_field object=$object field="user"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="password"}}</th>
            <td>{{mb_field object=$object field="password"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="wsdl_mode"}}</th>
            <td>{{mb_field object=$object field="wsdl_mode"}}</td>
          </tr>
          
          <tr>
            <td class="button" colspan="2">
              {{if $object->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:1, typeName:'',objName:'{{$object->_view|smarty:nodefaults|JSAttribute}}', onComplete: refreshExchangeSource.curry('{{$exchange_source_name}}', '{{$type}}')})">
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
        var SOAP = {
          connexion: function (exchange_source_name) {
            var url = new Url("system", "ajax_connexion_soap");
            url.addParam("exchange_source_name", exchange_source_name);
            url.requestUpdate("utilities-source-soap-connexion-" + exchange_source_name);
          },
          
          getFunctions: function (exchange_source_name) {
            var url = new Url("system", "ajax_getFunctions_soap");
            url.addParam("exchange_source_name", exchange_source_name);
            url.requestUpdate("utilities-source-soap-getFunctions-" + exchange_source_name);
          }
        }
      </script>
      <table class="tbl">
        <tr>
          <th class="category" colspan="100">
            {{tr}}utilities-source-soap{{/tr}}
          </th>
        </tr>
        
        <!-- Test connexion SOAP -->
        <tr>
          <td>
            <button type="button" class="search" onclick="SOAP.connexion('{{$exchange_source_name}}');">
              {{tr}}utilities-source-soap-connexion{{/tr}}
            </button>
          </td>
          <td id="utilities-source-soap-connexion-{{$exchange_source_name}}" />
        </tr>
        
        <!-- Liste des functions SOAP -->
        <tr>
          <td>
            <button type="button" class="search" onclick="SOAP.getFunctions('{{$exchange_source_name}}');">
              {{tr}}utilities-source-soap-getFunctions{{/tr}}
            </button> 
          </td>
          <td id="utilities-source-soap-getFunctions-{{$exchange_source_name}}" />
        </tr>
        
      </table>
    </td>
  </tr>
</table>
<div class="big-info">
  Les caractères suivants sont utilisés pour spécifier l'authentification dans l'url :
  <ul>
    <li>%u - Utilisateur service web </li>
    <li>%p - Mot de passe service web</li>
  </ul>
</div>