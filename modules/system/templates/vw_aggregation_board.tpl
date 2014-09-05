{{*
 * $Id$
 *
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 *}}

<script>
  aggregate = function (dry_run, table) {
    var url = new Url('system', 'aggregate_access_logs');
    url.addParam("table", table);

    if (dry_run) {
      url.addParam('dry_run', 1);
      url.requestUpdate("aggregate_" + table);
    }
    else {
      Modal.confirm("Souhaitez vous réellement aggréger les journaux ?", {onOK: function () {
          url.requestUpdate("aggregate_" + table);
      } });
    }
  }
</script>

<div class="small-warning">
  Ne seront agrégés que les journaux du plus ancien mois.
</div>

<table class="main tbl">
  <tr>
    <th>{{tr}}Table{{/tr}}</th>
    <th>{{tr}}Aggregation{{/tr}}</th>
    <th>{{tr}}Entries{{/tr}}</th>
    <th>Date min.</th>
    <th>Date max.</th>

    <th>{{tr}}Data{{/tr}}</th>
    <th>{{tr}}Indexes{{/tr}}</th>
    <th>{{tr}}Free{{/tr}}</th>
    <th>{{tr}}Total{{/tr}}</th>

    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>

  {{foreach from=$stats key=_table item=_stats}}
    {{foreach name=_loop from=$_stats.data item=_aggregate}}
      <tr>
      {{if $smarty.foreach._loop.first}}
        <th class="section" rowspan="{{$_stats.data|@count}}">{{$_table}}</th>
      {{/if}}

        <td style="text-align: right;">{{$_aggregate.aggregate}}</td>
        <td style="text-align: right;">{{$_aggregate.records|integer}}</td>
        <td style="text-align: right;">{{$_aggregate.date_min|date_format:$conf.date}}</td>
        <td style="text-align: right;">{{$_aggregate.date_max|date_format:$conf.date}}</td>

        {{if $smarty.foreach._loop.first}}
          <td rowspan="{{$_stats.data|@count}}" style="text-align: right;">{{$_stats.meta.data_length|decabinary}}</td>
          <td rowspan="{{$_stats.data|@count}}" style="text-align: right;">{{$_stats.meta.index_length|decabinary}}</td>
          <td rowspan="{{$_stats.data|@count}}" style="text-align: right;">{{$_stats.meta.data_free|decabinary}}</td>
          <td rowspan="{{$_stats.data|@count}}" style="text-align: right;">{{$_stats.meta.total|decabinary}}</td>

          <td rowspan="{{$_stats.data|@count}}" style="text-align: center;">
            <button class="search" type="button" onclick="aggregate(true, '{{$_table}}');">{{tr}}DryRun{{/tr}}</button>
            <button class="search" type="button" onclick="aggregate(false, '{{$_table}}')">{{tr}}Aggregate{{/tr}}</button>
          </td>

          <td rowspan="{{$_stats.data|@count}}" id="aggregate_{{$_table}}"></td>
        {{/if}}
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>