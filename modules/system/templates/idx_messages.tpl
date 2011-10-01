{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=system script=message}}

<script type="text/javascript">
Main.add(Message.refreshList);
</script>

<button class="new singleclick" onclick="Message.edit(0);">
  {{tr}}CMessage-title-create{{/tr}}
</button>

<button class="new singleclick" onclick="Message.createUpdate();" style="float:right;">
  {{tr}}CMessage-title-create_update{{/tr}}
</button>

<div id="list-messages"></div>
