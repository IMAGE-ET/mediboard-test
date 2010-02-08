{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 *}}

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-back', true);
});
</script>

<ul id="tabs-back" class="control_tabs">
  <li id="tab-equipements">
    {{assign var=count_equipements value=$plateau->_ref_equipements|@count}}
  	<a href="#equipements" {{if !$count_equipements}}class="empty"{{/if}}>
      {{tr}}CPlateauTechnique-back-equipements{{/tr}}
			<small>({{$count_equipements}})</small>
		</a>
	</li>
  <li id="tab-techniciens">
    {{assign var=count_techniciens value=$plateau->_ref_techniciens|@count}}
    <a href="#techniciens" {{if !$count_techniciens}}class="empty"{{/if}}>
      {{tr}}CPlateauTechnique-back-techniciens{{/tr}}
      <small>({{$count_techniciens}})</small>
    </a>
  </li>
</ul>

<hr class="control_tabs" />

<div id="equipements" style="display: none;">
  {{mb_include_script module=ssr script=equipement}}
  {{mb_include template=inc_edit_equipement}}
</div>

<div id="techniciens" style="display: none;">
  {{mb_include_script module=ssr script=technicien}}
  {{mb_include template=inc_edit_technicien}}
</div>