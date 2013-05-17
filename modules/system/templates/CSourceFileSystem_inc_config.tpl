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
      <form name="editSourceFileSystem-{{$source->name}}" action="?m={{$m}}" method="post" 
        onsubmit="return onSubmitFormAjax(this, { onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}') } )">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_source_file_system_aed" />
        <input type="hidden" name="source_file_system_id" value="{{$source->_id}}" />
        <input type="hidden" name="del" value="0" /> 
           
        <table class="form">        
          <tr>
            <th class="category" colspan="2">
              {{tr}}config-source-file_system{{/tr}}
            </th>
          </tr>
          
          {{mb_include module=system template=CExchangeSource_inc}}
          
          <tr>
            <th>{{mb_label object=$source field="fileprefix"}}</th>
            <td>{{mb_field object=$source field="fileprefix"}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$source field="fileextension"}}</th>
            <td>{{mb_field object=$source field="fileextension"}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$source field="fileextension_write_end"}}</th>
            <td>{{mb_field object=$source field="fileextension_write_end"}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$source field="sort_files_by"}}</th>
            <td>{{mb_field object=$source field="sort_files_by"}}</td>
          </tr>

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
    <td class="greedyPane">
      <script type="text/javascript">
        FileSystem = {
          connexion: function (exchange_source_name) {
            var url = new Url("system", "ajax_tests_file_system");
            url.addParam("exchange_source_name", exchange_source_name);
            url.addParam("type_action", "connexion");
            url.requestUpdate("utilities-source-file_system-connexion-" + exchange_source_name);
          },

          sendFile: function (exchange_source_name) {
            var url = new Url("system", "ajax_tests_file_system");
            url.addParam("exchange_source_name", exchange_source_name);
            url.addParam("type_action", "sendFile");
            url.requestUpdate("utilities-source-file_system-sendFile-" + exchange_source_name);
          },
        
          getFiles: function (exchange_source_name) {
            var url = new Url("system", "ajax_tests_file_system");
            url.addParam("exchange_source_name", exchange_source_name);
            url.addParam("type_action", "getFiles");
            url.requestUpdate("utilities-source-file_system-getFiles-" + exchange_source_name);
          }
        }
      </script>
      <table class="tbl">
        <tr>
          <th class="category" colspan="100">
            {{tr}}utilities-source-smtp{{/tr}}
          </th>
        </tr>
        
        <!-- Test de connexion -->
        <tr>
          <td>
            <button type="button" class="search" onclick="FileSystem.connexion('{{$source->name}}');">
              {{tr}}utilities-source-file_system-connexion{{/tr}}
            </button>
          </td>
          <td id="utilities-source-file_system-connexion-{{$source->name}}" class="text"></td>
        </tr>
        
        <!-- Dépôt d'un fichier -->
        <tr>
          <td>
            <button type="button" class="search" onclick="FileSystem.sendFile('{{$source->name}}');">
              {{tr}}utilities-source-file_system-sendFile{{/tr}}
            </button> 
          </td>
          <td id="utilities-source-file_system-sendFile-{{$source->name}}" class="text"></td>
        </tr>
        
        <!-- Liste des fichiers -->
        <tr>
          <td>
            <button type="button" class="search" onclick="FileSystem.getFiles('{{$source->name}}');">
              {{tr}}utilities-source-file_system-getFiles{{/tr}}
            </button> 
          </td>
          <td id="utilities-source-file_system-getFiles-{{$source->name}}" class="text"></td>
        </tr>
        <tr>
          <td>
            <button type="button" class="search" onclick="ExchangeSource.manageFiles('{{$source->_guid}}');">
              {{tr}}utilities-source-file_system-manageFiles{{/tr}}
            </button>
          </td>
          <td id="utilities-source-file_system-manageFiles-{{$source->name}}" class="text"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>