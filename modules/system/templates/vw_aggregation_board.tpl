{{*
 * $Id$
 *
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 *}}

<table class="main tbl">
  <tr>
    <th>{{tr}}Table{{/tr}}</th>
    <th>{{tr}}Aggregation{{/tr}}</th>
    <th>{{tr}}Entries{{/tr}}</th>
    <th>Date min.</th>
    <th>Date max.</th>

    <th>{{tr}}Data{{/tr}} (MB)</th>
    <th>{{tr}}Indexes{{/tr}} (MB)</th>
    <th>{{tr}}Free{{/tr}} (MB)</th>
    <th>{{tr}}Total{{/tr}} (MB)</th>

    <th>{{tr}}Action{{/tr}}</th>
    <th></th>
  </tr>

  {{foreach from=$stats key=_table item=_stats}}
    {{foreach name=_loop from=$_stats.data item=_aggregate}}
      <tr>
      {{if $smarty.foreach._loop.first}}
        <th class="section" rowspan="{{$_stats.data|@count}}">{{$_table}}</th>
      {{/if}}

        <td style="text-align: right;">{{$_aggregate.aggregate}}</td>
        <td style="text-align: right;">{{$_aggregate.records|number_format:0:'.':' '}}</td>
        <td style="text-align: right;">{{$_aggregate.date_min|date_format:"%d-%m-%Y"}}</td>
        <td style="text-align: right;">{{$_aggregate.date_max|date_format:"%d-%m-%Y"}}</td>

        {{if $smarty.foreach._loop.first}}
          <td style="text-align: right;">{{$_stats.meta.data_mb|number_format:2:'.':' '}}</td>
          <td style="text-align: right;">{{$_stats.meta.index_mb|number_format:2:'.':' '}}</td>
          <td style="text-align: right;">{{$_stats.meta.data_free|number_format:2:'.':' '}}</td>
          <td style="text-align: right;">{{$_stats.meta.total|number_format:2:'.':' '}}</td>

          <td rowspan="{{$_stats.data|@count}}" style="text-align: center;">
            <button class="search" type="button" onclick="AccessLog.aggregate(true)">{{tr}}DryRun{{/tr}}</button>
            <button class="search" type="button" onclick="AccessLog.aggregate(false)">{{tr}}Aggregate{{/tr}}</button>
          </td>
          <td rowspan="{{$_stats.data|@count}}"></td>
        {{/if}}
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>