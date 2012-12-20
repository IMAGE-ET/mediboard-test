{{*
 * View list domains EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<form name="listGroupDomains" action="?m={{$m}}" method="post">
  <table class="tbl">
    <tr>
      <th>
        <button type="button" class="merge notext compact" title="{{tr}}Merge{{/tr}}" style="float: left;" onclick="Domain.resolveConflicts(this.form);">
          {{tr}}Merge{{/tr}}
        </button>
      </th>
      <th>{{mb_title object=$domain field=tag}}</th>
      <th>{{mb_title object=$domain field=libelle}}</th>
      <th>{{mb_title object=$domain field=incrementer_id}}</th>
      <th>{{mb_title object=$domain field=actor_id}}</th>
      <th>{{tr}}CDomain-back-group_domains{{/tr}}</th>
      <th>{{mb_title object=$domain field=_is_master_ipp}}</th>
      <th>{{mb_title object=$domain field=_is_master_nda}}</th>
      <th>{{mb_title object=$domain field=_count_objects}}</th>
    </tr>
    {{foreach from=$domains item=_domain}}
      <tr {{if $_domain->_id == $domain->_id}}class="selected"{{/if}}>
        <td class="narrow">
          <input type="checkbox" name="domains_id[]" value="{{$_domain->_id}}" class="merge" style="float: left;" onclick="checkOnlyTwoSelected(this)" />
        </td>
        <td>
          <a href="#{{$_domain->_guid}}" onclick="Domain.showDomain('{{$_domain->_id}}', this)">
            {{mb_value object=$_domain field=tag}}
          </a>
        </td>
        <td>
          <a href="#{{$_domain->_guid}}" onclick="Domain.showDomain('{{$_domain->_id}}', this)">
            {{mb_value object=$_domain field=libelle}}
          </a>
        </td>
        {{assign var=incrementer value=$_domain->_ref_incrementer}}
        <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$incrementer->_guid}}');">
            {{mb_value object=$incrementer field=_view}}
          </span>
        </td>
        {{assign var=actor value=$_domain->_ref_actor}}
        <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$actor->_guid}}');">
            {{mb_value object=$actor field=_view}}
          </span>
        </td>
        <td>
          {{if $_domain->_ref_group_domains > 0}}
            <ul>
              {{foreach from=$_domain->_ref_group_domains item=_group_domain}}
                <li>{{$_group_domain->_ref_group->_view}}</li>
              {{/foreach}}
            </ul> 
          {{/if}}
        </td>
        <td {{if $_domain->_is_master_ipp}}class="ok"{{/if}}></td>
        <td {{if $_domain->_is_master_nda}}class="ok"{{/if}}></td>
        <td>
          {{if $_domain->_count_objects > 0}}
            <ul>
              {{foreach from=$_domain->_detail_objects item=_detail_object}}
                <li><strong>{{tr}}{{$_detail_object.object_class}}{{/tr}}</strong> : {{$_detail_object.total}}</li>
              {{/foreach}}
            </ul> 
          {{/if}}
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="4" class="empty">{{tr}}CDomain.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</form>