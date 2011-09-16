{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th>{{mb_title object=$table_description field=number}}</th>
    <th>{{mb_title object=$table_description field=description}}</th>
    <th>{{mb_title object=$table_description field=_count_entries}}</th>
  </tr>
  {{foreach from=$tables item=_table}}
    <tr {{if $_table->number == $table_entry->number}}class="selected"{{/if}}>
      <td>
        <a href="#{{$_table->number}}" onclick="loadEntries('{{$_table->number}}', this)">
          {{$_table->number|str_pad:4:'0':$smarty.const.STR_PAD_LEFT}}
        </a>
      </td>
      <td class="text">{{$_table->description}}</td>
      <td>{{$_table->_count_entries}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="3">{{tr}}CHL7v2TableDescription.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>