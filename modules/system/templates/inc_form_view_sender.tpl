{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10391 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="Edit-{{$sender->_guid}}" action="?m={{$m}}" method="post" onsubmit="return ViewSender.onSubmit(this);">
  {{mb_class object=$sender}}
  {{mb_key   object=$sender}}
  <input type="hidden" name="del" value="0" />

  <table class="form">
  
    {{mb_include template=inc_form_table_header object=$sender}}
    
    <tr>
      <th class="narrow">{{mb_label object=$sender field=name}}</th>
      <td>{{mb_field object=$sender field=name}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$sender field=description}}</th>
      <td>{{mb_field object=$sender field=description}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$sender field=params}}</th>
      <td>{{mb_field object=$sender field=params}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$sender field=period}}</th>
      <td>{{mb_field object=$sender field=period}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$sender field=offset}}</th>
      <td>{{mb_field object=$sender field=offset}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$sender field=active}}</th>
      <td>{{mb_field object=$sender field=active}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$sender field=max_archives}}</th>
      <td>{{mb_field object=$sender field=max_archives}}</td>
    </tr>
    
    {{if $sender->_id}}
    <tr>
      <th>{{mb_label object=$sender field=last_size}}</th>
      <td>{{$sender->last_size|decabinary}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$sender field=last_duration}}</th>
      <td>{{$sender->last_duration|string_format:"%.3f"}}s</td>
    </tr>
    {{/if}}
    
    <tr>
      <td class="button" colspan="2">
        {{if $sender->_id}}
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        <button class="new" type="submit" onclick="ViewSender.duplicate(this.form);">
          {{tr}}Duplicate{{/tr}}
        </button>
        <button class="trash" type="button" onclick="ViewSender.confirmDeletion(this.form);">
          {{tr}}Delete{{/tr}}
        </button>
        <br />
        <button class="search" type="button" onclick="ViewSender.show('{{$sender->_id}}');">
          {{tr}}Show{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  
  </table>

</form>
