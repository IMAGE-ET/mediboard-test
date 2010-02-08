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

<a class="button new" href="?m=ssr&amp;tab=vw_aed_sejour_ssr&amp;sejour_id=0">
  Admettre un patient
</a>
  
{{mb_include module=ssr template=inc_form_sejour_ssr}}

{{if $sejour->_id && $can->edit}}
<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tab-sejour', true));
</script>

<ul id="tab-sejour" class="control_tabs">
  {{if $can_view_dossier_medical}}
  <li><a href="#antecedents">{{tr}}CAntecedent{{/tr}} &amp; {{tr}}CTraitement{{/tr}}</a></li>
  <li><a href="#autonomie">{{tr}}CFicheAutonomie{{/tr}}</a></li>
  <li><a href="#bilan_ssr">{{tr}}CBilanSSR{{/tr}}</a></li>
  {{/if}} 
</ul>

<hr class="control_tabs" />  

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
  <div class="small-info">
    Veuillez sélectionner un séjour dans la liste de gauche pour pouvoir
    consulter et modifier les antécédents du patient concerné.
  </div>
</div>
{{/if}}

<div id="autonomie" style="display: none;">
  {{mb_include template=inc_form_fiche_autonomie}}
</div>

<div id="bilan_ssr" style="display: none;">
  {{mb_include template=inc_form_bilan_ssr}}
</div>

{{/if}}