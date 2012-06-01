<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create("list_operations");
  });
</script>
<ul id="list_operations" class="control_tabs">
  <li>
    <a href="#prevues" {{if !$operations_prevues|@count}}class="empty"{{/if}}>
      Prévues <small>({{$operations_prevues|@count}})</small>
    </a>
  </li>
  <li>
    <a href="#placees" {{if !$operations_placees|@count}}class="empty"{{/if}}>
      Placées <small>({{$operations_placees|@count}})</small>
    </a>
  </li>
</ul>

<hr class="control_tabs" />

<div id="prevues" style="display: none;">
  <table class="tbl">
    <tr>
      <th class="title" colspan="6">
        Liste des interventions
      </th>
    </tr>
    {{foreach from=$operations_prevues item=_operation}}
      {{mb_include module=hospi template=inc_line_stat_operation}}
    {{foreachelse}}
      <tr>
        <td class="empty">
          {{tr}}COperation.none{{/tr}}
        </td>
      </tr>
    {{/foreach}}
  </table>
</div>

<div id="placees" style="display: none;">
  <table class="tbl">
    <tr>
      <th class="title" colspan="6">
        Liste des interventions
      </th>
    </tr>
    {{foreach from=$operations_placees item=_operation}}
      {{mb_include module=hospi template=inc_line_stat_operation}}
    {{foreachelse}}
      <tr>
        <td class="empty">
          {{tr}}COperation.none{{/tr}}
        </td>
      </tr>
    {{/foreach}}
  </table>
</div>
