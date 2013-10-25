{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=ftp script=action_ftp ajax=true}}
{{mb_script module=system script=exchange_source ajax=true}}

<table class="main">
  <tr>
    <td>
      <form name="editSourceSFTP-{{$source->name}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}') } )">
        <input type="hidden" name="m" value="ftp" />
        {{mb_class object=$source}}
        {{mb_key object=$source}}

        <fieldset>
          <legend>{{tr}}CSourceSFTP{{/tr}}</legend>

          <table class="form">
            {{mb_include module=system template=CExchangeSource_inc}}

            <tr>
              <th style="width: 120px">{{mb_label object=$source field="user"}}</th>
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
            <tr>
              <th>{{mb_label object=$source field="port"}}</th>
              <td>{{mb_field object=$source field="port"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="timeout"}}</th>
              <td>{{mb_field object=$source field="timeout"}}</td>
            </tr>
          </table>
        </fieldset>


        <fieldset>
          <legend>{{tr}}CSourceFTP-manage_files{{/tr}}</legend>

          <table class="main form">
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