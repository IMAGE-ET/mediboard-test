{{* $Id: configure.tpl 20011 2013-07-23 10:51:17Z flaviencrochard $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 20011 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=urgences script=rpu_sender}}

<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create("tabs_configure", false);

    Control.Tabs.create("tabs_configure_source", false);

    {{if !$writable}}
      $('import-key').disabled = true;
    {{/if}}
  });
</script>

<ul id="tabs_configure" class="control_tabs">
  <li> <a href="#Config_RPU_sender">{{tr}}Config_RPU_Sender{{/tr}}</a> </li>
  <li> <a href="#RPU_sender_source">{{tr}}RPU_sender_source{{/tr}}</a> </li>
</ul>

<hr class="control_tabs" />

<div id="Config_RPU_sender" style="display: none">
  <form name="editConfigOpale" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
    <input type="hidden" name="dosql" value="do_configure" />
    <input type="hidden" name="m" value="system" />
    <table class="form">
      <tr>
        <th class="category" colspan="10">{{tr}}config-dPurgences-chiffrement{{/tr}}</th>
      </tr>
      {{assign var="var" value="gnupg_path"}}
      <tr>
        <th><label for="{{$m}}[{{$var}}]" title="{{tr}}config-dPurgences-{{$var}}{{/tr}}">
            {{tr}}config-dPurgences-{{$var}}{{/tr}}
          </label></th>
        <td>
          <input class="str" name="{{$m}}[{{$var}}]" value="{{$conf.$m.$var}}">
          {{if $path !== "~"}}
            {{tr}}Default{{/tr}} : {{$home}}
          {{/if}}
        </td>
      </tr>

      {{assign var="var" value="pattern_keyinfo"}}
      <tr>
        <th>
          <label for="{{$m}}[{{$var}}]" title="{{tr}}config-dPurgences-{{$var}}{{/tr}}">
            {{tr}}config-dPurgences-{{$var}}{{/tr}}
          </label>
        </th>
        <td>
          <input class="str" name="{{$m}}[{{$var}}]" value="{{$conf.$m.$var}}" />
        </td>
      </tr>

      <tr>
        <th class="button">
          <button class="hslip" id="import-key" onclick="return RPU_Sender.popupImport('{{$m}}');" type="submit">
            {{tr}}config-dPurgences-import-key{{/tr}}
          </button>
        </th>
        <td id="import_key">
          {{if !$writable}}
            <div class="big-error">
              Le dossier '{{$home}}' n'est pas autorisé en écriture. <br />
              Il est nécessaire de réaliser les manipulations suivantes : <br />
              <pre>$ mkdir {{$home}} <br />$ chown {{$user_apache}} {{$home}}</pre>
            </div>
          {{/if}}
        </td>
      </tr>
      <tr>
        <th class="button">
          <button class="lookup" onclick="return RPU_Sender.showEncryptKey();" type="button">
            {{tr}}config-dPurgences-show-encrypt-key{{/tr}}
          </button>
        </td>
        <td id="show_encrypt_key">

        </td>
      </tr>

      <tr>
        <th class="category" colspan="10">{{tr}}config-dPurgences-extraction{{/tr}}</th>
      </tr>

      {{assign var="var" value="mode_procedure"}}
      <tr>
        <th>
          <label for="{{$m}}[{{$var}}]" title="{{tr}}config-dPurgences-{{$var}}{{/tr}}">
            {{tr}}config-dPurgences-{{$var}}{{/tr}}
          </label>
        </th>
        <td class="text">
          <select class="str" name="{{$m}}[{{$var}}]">
            <option value="">&mdash; Mode procédure &mdash;</option>
            <option value="auto" {{if $conf.$m.$var == "auto"}} selected="selected" {{/if}}>{{tr}}config-dPurgences-{{$var}}-auto{{/tr}}</option>
            <option value="manu" {{if $conf.$m.$var == "manu"}} selected="selected" {{/if}}>{{tr}}config-dPurgences-{{$var}}-manu{{/tr}}</option>
          </select>
        </td>
      </tr>

      {{assign var="var" value="heure_activation"}}
      <tr>
        <th>
          <label for="{{$m}}[{{$var}}]" title="{{tr}}config-dPurgences-{{$var}}{{/tr}}">
            {{tr}}config-dPurgences-{{$var}}{{/tr}}
          </label>
        </th>
        <td>
          <input class="str" name="{{$m}}[{{$var}}]" value="{{$conf.$m.$var}}" />
        </td>
      </tr>

      {{mb_include module=$m template="Config_RPU_Sender_inc"}}

      <tr>
        <td class="button" colspan="10">
          <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</div>

<div id="RPU_sender_source" style="display: none">
  <table class="main">
    <tr>
      <td style="width: 10%">
        <ul class="control_tabs_vertical" id="tabs_configure_source" style="white-space: nowrap;">
          <li><a href="#config_source">{{tr}}config_source{{/tr}}</a></li>
          <li><a href="#config_source_rescue">{{tr}}config_source_rescue{{/tr}}</a></li>
        </ul>
      </td>
      <td style="width: 90%">
        <div id="config_source">
          <table class="form">
            <tr>
              <th class="title">
                {{tr}}config-exchange-source{{/tr}}
              </th>
            </tr>
            <tr>
              <td> {{mb_include module=system template=inc_config_exchange_source source=$source}} </td>
            </tr>
          </table>
        </div>
        <div id="config_source_rescue">
          <table class="form">
            <tr>
              <th class="title">
                {{tr}}config-exchange-source-rescue{{/tr}}
              </th>
            </tr>
            <tr>
              <td>{{mb_include module=system template=inc_config_exchange_source source=$source_rescue}}</td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  </table>
</div>