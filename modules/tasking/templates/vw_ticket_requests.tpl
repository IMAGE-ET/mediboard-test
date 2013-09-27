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

{{mb_script module="tasking" script="tasking"}}
{{mb_script module="tasking" script="tasking.message"}}

<script>
  Main.add(function() {

  });

  function searchTicketRequests(form) {
    var url = new Url("tasking", "ajax_list_ticket_results");
    url.addFormData(form);
    url.requestUpdate("ticket_requests-results");

    return false;
  }

  function getObjectsFromClass(selected) {
    console.log(selected);

  }
</script>

<div id="ticket_requests">
  <form name="search-ticket_requests" action="" method="get" onsubmit="return searchTicketRequests(this)">
    <input type="hidden" name="start" value="0" />

    <table class="layout">
      <tr>
        <td>
          <table class="main form">
            <tr>
              <th>{{mb_label object=$ticket field=label}}</th>
              <td>{{mb_field object=$ticket field=label prop=str}}</td>

              <th>{{mb_label object=$ticket field=priority}}</th>
              <td>
                <label>
                  <input type="radio" name="priority" value="0" {{if $ticket->priority == 0}}checked="checked"{{/if}} />
                  {{tr}}CTicketRequest-priority.none{{/tr}}
                </label>
                <label>
                  <input type="radio" name="priority" value="1" {{if $ticket->priority == 1}}checked="checked"{{/if}} />
                  {{tr}}CTicketRequest-priority.high{{/tr}}
                </label>
                <label>
                  <input type="radio" name="priority" value="2" {{if $ticket->priority == 2}}checked="checked"{{/if}} />
                  {{tr}}CTicketRequest-priority.medium{{/tr}}
                </label>
                <label>
                  <input type="radio" name="priority" value="3" {{if $ticket->priority == 3}}checked="checked"{{/if}} />
                  {{tr}}CTicketRequest-priority.low{{/tr}}
                </label>
              </td>
            </tr>
            <tr>
              <th>{{mb_label object=$ticket field=object_class}}</th>
              <td>
                <select name="object_class" onchange="getObjectsFromClass($V(this))">
                  <option value="CMediusers">CMediusers</option>
                  <option value="CTaskingContactEvent">CTaskingContactEvent</option>
                  <option value="CMonitorSite">CMonitorSite</option>
                  <option value="CMonitorGroup">CMonitorGroup</option>
                  <option value="CMonitorFunction">CMonitorFunction</option>
                </select>
              </td>

              <th>{{mb_label object=$ticket field=due_date}}</th>
              <td>{{mb_field object=$ticket field=due_date register=true form="search-ticket_requests" prop=dateTime}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$ticket field=object_id}}</th>
              <td>{{mb_field object=$ticket field=object_id prop=str}}</td>

              <th>Trier par</th>
              <td>
                <select name="order_by">
                  <option value="priority" {{if $order_by == "priority"}} selected {{/if}}>{{tr}}CTicketRequest-priority{{/tr}}</option>
                  <option value="due_date" {{if $order_by == "due_date"}} selected {{/if}}>{{tr}}CTicketRequest-due_date{{/tr}}</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>{{mb_label object=$ticket field=description}}</th>
              <td>{{mb_field object=$ticket field=description}}</td>
            </tr>
            <tr>
              <td></td>
              <td colspan="3">
                <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
                <button type="button" class="close" onclick="this.form.clear();">{{tr}}Reset{{/tr}}</button>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </form>

  <div id="ticket_requests-results"></div>
</div>