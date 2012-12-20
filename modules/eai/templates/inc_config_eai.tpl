{{*
 * Configs EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<form name="editConfigEAI" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{assign var="mod" value="eai"}}
    <tr>
      <th class="title" colspan="10">{{tr}}config-{{$mod}}{{/tr}}</th>
    </tr>
    
    {{mb_include module=system template=inc_config_str var=exchange_format_delayed numeric=true}}
    
    {{mb_include module=system template=inc_config_str var=max_files_to_process numeric=true}}
    
    {{mb_include module=system template=inc_config_str var=max_reprocess_retries numeric=true}}
    
    {{mb_include module=system template=inc_config_bool var=use_domain}}

    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<hr/>

<table class="tbl">
  <tr>
    <th class="title" colspan="10">{{tr}}{{$mod}}-resume{{/tr}}</th>
  </tr>
  <tr>
    <th>{{tr}}CGroups{{/tr}}</th>
    <th colspan="2">Numéroteur</th>
    <th colspan="2">Serveur</th>
    <th colspan="2">Notifieur</th>
  </tr>
  <tr>  
    <th></th>
    <th>IPP</th>
    <th>NDA</th>
    <th>SIP</th>
    <th>SMP</th>
    <th>SIP</th>
    <th>SMP</th>
  </tr>
  {{foreach from=$groups item=_group}} 
    {{assign var=config value=$_group->_configs}}
    <tr>
      <td>{{$_group}}</td>
      <td class="{{if $_group->_is_ipp_supplier}}ok{{else}}error{{/if}}">
        {{tr}}bool.{{$_group->_is_ipp_supplier}}{{/tr}}</td>
      <td class="{{if $_group->_is_nda_supplier}}ok{{else}}error{{/if}}">
        {{tr}}bool.{{$_group->_is_nda_supplier}}{{/tr}}</td>
      <td></td>
      <td></td>
      <td class="{{if $config.sip_notify_all_actors}}ok{{else}}error{{/if}}">
        {{tr}}bool.{{$config.sip_notify_all_actors}}{{/tr}}</td>
      <td class="{{if $config.smp_notify_all_actors}}ok{{else}}error{{/if}}">
        {{tr}}bool.{{$config.smp_notify_all_actors}}{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>