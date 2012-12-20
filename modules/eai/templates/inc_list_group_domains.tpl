{{*
 * View list group domains EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<tr>
  <td>{{mb_value object=$group_domain->_ref_group field="_view"}}</td>
  <td>{{mb_value object=$group_domain field="object_class"}}</td>
  <td>{{mb_value object=$group_domain field="master"}}</td>
  <td>
    <form name="editGroupDomain{{$group_domain->_guid}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_group_domain_aed" />
      <input type="hidden" name="group_domain_id" value="{{$group_domain->_id}}" />
      <input type="hidden" name="del" value="0" />

      <button type="button" class="edit notext" onclick="Domain.editGroupDomain('{{$group_domain->_id}}', '{{$domain->_id}}')">{{tr}}Edit{{/tr}}</button> 
      <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {
        ajax:1, 
        typeName:&quot;{{tr}}{{$group_domain->_class}}.one{{/tr}}&quot;,
        objName:&quot;{{$group_domain->_view|smarty:nodefaults|JSAttribute}}&quot;},
        { onComplete: function() {          
          Domain.refreshListGroupDomains('{{$domain->_id}}');
        }})">
        {{tr}}Delete{{/tr}}
      </button>
    </form> 
  </td>
</tr>