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
    var form = getForm("search-tickets");

    // Autocomplete des users "assigned to"
    var element_assigned = form.elements._view_assigned_to;
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("object_class", "CMediusers");
    url.addParam("input_field", element_assigned.name);
    url.autoComplete(element_assigned, null, {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        console.log();
        var id = selected.get("id");
        $V(form.elements.assigned_to_id, id);
        if ($V(element_assigned) == "") {
          $V(element_assigned, selected.down('.view').innerHTML);
        }
      }
    });

    // Autocomplete des users "supervisors"
    var element_supervisor = form.elements._view_supervisor;
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("object_class", "CMediusers");
    url.addParam("input_field", element_supervisor.name);
    url.autoComplete(element_supervisor, null, {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        var id = selected.get("id");
        $V(form.elements.supervisor_id, id);
        if ($V(element_supervisor) == "") {
          $V(element_supervisor, selected.down('.view').innerHTML);
        }
      }
    });

    // Autocomplete des étiquettes
    var element_tag = form.elements._bind_tag_view;
    var url = new Url("system", "ajax_seek_autocomplete");

    url.addParam("object_class", "CTag");
    url.addParam("input_field", element_tag.name);
    url.addParam("where[object_class]", "CTaskingTicket");

    var autocompleter = url.autoComplete(element_tag, null, {
      minChars: 3,
      width: "250px",
      method: "get",
      dropdown: true,
      updateElement: function(selected) {
        autocompleter.options.afterUpdateElement(autocompleter.element, selected);
      },
      afterUpdateElement: function(field, selected) {
        var id = selected.get("id");

        var color = selected.down(0).getStyle("background");
        var name  = selected.down(1).getText();

        Tasking.bindTag(form, id, "addSearchTag", color, name);
        $V(element_tag, "");
      }
    });

    // Auto search
    form.onsubmit();
  });

  function changePage(start) {
    var form = getForm("search-tickets");
    $V(form.elements.start, start);
    form.onsubmit();
  }

  function orderBy(order_col, order_way) {
    var form = getForm("search-tickets");
    $V(form.elements.order_col, order_col);
    $V(form.elements.order_way, order_way);
    form.onsubmit();
  }

  function toggleForm() {
    $('labelFor_search-tickets_priority').up('tr').toggle();
    $('labelFor_search-tickets_creation_date').up('tr').toggle();
    var form = getForm("search-tickets");
    ($V(form.elements.toggle) == "1") ? $V(form.elements.toggle, "0") : $V(form.elements.toggle, "1");
  }
</script>

<div id="tickets">
  <form name="search-tickets" action="" method="get" onsubmit="return Tasking.searchTaskingTickets(this)">
    <input type="hidden" name="start" value="0" />
    <input type="hidden" name="order_col" value="{{$order_col}}" />
    <input type="hidden" name="order_way" value="{{$order_way}}" />
    <input type="hidden" name="supervisor_id" value="" />
    <input type="hidden" name="assigned_to_id" value="{{$ticket->assigned_to_id}}" />
    <input type="hidden" name="toggle" value="{{$toggle}}" />
    <input type="hidden" name="tags_to_search" value="{{$tags_to_search}}" />

    <table class="main layout">
      <tr>
        <td>
          <table class="main form">
            <tr>
              <th>{{tr}}Filter{{/tr}}</th>
              <td>{{mb_field object=$ticket field=ticket_name prop=str style="width:100%; box-sizing: border-box;" onchange="\$V(this.form.elements.start, 0)"}}</td>

              <th>{{mb_label object=$ticket field=assigned_to_id}}</th>
              <td>
                <input type="text" name="_view_assigned_to" class="autocomplete"
                       value="{{if $ticket->assigned_to_id}}{{$ticket->_ref_assigned_to_user}}{{else}}&mdash; Tous les utilisateurs{{/if}}"
                       onchange="\$V(this.form.elements.start, 0)" />
                <button type="button" class="cancel notext" onclick="$V(this.form.elements.assigned_to_id, ''); $V(this.form.elements._view_assigned_to, '');"></button>

                <label for="no-assigned_to">
                  <input type="checkbox" name="no-assigned_to" {{if $no_assigned_to}}checked="checked"{{/if}} />
                  {{tr}}No-one{{/tr}}
                </label>
              </td>
            </tr>
            <tr>
              <th>
                <button style="float: none;" class="tag-edit" type="button" onclick="Tag.manage('CTaskingTicket')">
                  {{tr}}CTaskingTicket-Tags{{/tr}}
                </button>
              </th>
              <td>
                <div style="text-align: left; display: inline-block;">
                  <input type="text" name="_bind_tag_view" class="autocomplete" size="13" onchange="\$V(this.form.elements.start, 0)" />
                </div>
                <ul class="tags" id="search-tags" style="display: inline-block;">
                  {{if $tags|@count}}
                    {{foreach from=$tags item=_tag}}
                      <li data-tag_item_id="{{$_tag->_id}}" id="{{$_tag->_guid}}" style="background-color: #{{$_tag->color}}" class="tag">
                        {{$_tag->name}}
                        <button type="button" class="delete"
                                onclick="Tasking.bindTag(form, $(this).up('li').get('tag_item_id'), 'removeSearchTag');
                                $V(this.form.elements.start, 0);">
                      </li>
                    {{/foreach}}
                  {{/if}}
                </ul>
              </td>

              <th>{{mb_label object=$ticket field=supervisor_id}}</th>
              <td>
                <input type="text" name="_view_supervisor" class="autocomplete"
                       value="{{if $ticket->supervisor_id}}{{$ticket->_ref_supervisor_user}}{{else}}&mdash; Tous les utilisateurs{{/if}}"
                       onchange="\$V(this.form.elements.start, 0)" />
                <button type="button" class="cancel notext" onclick="$V(this.form.elements.supervisor_id, ''); $V(this.form.elements._view_supervisor, '');"></button>

                <label for="no-supervisor">
                  <input type="checkbox" name="no-supervisor" {{if $no_supervisor}}checked="checked"{{/if}} />
                  {{tr}}No-one{{/tr}}
                </label>
              </td>
            </tr>
            <tr>
              <th>{{mb_label object=$ticket field=status}}</th>
              <td>
                {{mb_field object=$ticket field=_status onchange="\$V(this.form.elements.start, 0); Tasking.checkStatus(this);"}}
              </td>

              <th>{{mb_label object=$ticket field=type}}</th>
              <td>
                {{mb_field object=$ticket field=_type onchange="\$V(this.form.elements.start, 0)"}}
              </td>
            </tr>
            {{mb_include module=tasking template=inc_show_tasking_tickets_advanced_search}}
            <tr>
              <td></td>
              <td colspan="3">
                <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
                <button type="button" class="close" onclick="this.form.clear();">{{tr}}Reset{{/tr}}</button>

                <button type="button" class="search" onclick="toggleForm();">{{tr}}Advanced-Search{{/tr}}...</button>

                <label>
                  <input type="checkbox" name="relative" {{if $relative}}checked="checked"{{/if}} />
                  {{tr}}CTaskingTicket-relative-dates{{/tr}}
                </label>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </form>

  {{mb_include module=tasking template=inc_add_tasking_ticket}}

  <div id="tickets-results"></div>
</div>