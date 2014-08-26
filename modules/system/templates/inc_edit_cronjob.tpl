{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  Main.add(function() {
    var form = getForm("editcronjob");
    CronJob.changeField(form._frequently)
  });
</script>

<form name="editcronjob" method="post" action="?" onsubmit="return onSubmitFormAjax(this, Control.Modal.close)">
  {{mb_class object=$cronjob}}
  {{mb_key object=$cronjob}}
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="del" value="0" />
  <table class="form">
    <tr>
      {{if $cronjob->_id}}
        <th class="title modify text" colspan="2">
          {{mb_include module=system template=inc_object_idsante400 object=$cronjob}}
          {{mb_include module=system template=inc_object_history object=$cronjob}}

          {{tr}}{{$cronjob->_class}}-title-modify{{/tr}} '{{$cronjob}}'
      {{else}}
        <th class="title" colspan="2">
          {{tr}}{{$cronjob->_class}}-title-create{{/tr}}
      {{/if}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="name"}}</th>
      <td>{{mb_field object=$cronjob field="name"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="description"}}</th>
      <td>{{mb_field object=$cronjob field="description"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="active"}}</th>
      <td>{{mb_field object=$cronjob field="active"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="params"}}</th>
      <td>{{mb_field object=$cronjob field="params"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="cron_login"}}</th>
      <td>{{mb_field object=$cronjob field="cron_login"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="cron_password"}}</th>
      <td>{{mb_field object=$cronjob field="cron_password"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="servers_address"}}</th>
      <td>
        {{mb_field object=$cronjob field="servers_address"}}<br/>
        <div style="width: 250px;">
          {{foreach from=$address item=_address}}
            <label style="display: block; float: left; padding-right: 5px;">
              <input type="checkbox" name="address" value="{{$_address}}"
                    onclick="CronJob.setServerAddress(this)" {{if in_array($_address, $cronjob->_servers)}}checked{{/if}}>{{$_address}}
            </label>
          {{/foreach}}
        </div>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="execution" canNull=true}}</th>
      <td>{{mb_value object=$cronjob field="execution"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="_frequently"}}</th>
      <td>{{mb_field object=$cronjob field="_frequently" emptyLabel="Choose" onchange="CronJob.changeField(this)"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="_second"}}</th>
      <td>{{mb_field object=$cronjob field="_second" placeholder="0"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="_minute"}}</th>
      <td>{{mb_field object=$cronjob field="_minute" placeholder="*"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="_hour"}}</th>
      <td>{{mb_field object=$cronjob field="_hour" placeholder="*"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="_day"}}</th>
      <td>{{mb_field object=$cronjob field="_day" placeholder="*"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="_month"}}</th>
      <td>{{mb_field object=$cronjob field="_month" placeholder="*"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$cronjob field="_week"}}</th>
      <td>{{mb_field object=$cronjob field="_week" placeholder="*"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        {{if $cronjob->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button"
                  onclick="confirmDeletion(this.form, null, {onComplete: Control.Modal.close})">{{tr}}Delete{{/tr}}</button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>