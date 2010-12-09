{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=system script=ex_class_editor}}

{{main}}
ExClass.edit({{$ex_class->_id}});
{{/main}}

<table class="main">
  <tr>
    <td style="width: 20%">
      <button type="button" class="new" onclick="ExClass.edit('0')">
        {{tr}}CExClass-title-create{{/tr}}
      </button>
      
      <table class="main tbl">
        <tr>
          <th>{{mb_title class=CExClass field=host_class}}</th>
          <th>{{mb_title class=CExClass field=event}}</th>
          <th>{{tr}}CExClass-back-fields{{/tr}}</th>
        </tr>
        {{foreach from=$list_ex_class item=_ex_class}}
          <tr>
            <td>
              <a href="#1" onclick="ExClass.edit({{$_ex_class->_id}})">
                <strong>{{mb_value object=$_ex_class field=host_class}}</strong>
              </a>
            </td>
            <td>{{mb_value object=$_ex_class field=event}}</td>
            <td>{{$_ex_class->countBackRefs("fields")}}</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="4">{{tr}}CExClass.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    
    </td>
    <td id="exClassEditor">
      <!-- exClassEditor -->
    </td>
  </tr>
</table>