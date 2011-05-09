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

<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tabs-main', true));
</script>

<ul id="tabs-main" class="control_tabs">
  <li><a href="#senders">{{tr}}CViewSender{{/tr}}</a></li>
  <li><a href="#sources">{{tr}}CViewSenderSource{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="senders" style="display: none;">

	<button class="new singleclick" onclick="ViewSender.edit(0);">
	  {{tr}}CViewSender-title-create{{/tr}}
	</button>
	
	<script type="text/javascript">
    Main.add(ViewSender.refreshList);
    Main.add(ViewSender.doSend);
	</script>

	<div id="list-senders">
	</div>

  <div id="send-views">
  </div>

</div>

<div id="sources" style="display: none;">

  <button class="new singleclick" onclick="ViewSenderSource.edit(0);">
    {{tr}}CViewSenderSource-title-create{{/tr}}
  </button>
  
  <div id="list-sources">
  </div>

  <script type="text/javascript">
    Main.add(ViewSenderSource.refreshList);
  </script>
    
</div>

