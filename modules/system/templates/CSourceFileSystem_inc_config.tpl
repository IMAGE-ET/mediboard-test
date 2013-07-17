{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  FileSystem = {
    connexion: function (exchange_source_name) {
      var url = new Url("system", "ajax_tests_file_system");
      url.addParam("exchange_source_name", exchange_source_name);
      url.addParam("type_action", "connexion");
      url.requestModal(500, 400);
    },

    sendFile: function (exchange_source_name) {
      var url = new Url("system", "ajax_tests_file_system");
      url.addParam("exchange_source_name", exchange_source_name);
      url.addParam("type_action", "sendFile");
      url.requestModal(500, 400);
    },

    getFiles: function (exchange_source_name) {
      var url = new Url("system", "ajax_tests_file_system");
      url.addParam("exchange_source_name", exchange_source_name);
      url.addParam("type_action", "getFiles");
      url.requestModal(500, 400);
    }
  }
</script>

<table class="main"> 
  <tr>
    <td>
      <form name="editSourceFileSystem-{{$source->name}}" action="?m={{$m}}" method="post" 
        onsubmit="return onSubmitFormAjax(this, { onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}') } )">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_source_file_system_aed" />
        <input type="hidden" name="source_file_system_id" value="{{$source->_id}}" />
        <input type="hidden" name="del" value="0" />

        <fieldset>
          <legend>{{tr}}CSourceFileSystem{{/tr}}</legend>

          <table class="form">
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

        <fieldset>
          <legend>{{tr}}utilities-source-soap{{/tr}}</legend>

          <table class="main form">
            <tr>
              <td class="button">
                <!-- Test de connexion -->
                <button type="button" class="search" onclick="FileSystem.connexion('{{$source->name}}');"
                        {{if !$source->_id}}disabled{{/if}}>
                  {{tr}}utilities-source-file_system-connexion{{/tr}}
                </button>

                <!-- Dépôt d'un fichier -->
                <button type="button" class="search" onclick="FileSystem.sendFile('{{$source->name}}');"
                        {{if !$source->_id}}disabled{{/if}}>
                  {{tr}}utilities-source-file_system-sendFile{{/tr}}
                </button>

                <!-- Liste des fichiers -->
                <button type="button" class="search" onclick="FileSystem.getFiles('{{$source->name}}');"
                        {{if !$source->_id}}disabled{{/if}}>
                  {{tr}}utilities-source-file_system-getFiles{{/tr}}
                </button>
              </td>
            </tr>
          </table>
        </fieldset>
      </form>
    </td>
  </tr>
</table>