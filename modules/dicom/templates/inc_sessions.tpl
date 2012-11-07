{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


{{if $total_sessions != 0}}
  {{mb_include module=system template=inc_pagination total=$total_sessions current=$page change_page='DicomSession.changePage' step=20}}
{{/if}}

<table class="tbl">
  <thead>
  <tr>
    <th></th>
    <th>{{tr}}Actions{{/tr}}</th>
    <th>{{tr}}Details{{/tr}}</th>
    <th>{{mb_label object=$session field="begin_date"}}</th>
    <th>{{mb_label object=$session field="end_date"}}</th>
    <th>{{mb_label object=$session field="_duration"}}</th>
    <th>{{mb_label object=$session field="sender"}}</th>
    <th>{{mb_label object=$session field="receiver"}}</th>
    <th>{{mb_label object=$session field="status"}}</th>
  </tr>
  </thead>
  <tbody>
    {{foreach from=$sessions item=_session}}
      {{mb_include template=inc_session object=$_session}}
    {{foreachelse}}
      <tr>
        <td colspan="9" class="empty">
          {{tr}}CDicomSession.none{{/tr}}
        </td>
      </tr>
    {{/foreach}}
  </tbody>
</table>