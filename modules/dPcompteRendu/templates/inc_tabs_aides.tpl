{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create('tabs-owner', true);
});
</script>

<ul id="tabs-owner" class="control_tabs">
  <li>
    <a href="#owner-user" {{if $aidesCount.user == 0}}class="empty"{{/if}}>
      {{$userSel}} <small>({{$aidesCount.user}})</small>
    </a>
  </li>
  <li>
    <a href="#owner-func" {{if $aidesCount.func == 0}}class="empty"{{/if}}>
      {{$userSel->_ref_function}} <small>({{$aidesCount.func}})</small>
    </a>
  </li>
  <li>
    <a href="#owner-etab" {{if $aidesCount.etab == 0}}class="empty"{{/if}}>
      {{$userSel->_ref_function->_ref_group}} <small>({{$aidesCount.etab}})</small>
    </a>
  </li>
</ul>

<hr class="control_tabs" />

<div id="owner-user" style="display: none;">
  {{include file=inc_list_aides.tpl owner=$userSel aides=$aides.user type=user}}
</div>

<div id="owner-func" style="display: none;">
  {{include file=inc_list_aides.tpl owner=$userSel->_ref_function aides=$aides.func type=func}}
</div>

<div id="owner-etab" style="display: none;">
  {{include file=inc_list_aides.tpl owner=$userSel->_ref_function->_ref_group aides=$aides.etab type=etab}}
</div>
