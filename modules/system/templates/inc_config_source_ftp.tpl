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
      <form name="editSourceFTP" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: refreshExchangeSource.curry('{{$exchange_source_name}}') } )">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_source_ftp_aed" />
        <input type="hidden" name="source_ftp_id" value="{{$object->_id}}" />
        <input type="hidden" name="del" value="0" /> 
        <input type="hidden" name="name" value="{{$exchange_source_name}}" /> 
           
        <table class="form">        
          <tr>
            <th class="category" colspan="100">
              {{tr}}config-source-ftp{{/tr}}
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
            <th>{{mb_label object=$object field="port"}}</th>
            <td>{{mb_field object=$object field="port"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="timeout"}}</th>
            <td>{{mb_field object=$object field="timeout"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="pasv"}}</th>
            <td>{{mb_field object=$object field="pasv"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="mode"}}</th>
            <td>{{mb_field object=$object field="mode"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="counter"}}</th>
            <td>{{mb_field object=$object field="counter"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="fileprefix"}}</th>
            <td>{{mb_field object=$object field="fileprefix"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="fileextension"}}</th>
            <td>{{mb_field object=$object field="fileextension"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="filenbroll"}}</th>
            <td>{{mb_field object=$object field="filenbroll"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$object field="fileextension_write_end"}}</th>
            <td>{{mb_field object=$object field="fileextension_write_end"}}</td>
          </tr>
          
          <tr>
            <td class="button" colspan="2">
              {{if $object->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$object->_view|smarty:nodefaults|JSAttribute}}'})">
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
        var FTP = {
          connexion: function (exchange_source_name) {
            var url = new Url("system", "ajax_connexion_ftp");
            url.addParam("exchange_source_name", exchange_source_name);
            url.requestUpdate("utilities-source-ftp-connexion-" + exchange_source_name);
          },
          
          getFiles: function (exchange_source_name) {
            var url = new Url("system", "ajax_getFiles_ftp");
            url.addParam("exchange_source_name", exchange_source_name);
            url.requestUpdate("utilities-source-ftp-getFiles-" + exchange_source_name);
          }
        }
      </script>
      <table class="tbl">
        <tr>
          <th class="category" colspan="100">
            {{tr}}utilities-source-ftp{{/tr}}
          </th>
        </tr>
        
        <!-- Test connexion FTP -->
        <tr>
          <td>
            <button type="button" class="search" onclick="FTP.connexion('{{$exchange_source_name}}');">
              {{tr}}utilities-source-ftp-connexion{{/tr}}
            </button>
          </td>
          <td id="utilities-source-ftp-connexion-{{$exchange_source_name}}" />
        </tr>
        
        <!-- Liste des fichiers -->
        <tr>
          <td>
            <button type="button" class="search" onclick="FTP.getFiles('{{$exchange_source_name}}');">
              {{tr}}utilities-source-ftp-getFiles{{/tr}}
            </button> 
          </td>
          <td id="utilities-source-ftp-getFiles-{{$exchange_source_name}}" />
        </tr>
      </table>
    </td>
  </tr>
</table>