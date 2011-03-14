{{* $Id: view_messages.tpl 7622 2009-12-16 09:08:41Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=system script=view_sender}}

<button class="new singleclick" onclick="ViewSender.edit(0);">
	{{tr}}CViewSender-title-create{{/tr}}
</button>

<script type="text/javascript">
	Main.add(ViewSender.refreshList);
</script>
<div id="list-senders">
	<div class="small-info">Liste des exports de vues</div>
</div>

