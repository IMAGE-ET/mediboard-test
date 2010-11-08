{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function startCCAM() {
  var CCAMUrl = new Url;
  CCAMUrl.setModuleAction("dPccam", "httpreq_do_add_ccam");
  CCAMUrl.requestUpdate("ccam");
}

function startNGAP(){
  var NGAPUrl = new Url;
  NGAPUrl.setModuleAction("dPccam", "httpreq_do_add_ngap");
  NGAPUrl.requestUpdate("ngap");
}
	
Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#CCAM">{{tr}}CCAM{{/tr}}</a></li>
  <li><a href="#NGAP">{{tr}}NGAP{{/tr}}</a></li>
  <li><a href="#FraisDivers">{{tr}}CFraisDivers{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="CCAM" style="display: none;">
{{mb_include template=inc_config_ccam}}
</div>

<div id="NGAP" style="display: none;">
{{mb_include template=inc_config_ngap}}
</div>

<div id="FraisDivers" style="display: none;">
{{mb_include template=inc_config_frais_divers}}
</div>