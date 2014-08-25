{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th class="title" colspan="3">
      {{tr}}CDataSourceLog-title-crazy-found{{/tr}}
      <br />
      {{mb_label class=CDataSourceLog field=duration}} &gt; {{$ratio}}s
    </th>
  </tr>
  <tr>
    <th>{{mb_label class=CDataSourceLog field=_module}}</th>
    <th>{{mb_label class=CDataSourceLog field=_action}}</th>
    <th>{{tr}}Total{{/tr}}</th>
  </tr>

  {{foreach from=$logs item=_log}}
    <tr>
      <td><strong>{{$_log._module}}</strong></td>
      <td>{{$_log._action}}</td>
      <td>{{$_log.total}}</td>
    </tr>
    {{foreachelse}}
    <tr>
      <td class="empty" colspan="3">{{tr}}CDataSourceLog.none{{/tr}}</td>
    </tr>
  {{/foreach}}

  <tr>
    <td colspan="3" class="button">
      <button class="trash" type="button" onclick="AccessLog.purgeCrazyDataSourceLogs();" {{if !count($logs)}}disabled="true"{{/if}}>
        {{tr}}Purge{{/tr}}
      </button>
    </td>
  </tr>
</table>

{{if $purged_count !== null}}
  <div class="small-success">
    {{tr}}CDataSourceLog-title-crazy-purged{{/tr}} : {{$purged_count}}
  </div>
{{/if}}


