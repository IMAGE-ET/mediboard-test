{{*
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_include module=system template=inc_pagination change_page="changePage" total=$total current=$start step=30}}

{{mb_include module=tasking template=inc_show_multiple_actions}}

{{if $total_estimation}}
  <div class="text">
    {{foreach from=$estimations key=_type item=_part}}
      <div class="text compact type_{{$_type}}" style="width: {{$_part.part}}%; display: inline-block;  float: left;" title="{{$_type}} : {{$_part.count}}h/{{$total_estimation}}h, soit {{$_part.part|round}}% du temps estimé.">
        &nbsp;
      </div>
    {{/foreach}}
  </div>
{{/if}}

<table class="main tbl">
  <tr>
    <th><input type="checkbox" name="allTasks" onchange="Tasking.selectAllTasks(this.checked);"/></th>
    <th>{{mb_colonne class="CTaskingTicket" field="priority" order_col=$order_col order_way=$order_way function="orderBy" label="!"}}</th>
    <th>{{mb_colonne class="CTaskingTicket" field="ticket_name" order_col=$order_col order_way=$order_way function="orderBy"}}</th>
    <th>{{mb_title class=CTaskingTicket field=type}}</th>
    <th>{{mb_title class=CTaskingTicket field=status}}</th>
    <th>{{* {{tr}}CTaskingTicket-messages{{/tr}} *}}</th>
    <th>{{$total_estimation}}h</th>
    <th>{{mb_colonne class="CTaskingTicket" field="due_date" order_col=$order_col order_way=$order_way function="orderBy"}}</th>
  </tr>
  {{foreach from=$tasking_tickets item=_tasking_ticket}}
    <tbody>
      {{mb_include module=tasking template=inc_show_tasking_ticket_line}}
    </tbody>
    {{foreachelse}}
    <tr>
      <td class="empty" colspan="8">{{tr}}CTaskingTicket.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>