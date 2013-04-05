{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $conf.ssr.CPrescription.show_dossier_soins}}
  {{mb_script module="soins" script="plan_soins"}}
  
  {{if "dPprescription"|module_active}}
    {{mb_script module="dPprescription" script="prescription"}}
    {{mb_script module="dPprescription" script="element_selector"}}
  {{/if}}
  
  {{if "dPmedicament"|module_active}}
    {{mb_script module="dPmedicament" script="medicament_selector"}}
    {{mb_script module="dPmedicament" script="equivalent_selector"}}
  {{/if}}
  
  <script type="text/javascript">
  
  showDossierSoins = function(sejour_id, date, default_tab){
    $('dossier_sejour').update("");
    var url = new Url("soins", "ajax_vw_dossier_sejour");
    url.addParam("sejour_id", sejour_id);
    if(default_tab){
      url.addParam("default_tab", default_tab);
    }
    url.requestUpdate($('dossier_sejour'));
    modalWindow = modal($('dossier_sejour'));
  }
  
  </script>
{{/if}}

{{mb_script module=ssr script=sejours_ssr}}

{{if $dialog}}
  {{mb_include style=mediboard template=open_printable}}
{{else}}
	{{if $can->edit && !$conf.ssr.recusation.sejour_readonly}} 
	<a class="button new" href="?m=ssr&amp;tab=vw_aed_sejour_ssr&amp;sejour_id=0">
	  Créer une demande de prise en charge
	</a>
	{{/if}}

	<button type="button" class="print" style="float: right;" onclick="new Url().setModuleAction('{{$m}}', '{{$action}}').popup(800, 600);">
	  {{tr}}Print{{/tr}}
	</button>
{{/if}}
	
<form class="not-printable" name="Filter" action="?" method="get" style="float: right;" onsubmit="this.submit();">
  <input name="m" value="{{$m}}" type="hidden" />
  <input name="{{$actionType}}" value="{{$action}}" type="hidden" />
  <input name="dialog" value="{{$dialog}}" type="hidden" />

  <input name="service_id"   value="{{$filter->service_id}}"   type="hidden" onchange="this.form.onsubmit()" />
  <input name="praticien_id" value="{{$filter->praticien_id}}" type="hidden" onchange="this.form.onsubmit()" />
  <input name="referent_id"  value="{{$filter->referent_id}}"  type="hidden" onchange="this.form.onsubmit()" />

	{{if $dialog}} 
  <input type="checkbox" name="group_by" value="1" {{if $group_by}} checked="checked" {{/if}} onclick="this.form.onsubmit();">
  <label for="group_by">
    Regrouper par kiné
  </label>
  &mdash;
	{{/if}}

  {{mb_include template=inc_show_cancelled_services}}
	&mdash;

  Prescription
  <select name="show" onchange="this.form.submit();">
    <option value="all"     {{if $show == "all"    }} selected="selected"{{/if}}>Tous les séjours</option>
    <option value="nopresc" {{if $show == "nopresc"}} selected="selected"{{/if}}>Séjours sans prescription</option>
  </select>
</form>

{{if $group_by}} 
  {{foreach from=$kines item=_kine name=kines}}
  	{{assign var=kine_id value=$_kine->_id}}
    <h1 {{if $smarty.foreach.kines.first}} class="no-break" {{/if}}>
      {{$_kine}}
    </h1>
    {{mb_include template=inc_sejours_ssr sejours=$sejours_by_kine.$kine_id}}
  {{/foreach}}   
  
  {{if !$filter->referent_id}}
    {{assign var=kine_id value=""}}
    <h1>
    	<em>
    		&mdash; {{tr}}None{{/tr}} {{tr}}CBilanSSR-kine_id{{/tr}}
  		</em>
  	</h1>
    {{mb_include template=inc_sejours_ssr sejours=$sejours_by_kine.$kine_id}}
  {{/if}}
{{else}}
{{mb_include template=inc_sejours_ssr sejours=$sejours}}
{{/if}}

{{if $dialog}}
  {{mb_include style=mediboard template=close_printable}}
{{/if}}

{{if $conf.ssr.CPrescription.show_dossier_soins}}
<div id="dossier_sejour" style="width: 95%; height: 90%; overflow: auto; display: none;"></div>
{{/if}}