{{* $Id: configure.tpl 10085 2010-09-16 09:20:46Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage ftp
 * @version $Revision: 10085 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}



<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#config-read-files-senders">{{tr}}config-read-files-senders{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="config-read-files-senders" style="display: none;">
  {{mb_include template=inc_config_read_files_senders}}
</div>
