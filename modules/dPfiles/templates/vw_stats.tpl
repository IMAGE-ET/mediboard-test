<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-owner', true);
});
</script>

<ul id="tabs-owner" class="control_tabs">
  <li><a href="#tab-user">{{tr}}CMediusers{{/tr}}</a></li>
  <li><a href="#tab-func">{{tr}}CFunctions{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<table class="tbl">
<tr>
  <th colspan="2">{{tr}}CFile-_count{{/tr}}</th>
  <th colspan="2">{{tr}}CFile-_total_weight{{/tr}}</th>
  <th>{{tr}}CFile-_average_weight{{/tr}}</th>
  <th>{{tr}}Owner{{/tr}}</th>
</tr>

<tr style="font-weight: bold;">
  <td style="text-align: right;">{{$total.files_count}}</td>
  <td style="text-align: right;">{{1|percent}}</td>
  <td style="text-align: right;">{{$total._files_weight}}</td>
  <td style="text-align: right;">{{1|percent}}</td>
  <td style="text-align: right;">{{$total._file_average_weight}}</td>
  <td>{{tr}}Total{{/tr}}
</tr>

<tbody id="tab-user" style="display: none;">
  {{mb_include template=inc_stats_owner stats=$stats_user}}
</tbody>

<tbody id="tab-func" style="display: none;">
  {{mb_include template=inc_stats_owner stats=$stats_func}}
</tbody>

</table>