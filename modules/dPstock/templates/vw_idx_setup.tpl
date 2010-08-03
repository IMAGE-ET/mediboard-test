{{* $Id: vw_idx_order_manager.tpl 9451 2010-07-13 12:47:44Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 9451 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  var tabs = Control.Tabs.create('tab_setup', true);
  refreshTab(tabs.activeContainer.id, tabs.activeLink.up('li'));
});

function refreshTab(tab_name, element) {
  var url = new Url("dPstock", tab_name);
  url.requestUpdate(tab_name);
  if (element) element.onmousedown = null;
}
</script>

<!-- Tabs titles -->
<ul id="tab_setup" class="control_tabs">
  {{foreach from=$tabs item=_tab}}
    <li onmousedown="refreshTab('{{$_tab}}', this)">
      <a href="#{{$_tab}}">{{tr}}mod-dPstock-tab-{{$_tab}}{{/tr}}</a>
    </li>
  {{/foreach}}
</ul>
<hr class="control_tabs" />

<!-- Tabs containers -->
{{foreach from=$tabs item=_tab}}
  <div id="{{$_tab}}" style="display: none;"></div>
{{/foreach}}
