{{*
 * $Id$
 *
 * @category hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#config-treatment">{{tr}}config-hprimsante-treatment{{/tr}}</a></li>
  <li><a href="#config-purge_echange">{{tr}}config-hprimsante-purge_echange{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="config-treatment" style="display: none;">
  {{mb_include template=inc_config_treatment}}
</div>

<div id="config-purge_echange" style="display: none;">
  {{mb_include template=inc_config_purge_echange}}
</div>

