{{* $Id: configure.tpl 10594 2010-11-08 09:00:28Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage sa
 * @version $Revision: 10594 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#SA">{{tr}}SA{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="SA" style="display: none;">
  {{mb_include template=inc_config_sa}}
</div>