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

{{mb_include module=system template=inc_pagination total=$nb_log current=$page change_page="CronJob.changePageLog" step=30}}
<table class="tbl">
  <tr>
    <th>{{mb_title class=CCronJobLog field="status"}}</th>
    <th>{{mb_title class=CCronJobLog field="error"}}</th>
    <th>{{mb_title class=CCronJobLog field="cronjob_id"}}</th>
    <th>{{mb_title class=CCronJobLog field="start_datetime"}}</th>
    <th>{{mb_title class=CCronJobLog field="end_datetime"}}</th>
    <th>{{mb_title class=CCronJobLog field="_duration"}}</th>
  </tr>
  {{foreach from=$logs item=_log}}
    <tr>
      <td class="narrow statusCron_{{$_log->status}}">{{mb_value object=$_log field="status"}}</td>
      <td>{{mb_value object=$_log field="error"}}</td>
      <td>
        {{if $_log->_ref_cronjob}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_log->_ref_cronjob->_guid}}');">{{$_log->_ref_cronjob->_view}}</span>
        {{/if}}
      </td>
      <td>{{mb_value object=$_log field="start_datetime"}}</td>
      <td>{{mb_value object=$_log field="end_datetime"}}</td>
      <td>{{mb_value object=$_log field="_duration"}}</td>
    </tr>
  {{foreachelse}}
    <tr><td class="empty" colspan="6">{{tr}}CCronJobLog.none{{/tr}}</td></tr>
  {{/foreach}}
</table>