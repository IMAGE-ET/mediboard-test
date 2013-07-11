{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main layout">
  <tr>
    <td>
      <form name="editSourceSOAP-{{$source->name}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {
          onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}') } )">
        <input type="hidden" name="m" value="webservices" />
        <input type="hidden" name="dosql" value="do_source_soap_aed" />
        <input type="hidden" name="source_soap_id" value="{{$source->_id}}" />
        <input type="hidden" name="del" value="0" /> 
        <input type="hidden" name="name" value="{{$source->name}}" />

        <fieldset>
          <legend>{{tr}}CSourceSOAP{{/tr}}</legend>

          <table class="main form">
            {{mb_include module=system template=CExchangeSource_inc}}

            <tr>
              <th style="width: 100px">{{mb_label object=$source field="type_soap"}}</th>
              <td>{{mb_field object=$source field="type_soap"}}</td>
            </tr>

            <tr>
              <th>{{mb_label object=$source field="soap_version"}}</th>
              <td>{{mb_field object=$source field="soap_version"}}</td>
            </tr>

            <tr>
              <th>{{mb_label object=$source field="encoding"}}</th>
              <td>{{mb_field object=$source field="encoding"}}</td>
            </tr>

            <tr>
              <th>{{mb_label object=$source field="type_echange"}}</th>
              <td>{{mb_field object=$source field="type_echange"}}</td>
            </tr>

            <tr>
              <th>{{mb_label object=$source field="evenement_name"}}</th>
              <td>{{mb_field object=$source field="evenement_name"}}</td>
            </tr>

            <tr>
              <th>{{mb_label object=$source field="single_parameter"}}</th>
              <td>{{mb_field object=$source field="single_parameter"}}</td>
            </tr>

            <tr>
              <th>{{mb_label object=$source field="safe_mode"}}</th>
              <td>{{mb_field object=$source field="safe_mode"}}</td>
            </tr>

            <tr>
              <th>{{mb_label object=$source field="return_raw"}}</th>
              <td>{{mb_field object=$source field="return_raw"}}</td>
            </tr>
          </table>
        </fieldset>

        <fieldset>
          <legend>Authentification HTTP</legend>

          <table class="main form">
            <tr>
              <th style="width: 100px">{{mb_label object=$source field="user"}}</th>
              <td>{{mb_field object=$source field="user"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="password"}}</th>
              {{assign var=placeholder value="Pas de mot de passe"}}
              {{if $source->password}}
                {{assign var=placeholder value="Mot de passe enregistré"}}
              {{/if}}
              <td>{{mb_field object=$source field="password" placeholder=$placeholder}}</td>
            </tr>
          </table>
        </fieldset>

        <fieldset>
          <legend>Authentification à l'aide d'un certificat</legend>

          <table class="main form">
            <tr>
              <th style="width: 100px">{{mb_label object=$source field="local_cert"}}</th>
              <td>{{mb_field object=$source field="local_cert" size="50"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="passphrase"}}</th>
              {{assign var=placeholder value="Pas de phrase de passe"}}
              {{if $source->passphrase}}
                {{assign var=placeholder value="Phrase de passe enregistrée"}}
              {{/if}}
              <td>{{mb_field object=$source field="passphrase" placeholder=$placeholder size="30"}}</td>
            </tr>
          </table>
        </fieldset>

        <fieldset>
          <legend>Options de contexte SSL</legend>

          <table class="main form">
            <tr>
              <th style="width: 100px">{{mb_label object=$source field="verify_peer"}}</th>
              <td>{{mb_field object=$source field="verify_peer"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="cafile"}}</th>
              <td>{{mb_field object=$source field="cafile" size="50"}}</td>
            </tr>
          </table>
        </fieldset>

        <table class="main form">
          <tr>
            <td class="button" colspan="2">
              {{if $source->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" onclick="confirmDeletion(this.form, {ajax:1, typeName:'',
                  objName:'{{$source->_view|smarty:nodefaults|JSAttribute}}'}, 
                  {onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}')})">
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
  </tr>
</table>

<hr/>

<table class="main layout">
  <tr>
    <td>
      <script type="text/javascript">
        SOAP = {
          connexion: function (exchange_source_name) {
            var url = new Url("webservices", "ajax_connexion_soap");
            url.addParam("exchange_source_name", exchange_source_name);
            url.requestUpdate("utilities-source-soap-connexion-" + exchange_source_name);
          },

          getFunctions: function (exchange_source_name) {
            var url = new Url("webservices", "ajax_getFunctions_soap");
            url.addParam("exchange_source_name", exchange_source_name);
            url.requestUpdate("utilities-source-soap-getFunctions-" + exchange_source_name);
          }
        }
      </script>
      <table class="main tbl">
        <tr>
          <th class="category" colspan="100">
            {{tr}}utilities-source-soap{{/tr}}
          </th>
        </tr>

        <!-- Test connexion SOAP -->
        <tr>
          <td>
            <button type="button" class="search" onclick="SOAP.connexion('{{$source->name}}');">
              {{tr}}utilities-source-soap-connexion{{/tr}}
            </button>
          </td>
        </tr>
        <tr>
          <td id="utilities-source-soap-connexion-{{$source->name}}" class="text"></td>
        </tr>

        <!-- Liste des functions SOAP -->
        <tr>
          <td>
            <button type="button" class="search" onclick="SOAP.getFunctions('{{$source->name}}');">
              {{tr}}utilities-source-soap-getFunctions{{/tr}}
            </button>
          </td>
        </tr>
        <tr>
          <td id="utilities-source-soap-getFunctions-{{$source->name}}" class="text"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>