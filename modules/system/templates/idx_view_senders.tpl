{{* $Id: view_messages.tpl 7622 2009-12-16 09:08:41Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=system script=view_sender}}
{{mb_script module=system script=view_sender_source}}
{{mb_script module=system script=source_to_view_sender}}
{{mb_script module=system script=exchange_source}}

<script type="text/javascript">
Main.add(function() {
  Control.Tabs.create('tabs-main', true).activeLink.onmouseup();
});
</script>

<ul id="tabs-main" class="control_tabs">
  <li><a href="#senders" onmouseup="ViewSender.refreshList();"      >{{tr}}CViewSender{{/tr}}</a></li>
  <li><a href="#sources" onmouseup="ViewSenderSource.refreshList();">{{tr}}CViewSenderSource{{/tr}}</a></li>
  <li><a href="#dosend"  onmouseup="ViewSender.doSend();"           >{{tr}}CViewSender-title-dosend{{/tr}}</a></li>
  <li><a href="#monitor" onmouseup="ViewSender.refreshMonitor();"   >{{tr}}CViewSender-title-monitor{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="senders" style="display: none;">

	<button class="new singleclick" onclick="ViewSender.edit(0);">
	  {{tr}}CViewSender-title-create{{/tr}}
	</button>
	
	<div id="list-senders">
	</div>

</div>

<div id="sources" style="display: none;">

  <button class="new singleclick" onclick="ViewSenderSource.edit(0);">
    {{tr}}CViewSenderSource-title-create{{/tr}}
  </button>
  
  <div id="list-sources">
  </div>

</div>

<div id="dosend" style="display: none;">
</div>

<div id="monitor" style="display: none;">
</div>
