{{* $Id: inc_attente.tpl 8487 2010-04-07 10:02:06Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8487 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<div>
	<label style="visibility: hidden;" class="veille" title="Cacher les admissions non-sorties des {{$conf.dPurgences.date_tolerance}} derniers jours">
	  <input type="checkbox" id="admission_ant" onchange="Veille.toggle(this.checked);" />
	  {{tr}}Hide{{/tr}}
	  <span>0</span> admission(s) antérieure(s) 
	</label>
</div>

<script type="text/javascript">
Veille = {
  refresh: function() {
    var label = $$("label.veille")[0];
    var count = $$('tr.veille').length;
    label.setVisibility(count != 0);
    label.down("span").update(count);
	},
	
	toggle: function(checked) {
  	$$('tr.veille').invoke('setVisible', !checked);
	}
}
</script>
