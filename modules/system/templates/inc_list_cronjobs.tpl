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

{{assign var=now value=$dtnow|date_format:"%d-%m-%Y"}}

{{foreach from=$cronjobs item=_cronjob}}
  <tr {{if !$_cronjob->active}}class="opacity-30"{{/if}}>
    <td class="narrow">
      <form name="editactive_{{$_cronjob->_id}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this, CronJob.ChangeActive(this))">
        {{mb_class object=$_cronjob}}
        {{mb_key object=$_cronjob}}
        {{mb_field object=$_cronjob field="active" canNull=true onchange="this.form.onsubmit()"}}
      </form>
    </td>
    <td class="narrow">
      <button class="edit notext compact" type="button" onclick="CronJob.edit('{{$_cronjob->_id}}')">{{tr}}Modify{{/tr}}</button>
      {{mb_value object=$_cronjob field="name"}}
    </td>
    <td class="text compact">{{mb_value object=$_cronjob field="description"}}</td>
    <td>{{mb_value object=$_cronjob field="params"}}</td>
    <td style="font-family: monospace">{{mb_value object=$_cronjob field="execution"}}</td>
    <td>
      {{if $_cronjob->servers_address}}
        {{mb_value object=$_cronjob field="servers_address"}}
      {{else}}
        {{tr}}All{{/tr}}
      {{/if}}
    </td>
    {{foreach from=$_cronjob->_next_datetime item=_next_datetime}}
      <td style="text-align: right">
        {{if $_next_datetime|date_format:"%d-%m-%Y" == $now}}
          {{$_next_datetime|date_format:"%H:%M:%S"}}
        {{else}}
          {{$_next_datetime|date_format:"%d/%m/%Y %H:%M:%S"}}
        {{/if}}
      </td>
    {{foreachelse}}
      <td class="narrow"></td>
      <td class="narrow"></td>
      <td class="narrow"></td>
      <td class="narrow"></td>
      <td class="narrow"></td>
    {{/foreach}}
  </tr>
  {{foreachelse}}
  <tr>
    <td class="empty" colspan="10">{{tr}}CCronJob.none{{/tr}}</td>
  </tr>
{{/foreach}}