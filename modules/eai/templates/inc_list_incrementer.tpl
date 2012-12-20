{{*
 * View list incrementer EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<table class="form">
  <tr>
    <th class="category">{{mb_label object=$incrementer field="_view"}}</th>
    <th class="category">{{mb_label object=$incrementer field="pattern"}}</th>
    <th class="category">{{mb_label object=$incrementer field="range_min"}}</th>
    <th class="category">{{mb_label object=$incrementer field="range_max"}}</th>
    <th class="category">{{tr}}Actions{{/tr}}</th>
  </tr>
  
  <tr>
    <td>{{mb_value object=$incrementer field="_view"}}</td>
    <td>{{mb_value object=$incrementer field="pattern"}}</td>
    <td>{{mb_value object=$incrementer field="range_min"}}</td>
    <td>{{mb_value object=$incrementer field="range_max"}}</td>
    <td>
      <form name="editIncrementer" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPsante400" />
        <input type="hidden" name="dosql" value="do_incrementer_aed" />
        <input type="hidden" name="incrementer_id" value="{{$incrementer->_id}}" />
        <input type="hidden" name="del" value="0" />
               
        <button type="button" class="edit notext" onclick="Domain.editIncrementer('{{$incrementer->_id}}', '{{$domain->_id}}')">{{tr}}Edit{{/tr}}</button>
        <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {
          ajax:1, 
          typeName:&quot;{{tr}}{{$incrementer->_class}}.one{{/tr}}&quot;,
          objName:&quot;{{$incrementer->_view|smarty:nodefaults|JSAttribute}}&quot;},
          { onComplete: function() {          
            Domain.refreshListIncrementerActor('{{$domain->_id}}');
            Domain.refreshListDomains();
          }})">
          {{tr}}Delete{{/tr}}
        </button>
      </form>
    </td>
  </tr>  
</table>