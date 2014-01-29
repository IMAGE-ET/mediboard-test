{{*
 * $Id$
 *  
 * @category dPplanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script type="text/javascript">
  Main.add(function(){
    Control.Tabs.create("custom-cpi-mode-entre-sortie", true);
  });
</script>
<ul class="control_tabs" id="custom-cpi-mode-entre-sortie">
  <li><a href="#tab-CChargePriceIndicator">{{tr}}CChargePriceIndicator{{/tr}}</a></li>
  <li><a href="#tab-CModeEntreeSejour">{{tr}}CModeEntreeSejour{{/tr}}</a></li>
  <li><a href="#tab-CModeSortieSejour">{{tr}}CModeSortieSejour{{/tr}}</a></li>
</ul>

<div id="tab-CChargePriceIndicator" style="display: none;">
  {{mb_include template=CChargePriceIndicator_config}}
</div>

<div id="tab-CModeEntreeSejour" style="display: none;">
  {{mb_include template=CModeEntreeSortieSejour_config list_modes=$list_modes_entree mode_class=CModeEntreeSejour}}
</div>

<div id="tab-CModeSortieSejour" style="display: none;">
  {{mb_include template=CModeEntreeSortieSejour_config list_modes=$list_modes_sortie mode_class=CModeSortieSejour}}
</div>