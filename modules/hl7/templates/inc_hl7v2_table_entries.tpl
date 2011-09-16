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
    <th class="title" colspan="3">{{$table_name}} </th>
  </tr>
  <tr>
    <th class="category narrow">Code HL7</th>
    <th class="category narrow">Code MB</th>
    <th class="category">Description</th>
  </tr>
  {{foreach from=$table_entries item=_table_entry}}
    <tr>
      <td class="disabled" style="width: 20%">
        {{if !$_table_entry->user}}
          {{mb_value object=$_table_entry field="code_hl7"}}
        {{else}}
          <form name="editTabEntryHL7Code-{{$_table_entry->_id}}" action="?m=hl7" method="post" onsubmit="return onSubmitFormAjax(this, { 
            onComplete : function() { loadEntries('{{$_table_entry->number}}') }});">
            <input type="hidden" name="m" value="hl7" />
            <input type="hidden" name="@class" value="{{$_table_entry->_class}}" />
            {{mb_key object=$_table_entry}}
            {{mb_field object=$_table_entry field="code_hl7" size="10"}}  <button type="submit" class="save notext">{{tr}}Save{{/tr}}</button>
          </form>
        {{/if}}
      </td>
      <td class="disabled" style="width: 20%">
        <form name="editTabEntryMbCode-{{$_table_entry->_id}}" action="?m=hl7" method="post" onsubmit="return onSubmitFormAjax(this, { 
            onComplete : function() { loadEntries('{{$_table_entry->number}}') }});">
          <input type="hidden" name="m" value="hl7" />
          <input type="hidden" name="@class" value="{{$_table_entry->_class}}" />
          {{mb_key object=$_table_entry}}
          {{mb_field object=$_table_entry field="code_mb" size="10"}}  <button type="submit" class="save notext">{{tr}}Save{{/tr}}</button>
        </form>
      </td>
      <td class="disabled" style="width: 60%">
        {{if !$_table_entry->user}}
          {{mb_value object=$_table_entry field="description"}}
        {{else}}
          <form name="editTabEntryHL7Description-{{$_table_entry->_id}}" action="?m=hl7" method="post" onsubmit="return onSubmitFormAjax(this, { 
            onComplete : function() { loadEntries('{{$_table_entry->number}}') }});">
            <input type="hidden" name="m" value="hl7" />
            <input type="hidden" name="@class" value="{{$_table_entry->_class}}" />
            {{mb_key object=$_table_entry}}
            {{mb_field object=$_table_entry field="description"}}  <button type="submit" class="save notext">{{tr}}Save{{/tr}}</button>
          </form>
        {{/if}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="3">{{tr}}No result{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

<form name="editTabEntryHL7Code" action="?m=hl7" method="post" onsubmit="return onSubmitFormAjax(this, { 
  onComplete : function() { loadEntries('{{$table_entry->number}}') }});">
  <input type="hidden" name="m" value="hl7" />
  <input type="hidden" name="@class" value="{{$table_entry->_class}}" />
  {{mb_key object=$table_entry}}
  {{mb_field object=$table_entry field="number" hidden=true}}
  {{mb_field object=$table_entry field="user" hidden=true}}
  
  <table class="tbl">
    <tr>
      <td class="disabled" style="width: 20%">
        {{mb_field object=$table_entry field="code_hl7" size="10"}} 
      </td> 
      <td class="disabled" style="width: 20%">
        {{mb_field object=$table_entry field="code_mb" size="10"}} 
      </td> 
      <td class="disabled" style="width: 60%">
        {{mb_field object=$table_entry field="description"}}  <button type="submit" class="add notext">{{tr}}Save{{/tr}}</button>
      </td>   
    </tr>
  </table>
</form>