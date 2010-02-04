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
  <li>
  	<a href="#techniciens">
  	  {{tr}}CPlateau-back-techniciens{{/tr}}
		</a>
  </li>
  <li>
  	<a href="#equipements">
      {{tr}}CPlateau-back-equipements{{/tr}}
		</a>
	</li>
</ul>

<hr class="control_tabs" />

<div id="techniciens" style="display: none;">
  Techniciens
</div>

<div id="equipements" style="display: none;">
  Equipements
</div>