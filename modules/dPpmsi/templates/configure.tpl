{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#PMSI">{{tr}}PMSI{{/tr}}</a></li>
  <li><a href="#Export">{{tr}}GHS{{/tr}}</a></li>
  <li><a href="#Repair">{{tr}}config_facture_hprim{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="PMSI" style="display: none;">
{{mb_include template=inc_config_pmsi}}
</div>

<div id="Export" style="display: none;">
{{mb_include template=inc_config_ghs}}
</div>

<div id="Repair" style="display: none;">
{{mb_include template=inc_config_facture_hprim}}
</div>
