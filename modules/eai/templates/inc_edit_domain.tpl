{{*
 * Edit domain EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{if $domain->_is_master_ipp}}
  <div class="small-info">
    {{tr}}CDomain-master_ipp{{/tr}}
  </div>
{{/if}}

{{if $domain->_is_master_nda}}
  <div class="small-info">
    {{tr}}CDomain-master_nda{{/tr}}
  </div>
{{/if}}

{{if $domain->derived_from_idex}}
  <div class="small-info">
    {{tr}}CDomain-is_derived_from_idex{{/tr}}
  </div>
{{/if}}

<form name="editDomain" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, Domain.refreshListDomains);">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="dosql" value="do_domain_aed" />
  <input type="hidden" name="domain_id" value="{{$domain->_id}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="incrementer_id" value="{{$domain->_ref_incrementer->_id}}" />
  <input type="hidden" name="actor_id" value="{{$domain->_ref_actor->_id}}" />
  <input type="hidden" name="actor_class" value="{{$domain->_ref_actor->_class}}" />
  <input type="hidden" name="callback" value="Domain.showDomainCallback" />

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$domain}}
    
    <tr>
      <th>{{mb_label object=$domain field="tag"}}</th>
      {{if $domain->_id}}
        <td>{{mb_value object=$domain field="tag"}}</td>
      {{else}}
        <td>{{mb_field object=$domain field="tag"}}</td>
      {{/if}}
    </tr>

    <tr>
      <th>{{mb_label object=$domain field="libelle"}}</th>
      <td>{{mb_field object=$domain field="libelle"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$domain field="OID"}}</th>
      <td>{{mb_field object=$domain field="OID" size=50}}</td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {{if $domain->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button" onclick="confirmDeletion(this.form, {
            ajax:1, 
            typeName:&quot;{{tr}}{{$domain->_class}}.one{{/tr}}&quot;,
            objName:&quot;{{$domain->_view|smarty:nodefaults|JSAttribute}}&quot;},
            { onComplete: function() {
              Domain.refreshListDomains();
              Domain.showDomain('{{$domain->_id}}');
            }})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
           <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr> 
  </table>
</form>

{{if $domain->derived_from_idex}}
  {{mb_return}}
{{/if}}

<div id="vw_list_group_domains">
  {{mb_include template=inc_vw_group_domains}}
</div>

<div id="vw_list_incrementer_actor">
  {{mb_include template=inc_vw_incrementer_actor}}
</div>
    