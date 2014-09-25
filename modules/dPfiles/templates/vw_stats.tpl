<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-owner', true);
});

Details = {
  statOwner: function (doc_class, owner_guid) {
    new Url('files', 'stats_details') .
      addParam('doc_class', doc_class) .
      addParam('owner_guid', owner_guid) .
      requestModal();
  }
}
</script>

<ul id="tabs-owner" class="control_tabs">
  <li><a href="#tab-group">{{tr}}CGroups{{/tr}}</a></li>
  <li><a href="#tab-func" >{{tr}}CFunctions{{/tr}}</a></li>
  <li><a href="#tab-user" >{{tr}}CMediusers{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<table class="tbl">
<tr>
  <th colspan="2">{{tr}}CFile-_count{{/tr}}</th>
  <th colspan="2">{{tr}}CFile-_total_weight{{/tr}}</th>
  <th>{{tr}}CFile-_average_weight{{/tr}}</th>
  <th>{{tr}}Owner{{/tr}}</th>
  <th class="narrow"></th>
</tr>

<tr style="font-weight: bold;">
  <td class="narrow" style="text-align: right;">{{$total.docs_count|integer}}</td>
  <td class="narrow" style="text-align: right;">{{1|percent}}</td>
  <td class="narrow" style="text-align: right;">{{$total.docs_weight|decabinary}}</td>
  <td class="narrow" style="text-align: right;">{{1|percent}}</td>
  <td class="narrow" style="text-align: right;">{{$total._docs_average_weight|decabinary}}</td>
  <td>{{tr}}Total{{/tr}}
  <td>
    <button class="search notext compact" type="button" onclick="Details.statOwner('{{$doc_class}}');">
      {{tr}}Details{{/tr}}
    </button>
  </td>
</tr>

<tbody id="tab-group" style="display: none;">
  {{mb_include template=inc_stats_owner stats=$groups_stats}}
</tbody>

<tbody id="tab-func" style="display: none;">
  {{mb_include template=inc_stats_owner stats=$funcs_stats}}
</tbody>

<tbody id="tab-user" style="display: none;">
  {{mb_include template=inc_stats_owner stats=$users_stats}}
</tbody>
</table>