{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPpatients" script="pat_selector"}}
{{mb_include_script module="dPplanningOp" script="cim10_selector"}}
{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="ssr" script="cotation_rhs"}}
{{mb_include_script module="ssr" script="planning"}}

<a class="button new" href="?m=ssr&amp;tab=vw_aed_sejour_ssr&amp;sejour_id=0">
  Admettre un patient
</a>
  
{{mb_include module=ssr template=inc_form_sejour_ssr}}

{{if $sejour->_id && $can->edit}}
<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tab-sejour'));
</script>

<ul id="tab-sejour" class="control_tabs">
  {{if $can_view_dossier_medical}}
  <li><a href="#autonomie">{{tr}}CFicheAutonomie{{/tr}}</a></li>
  <li><a href="#antecedents">{{tr}}CAntecedent{{/tr}}s &amp; {{tr}}CTraitement{{/tr}}s</a></li>
  <li><a href="#bilan">{{tr}}CPrescription{{/tr}} &amp; {{tr}}CBilanSSR{{/tr}}</a></li>
  <li onmousedown="Planification.refresh('{{$sejour->_id}}')">
  	<a href="#planification">
  		Planification
		</a>
	</li>
  <li onmousedown="CotationRHS.refresh('{{$sejour->_id}}')">
  	<a href="#cotation-rhs">
  		Cotation
		</a>
	</li>
  {{/if}} 
</ul>

<hr class="control_tabs" />  

<div id="autonomie" style="display: none;">
  {{mb_include template=inc_form_fiche_autonomie}}
</div>

{{if $can_view_dossier_medical}}
<script type="text/javascript">
function loadAntecedents(sejour_id){
  var url = new Url("dPcabinet","httpreq_vw_antecedents");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate('antecedents')
}

Main.add(loadAntecedents.curry({{$sejour->_id}}));
</script>

<div id="antecedents" style="display: none;">
</div>
{{/if}}

<div id="bilan" style="display: none;">
  {{mb_include template=inc_form_bilan_ssr}}
</div>

<div id="planification" style="display: none;">
  {{mb_include template=inc_planification}}
</div>

<div id="cotation-rhs" style="display: none;">
</div>
{{/if}}