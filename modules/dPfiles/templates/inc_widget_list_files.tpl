<script>
  Main.add(function() {
    if (window.updateCountTab) {
      updateCountTab();
    }
  });
</script>

{{mb_default var=show_only value=0}}
{{foreach from=$object->_ref_files item=_file}}
  {{assign var=object_class value=$object->_class}}
  {{assign var=object_id    value=$object->_id}}

  <tr id="tr_{{$_file->_guid}}">
    <td id="td_{{$_file->_guid}}">
      {{if $show_only}}
        <a href="#" class="action" id="readonly_{{$_file->_guid}}"
           onclick="File.popup('{{$object_class}}','{{$object_id}}','{{$_file->_class}}','{{$_file->_id}}');"
           onmouseover="ObjectTooltip.createEx(this, '{{$_file->_guid}}', 'objectView')">{{$_file}}</a><br/>
      {{else}}
        {{mb_include template="inc_widget_line_file"}}
      {{/if}}
    </td>
  </tr>
{{foreachelse}}
  <tr>
    <td class="empty">
      {{tr}}{{$object->_class}}{{/tr}} :
      {{tr}}CFile.none{{/tr}}
    </td>
  </tr>
{{/foreach}}

{{mb_default var=show_widget value=1}}
{{if $object->_ref_hypertext_links && $show_widget}}
  {{mb_include module=sante400 template=inc_widget_list_hypertext_links}}
{{/if}}

