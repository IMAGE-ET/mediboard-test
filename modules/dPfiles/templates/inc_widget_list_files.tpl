<script>
  Main.add(function() {
    if (window.updateCountTab) {
      updateCountTab();
    }
  });
</script>

{{foreach from=$object->_ref_files item=_file}}
  {{assign var=object_class value=$object->_class}}
  {{assign var=object_id    value=$object->_id}}

  <tr id="tr_{{$_file->_guid}}">
    <td id="td_{{$_file->_guid}}">
      {{mb_include template="inc_widget_line_file"}}
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

{{if $object->_ref_hypertext_links}}
  {{mb_include module=sante400 template=inc_widget_list_hypertext_links}}
{{/if}}

