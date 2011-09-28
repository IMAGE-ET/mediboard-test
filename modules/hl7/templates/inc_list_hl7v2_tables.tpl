{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
 
<form name="listFilter" action="?m={{$m}}" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="page" value="{{$page}}" onchange="this.form.submit()"/>
  
  <table class="form">
    <tr>
      <th class="title"> {{tr}}Filter{{/tr}} </th>
    </tr>
    <tr>
      <td style="text-align: center">
        <input type="text" name="keywords" value="{{$keywords}}" onchange="$V(this.form.page, 0)" />
        <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
      </td>
    </tr>
  </table>
     
  {{if $total_tables != 0}}
    {{mb_include module=system template=inc_pagination total=$total_tables current=$page change_page='changePage' step='25' narrow=true}}
  {{/if}}
</form>

<table class="tbl">
  <tr>
    <th>{{mb_title object=$table_description field=number}}</th>
    <th>{{mb_title object=$table_description field=description}}</th>
    <th>{{mb_title object=$table_description field=_count_entries}}</th>
    <th></th>
  </tr>
  {{foreach from=$tables item=_table}}
    <tr {{if $_table->number == $table_entry->number}}class="selected"{{/if}}>
      <td {{if !$_table->user}}class="disabled"{{/if}}>
        <a href="#{{$_table->number}}" onclick="loadEntries('{{$_table->number}}', this)">
          {{$_table->number|str_pad:4:'0':$smarty.const.STR_PAD_LEFT}}
        </a>
      </td>
      <td class="text {{if !$_table->user}}disabled{{/if}}">{{$_table->description}}</td>
      <td {{if !$_table->user}}class="disabled"{{/if}}>{{$_table->_count_entries}}</td>
      <td>
        {{if $_table->user}}
          <form name="delHL7TabDescription-{{$_table->_id}}" action="?m=hl7" method="post" onsubmit="return onSubmitFormAjax(this, { 
            onComplete : loadTables() });">
            <input type="hidden" name="m" value="hl7" />
            <input type="hidden" name="@class" value="{{$_table->_class}}" />
            <input type="hidden" name="del" value="1" />
            {{mb_key object=$_table}}
            <button type="submit" class="trash notext">{{tr}}Delete{{/tr}}</button>
          </form>
        {{/if}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="4">{{tr}}CHL7v2TableDescription.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>