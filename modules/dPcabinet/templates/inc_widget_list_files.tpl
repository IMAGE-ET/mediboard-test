<script>
  Main.add(function() {
    if (window.tabsConsult || window.tabsConsultAnesth) {
      var count_items = {{$object->_ref_files|@count}};
      count_items += $("documents-fdr").select("a").length;
      Control.Tabs.setTabCount("fdrConsult", count_items);
    }
  });
</script>

{{foreach from=$object->_ref_files item=_file}}
  {{assign var=object_class value=$object->_class}}
  {{assign var=object_id    value=$object->_id        }}
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