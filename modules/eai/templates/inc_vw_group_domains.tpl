{{*
 * View group domains EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<fieldset>
  <legend>
    {{tr}}CGroups-back-group_domains{{/tr}} 
    {{if $domain->_id}}
      <button type="button" class="add notext" onclick="Domain.editGroupDomain(null, '{{$domain->_id}}')">{{tr}}Add{{/tr}}</button>
    {{/if}}  
  </legend>
  
  {{if count($domain->_ref_group_domains) > 0}}
  <table class="form">
    <tr>
      <th class="category">{{mb_label object=$group_domain field="group_id"}}</th>
      <th class="category">{{mb_label object=$group_domain field="object_class"}}</th>
      <th class="category">{{mb_label object=$group_domain field="master"}}</th>
      <th class="category">{{tr}}Actions{{/tr}}</th>
    </tr>
    
    {{foreach from=$domain->_ref_group_domains item=_group_domain}}  
      {{mb_include template=inc_list_group_domains group_domain=$_group_domain}}
    {{/foreach}}
  </table>
  {{else}}
    <div class="small-warning">{{tr}}CDomain-group_domain-none{{/tr}}</div>
  {{/if}}
</fieldset>