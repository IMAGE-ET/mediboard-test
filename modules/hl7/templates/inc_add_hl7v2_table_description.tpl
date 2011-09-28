{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="createHL7TabDescription" action="?m=hl7" method="post" onsubmit="return onSubmitFormAjax(this, { 
  onComplete : loadTables() });">
  <input type="hidden" name="m" value="hl7" />
  <input type="hidden" name="@class" value="{{$table_description->_class}}" />
  {{mb_key object=$table_description}}
  {{mb_field object=$table_description field="user" hidden=true}}
  
  <table class="form">
    <tr>
      <th class="title" colspan="2">{{tr}}CHL7v2TableDescription-title-create{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$table_description field="number"}}</th>
      <td>{{mb_field object=$table_description field="number"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$table_description field="description"}}</th>
      <td>{{mb_field object=$table_description field="description"}}</td>
    </tr>
    <td colspan="2" style="text-align: center">
      <button type="submit" class="save">{{tr}}Save{{/tr}}</button>
    </td>
  </table>
</form>