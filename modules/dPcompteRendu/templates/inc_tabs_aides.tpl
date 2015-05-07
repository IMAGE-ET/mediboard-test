{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  Main.add(function() {
    Control.Tabs.create('tabs-owner', true);
  });
</script>

<ul id="tabs-owner" class="control_tabs">
  {{if isset($aides.user|smarty:nodefaults)}}
  <li>
    <a href="#owner-user" {{if $aidesCount.user == 0}}class="empty"{{/if}}>
      {{$userSel}} <small>({{$aidesCount.user}})</small>
    </a>
  </li>
  {{/if}}
  {{if isset($aides.func|smarty:nodefaults)}}
  <li>
    <a href="#owner-func" {{if $aidesCount.func == 0}}class="empty"{{/if}}>
      {{if $function->_id}}{{$function}}{{else}}{{$userSel->_ref_function}}{{/if}} <small>({{$aidesCount.func}})</small>
    </a>
  </li>
  {{/if}}
  {{if isset($aides.etab|smarty:nodefaults)}}
  <li>
    <a href="#owner-etab" {{if $aidesCount.etab == 0}}class="empty"{{/if}}>
      {{if $function->_id}}{{$function->_ref_group}}{{else}}{{$userSel->_ref_function->_ref_group}}{{/if}} <small>({{$aidesCount.etab}})</small>
    </a>
  </li>
  {{/if}}
</ul>

{{if isset($aides.user|smarty:nodefaults)}}
<div id="owner-user" style="display: none;">
  {{mb_include template=inc_list_aides owner=$userSel aides=$aides.user type=user aides_ids=$aides.user_ids}}
</div>
{{/if}}

{{if isset($aides.func|smarty:nodefaults)}}
<div id="owner-func" style="display: none;">
  {{assign var=owner value=$userSel->_ref_function}}
  {{if $function->_id}}
    {{assign var=owner value=$function}}
  {{/if}}
  {{mb_include template=inc_list_aides owner=$owner aides=$aides.func type=func aides_ids=$aides.func_ids}}
</div>
{{/if}}

{{if isset($aides.etab|smarty:nodefaults)}}
<div id="owner-etab" style="display: none;">
  {{assign var=owner value=$userSel->_ref_function->_ref_group}}
  {{if $function->_id}}
    {{assign var=owner value=$function->_ref_group}}
  {{/if}}
  {{mb_include template=inc_list_aides owner=$owner aides=$aides.etab type=etab aides_ids=$aides.etab_ids}}
</div>
{{/if}}
