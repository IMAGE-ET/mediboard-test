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
  new Url("ccam", "httpreq_do_add_ccam")
  .requestUpdate("ccam");
}

function startNGAP(){
  new Url("ccam", "httpreq_do_add_ngap")
  .requestUpdate("ngap");
}

function startCCAM_ICR() {
  new Url("ccam", "ajax_do_add_ccam_ICR")
  .requestUpdate("ccam_icr");
}

function startCCAM_radio() {
  new Url("ccam", "ajax_do_add_ccam_radio")
  .requestUpdate("ccam_radio");
}

function startCCAM_ngap() {
  new Url("dPccam", "ajax_do_add_ccam_ngap")
  .requestUpdate("ccam_ngap");
}

function startForfaits(){
  new Url("dPccam", "httpreq_do_add_forfaits")
  .requestUpdate("forfaits");
}

function modalImportFavoris() {
  new Url("ccam", "ajax_import_favoris")
  .pop(640, 400);
}

Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#CCAM">{{tr}}CCAM{{/tr}}</a></li>
  <li><a href="#NGAP">{{tr}}NGAP{{/tr}}</a></li>
  <li><a href="#ccam_ICR">CCAM ICR</a></li>
  <li><a href="#ccam_RADIO">CCAM radio</a></li>
  <li><a href="#ccam_NGAP">CCAM ngap</a></li>
  <li><a href="#FraisDivers">{{tr}}CFraisDivers{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="CCAM" style="display: none;">
{{mb_include template=inc_config_ccam}}
</div>

<div id="NGAP" style="display: none;">
{{mb_include template=inc_config_ngap}}
</div>

<div id="ccam_ICR" style="display: none;">
{{mb_include template=inc_config_ccam_ICR}}
</div>

<div id="ccam_RADIO" style="display: none;">
{{mb_include template=inc_config_ccam_radio}}
</div>

<div id="ccam_NGAP" style="display: none;">
{{mb_include template=inc_config_ccam_ngap}}
</div>


<div id="FraisDivers" style="display: none;">
{{mb_include template=inc_config_frais_divers}}
</div>