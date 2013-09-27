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

<script type="text/javascript">
  Main.add(function() {
    var form    = getForm("edit-tasking_ticket");

    // Autocomplete des users "assigned to"
    var element_assigned_to = form.elements._view_assigned_to;
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("object_class", "CMediusers");
    url.addParam("input_field", element_assigned_to.name);
    url.autoComplete(element_assigned_to, null, {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        var id = selected.get("id");
        $V(form.elements.assigned_to_id, id);
        if ($V(element_assigned_to) == "") {
          $V(element_assigned_to, selected.down('.view').innerHTML);
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

    var element_tag = form.elements._bind_tag_view;
    var url     = new Url("system", "ajax_seek_autocomplete");

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

        Tasking.bindTag(form, id, "addTag", color, name);
        $V(element_tag, "");
      }
    });

    Tasking.listTaskingMessages($V(form.elements.tasking_ticket_id));
  });
</script>

<form name="edit-tasking_ticket" action="" method="post" onsubmit="return Tasking.submitTaskingTicket(this)">
  {{mb_key object=$tasking_ticket}}
  <input type="hidden" name="m" value="tasking" />
  <input type="hidden" name="dosql" value="do_tasking_ticket_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="_duplicate" value="" />
  <input type="hidden" name="start" value="0" />
  <input type="hidden" name="supervisor_id" value="" />
  <input type="hidden" name="assigned_to_id" value="{{$tasking_ticket->assigned_to_id}}" />
  <input type="hidden" name="tags_to_add" value="" />
  <input type="hidden" name="tags_to_remove" value="" />
  <input type="hidden" name="callback" value="" />
  {{mb_field object=$tasking_ticket field=creation_date          hidden=true}}
  {{mb_field object=$tasking_ticket field=last_modification_date hidden=true}}
  {{mb_field object=$tasking_ticket field=closing_date           hidden=true}}
  {{mb_field object=$tasking_ticket field=nb_postponements       hidden=true}}

  <table class="main form">
    <tr>
      <th class="narrow">{{mb_label object=$tasking_ticket field=ticket_name}}</th>
      <td colspan="4">{{mb_field object=$tasking_ticket field=ticket_name style="width:100%; box-sizing: border-box;"}}</td>
    </tr>
    <tr>
      <th class="narrow">{{mb_title object=$tasking_ticket field=creation_date}}</th>
      <td>
        {{$tasking_ticket->creation_date|rel_datetime}}
        ({{mb_value object=$tasking_ticket field=creation_date}})
      </td>

      <th class="narrow">{{mb_label object=$tasking_ticket field=last_modification_date}}</th>
      <td>
        {{$tasking_ticket->last_modification_date|rel_datetime}}
        ({{mb_value object=$tasking_ticket field=last_modification_date}})
      </td>
    </tr>
    <tr>
      <th class="narrow">{{mb_label object=$tasking_ticket field=due_date}}</th>
      <td {{if !$tasking_ticket->closing_date}}colspan="4"{{/if}}>{{mb_field object=$tasking_ticket field=due_date register=true form="edit-tasking_ticket" prop=dateTime}}</td>

      {{if $tasking_ticket->closing_date}}
        <th class="narrow">{{mb_label object=$tasking_ticket field=closing_date}}</th>
        <td>
          {{$tasking_ticket->closing_date|rel_datetime}}
          ({{mb_value object=$tasking_ticket field=closing_date}})
        </td>
      {{/if}}
    </tr>
    <tr>
      <th class="narrow">{{mb_label object=$tasking_ticket field=priority}}</th>
      <td>
        {{mb_field object=$tasking_ticket field=priority typeEnum="radio"}}
      </td>

      <th class="narrow">{{mb_label object=$tasking_ticket field=nb_postponements}}</th>
      <td>
        {{mb_value object=$tasking_ticket field=nb_postponements}}
      </td>
    </tr>
    <tr>
      {{mb_include module=tasking template=inc_edit_tasking_ticket_estimate}}
    </tr>
    <tr>
      <th class="narrow">{{mb_label object=$tasking_ticket field=assigned_to_id}}</th>
      <td {{if !$tasking_ticket->duplicate_of_id}}colspan="4"{{/if}}>
        <input type="text" name="_view_assigned_to" class="autocomplete"
               value="{{if $tasking_ticket->assigned_to_id}}{{$tasking_ticket->_ref_assigned_to_user}}{{else}}&mdash; Tous les utilisateurs{{/if}}" />
        <button type="button" class="cancel notext" onclick="$V(this.form.elements.assigned_to_id, ''); $V(this.form.elements._view_assigned_to, '');"></button>
      </td>

      {{if $tasking_ticket->duplicate_of_id}}
        <th class="narrow">{{mb_label object=$tasking_ticket field=duplicate_of_id}}</th>
        <td>{{mb_value object=$tasking_ticket field=duplicate_of_id tooltip=true}}</td>
      {{/if}}
    </tr>
    <tr>
      <th class="narrow">{{mb_label object=$tasking_ticket field=supervisor_id}}</th>
      <td>
        <input type="text" name="_view_supervisor" class="autocomplete"
               value="{{if $tasking_ticket->supervisor_id}}{{$tasking_ticket->_ref_supervisor_user}}{{else}}&mdash; Tous les utilisateurs{{/if}}" />
        <button type="button" class="cancel notext" onclick="$V(this.form.elements.supervisor_id, ''); $V(this.form.elements._view_supervisor, '');"></button>
      </td>

      <th class="narrow">{{tr}}Bill{{/tr}}</th>
      <td>
        <div style="text-align: left;">
          {{mb_field object=$tasking_ticket field=bill_id autocomplete="true,1,50,true,true" form="edit-tasking_ticket"}}
        </div>
      </td>
    </tr>
    <tr>
      <th class="narrow">{{mb_label object=$tasking_ticket field=status}}</th>
      <td>
        {{mb_field object=$tasking_ticket field=status typeEnum="radio"}}
      </td>

      <th class="narrow">{{mb_label object=$tasking_ticket field=type}}</th>
      <td>
        {{mb_field object=$tasking_ticket field=type typeEnum="radio"}}
      </td>
    </tr>
    <tr>
      <th class="narrow">
        <button style="float: none;" class="tag-edit" type="button" onclick="Tag.manage('CTaskingTicket')">
          {{tr}}CTaskingTicket-Tags{{/tr}}
        </button>

        <div style="display: inline-block; text-align: left;">
          <input type="text" name="_bind_tag_view" class="autocomplete" size="13" />
        </div>
      </th>

      {{mb_include module=tasking template=inc_edit_tasking_ticket_tags}}

      <th class="narrow">{{mb_label object=$tasking_ticket field=funding}}</th>
      <td>
        {{mb_field object=$tasking_ticket field=funding typeEnum="radio"}}
      </td>
    </tr>
    <tr>
      {{mb_include module=tasking template=inc_edit_tasking_ticket_actions}}
    </tr>
  </table>
</form>

<div id="tasking-messages"></div>
