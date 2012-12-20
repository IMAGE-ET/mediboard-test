{{*
 * View incrementer/domain EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{if count($domain->_ref_group_domains) > 0}}
<fieldset>
  <legend>
    {{mb_label object=$domain field="incrementer_id"}}
    {{if !$domain->incrementer_id && !$domain->actor_id && ($domain->_is_master_ipp || $domain->_is_master_nda)}}
      <button type="button" class="add notext" onclick="Domain.editIncrementer(null, '{{$domain->_id}}')">{{tr}}Add{{/tr}}</button>
      <input type="hidden" name="incrementer_id" value="" />
    {{/if}}
  </legend>
  
  {{if $domain->incrementer_id}}
    {{mb_include template=inc_list_incrementer incrementer=$domain->_ref_incrementer}}
  {{else}}
    {{if $domain->actor_id}}
      <div class="small-warning">{{tr}}CDomain-incrementer-none{{/tr}}</div>
    {{else}}
      {{if !$domain->_is_master_ipp && !$domain->_is_master_nda}}
        <div class="small-warning">{{tr}}CDomain-incrementer-no_master{{/tr}}</div>
      {{else}}
        <div class="small-info">{{tr}}CDomain-incrementer_id-desc{{/tr}}</div>
      {{/if}}
    {{/if}}
  {{/if}}
</fieldset>

<fieldset>
  <legend> {{mb_label object=$domain field="actor_id"}} </legend>
  
  <table class="form">
  {{if !$domain->incrementer_id}}
    {{if $domain->actor_id}}     
      {{assign var=actor value=$domain->_ref_actor}}
      
      {{mb_include template=inc_list_actor actor=$domain->_ref_actor}}
    {{else}}
      <div class="small-info">{{tr}}CDomain-actor_id-desc{{/tr}}</div>
      
      <table class="form">
        <tr>
          <td>
            <form name="editActor" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete : function() {
              Domain.refreshListIncrementerActor('{{$domain->_id}}'); Domain.refreshListDomains(); }});">
              
              <input type="hidden" name="m" value="eai" />
              <input type="hidden" name="dosql" value="do_domain_actor_aed" />
              <input type="hidden" name="domain_id" value="{{$domain->_id}}" />
              <input type="hidden" name="del" value="0" />
            
              <select name="actor_guid" onchange="this.form.onsubmit()">
                <option value="">&mdash;</option>
                {{foreach from=$actors key=_actor_class item=_actors}}
                  <option value="" disabled> &mdash; {{tr}}{{$_actor_class}}{{/tr}} &mdash; </option>
                    {{foreach from=$_actors key=_sub_actor_class item=_sub_actors}}
                      <optgroup label="{{tr}}{{$_sub_actor_class}}{{/tr}}">
                      {{foreach from=$_sub_actors item=_actor}}  
                        <option value="{{$_actor->_guid}}">{{$_actor}}</option>
                      {{/foreach}}
                      </optgroup>
                    {{/foreach}}
                {{/foreach}}
              </select>
            </form>
          </td>
        </tr>
      </table>
    {{/if}} 
  {{else}}
    <div class="small-warning">{{tr}}CDomain-actor_none{{/tr}}</div>
  {{/if}} 
</fieldset>
{{/if}}