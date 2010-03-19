{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  
// Initialisation des dates pour les calendars
var date = new Date().toDATE();
var dDate = Date.fromDATE(date); 
dDate.addDays(-1);
date = dDate.toDATE();

dates = {  
  limit: {
    start: date,
    stop: null
  }
}


calculDuree = function(date1, date2, oForm, now, prescription_id){
	var dDate1 = Date.fromDATE(date1); 
  var dDate2 = Date.fromDATE(date2); 
  var date = dDate2 - dDate1;
  nb_days = date / (24 * 60 * 60 * 1000);
  oForm.duree.value = parseInt(oForm.duree.value,10) + nb_days;
	
	
  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
    calculSoinSemaine(now,prescription_id); 
  } });
}


addAdministrationPlan = function(line_id, line_class, unite_prise, date, list_administrations){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_add_administration");
  url.addParam("line_id",  line_id);
  url.addParam("key_tab", unite_prise);
  url.addParam("object_class", line_class);
  url.addParam("date", date);
  url.addParam("date_sel", '{{$now}}');
  url.addParam("mode_plan", '1');
  url.addParam("administrations", list_administrations);
  url.addParam("prescription_id", "{{$prescription->_id}}");
  url.popup(600,400,"Administration");
}

submitDossierSoinSemaine = function(oForm){
  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
                      calculSoinSemaine('{{$now}}','{{$prescription->_id}}'); 
  } });
}

submitFinDossierSoinSemaine = function(line_fin, oForm){
  calculDuree(line_fin, oForm._fin.value, oForm, '{{$now}}', '{{$prescription->_id}}');
}

Main.add(function () {
  var tabs = Control.Tabs.create('tab_categories_plan', true);
});

</script>

{{if $prescription->_id}}
	<table>
	  <tr>
	    <td style="width: 1%">
			  <ul id="tab_categories_plan" class="control_tabs_vertical">
				  {{if $prescription->_ref_perfusions_for_plan|@count}}
				    <li><a href="#plan_perf">Perfusions</a></li>
				  {{/if}}
	  			
	  			{{if $prescription->_ref_injections_for_plan|@count}}
					  <li><a href="#plan_inj">Injections</a></li>
			    {{/if}}
			    
				  {{if $prescription->_ref_lines_med_for_plan|@count}}
				    <li><a href="#plan_med">Médicaments</a></li>
				  {{/if}}
					{{assign var=specs_chapitre value=$categorie->_specs.chapitre}}
					{{foreach from=$specs_chapitre->_list item=_chapitre}}
					  {{if @is_array($prescription->_ref_lines_elt_for_plan.$_chapitre)}}
					    <li><a href="#plan_cat-{{$_chapitre}}">{{tr}}CCategoryPrescription.chapitre.{{$_chapitre}}{{/tr}}</a></li>
					  {{/if}}
					{{/foreach}}
			  </ul>
	    </td>
	    <td>
	      <table class="tbl">  
				  <tr>
				    <th class="title">Catégorie</th>
				    <th class="title">Libelle</th>
				    <th class="title">Posologie</th>
				    {{foreach from=$dates item=date}}
				    <th style="width: 1%" class="category">
				      {{$date|date_format:$dPconfig.date}}
				    </th>
				    {{/foreach}}
						 <th colspan="2" class="title">Sign.</th>
				  </tr>
	        
	        {{assign var=transmissions value=$prescription->_transmissions}}	  
	        
	        <!-- Affichage des perfusions -->
	        <tbody id="plan_perf" style="display:none;">
		        {{foreach from=$prescription->_ref_perfusions_for_plan item=_perfusion}}
		          {{include file="../../dPprescription/templates/inc_vw_perf_dossier_soin_semaine.tpl"}}
		        {{/foreach}}
	        </tbody>
	        
	        
	        <!-- Affichage des injections -->
				  <tbody id="plan_inj" style="display: none;">
					  {{foreach from=$prescription->_ref_injections_for_plan key=_key_cat_ATC item=lines_unite_prise_cat name="foreach_line_cat"}}
					    {{foreach from=$lines_unite_prise_cat item=lines_unite_prise name="foreach_line"}}
					      {{foreach from=$lines_unite_prise key=unite_prise item=line_med name="foreach_med"}}
					        <!-- Si l'unite de prise est bien exprimé en format texte et pas en identifiant de prise -->
					        {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin_semaine.tpl" 
					                  line=$line_med 
					                  dosql=do_prescription_line_medicament_aed 
					                  nodebug=true
					                  type="inj"
					                  first_foreach=foreach_med
					                  last_foreach=foreach_line}}    
						    {{/foreach}}
					    {{/foreach}}
					  {{/foreach}}
				  </tbody>
	        
				  <!-- Affichage des medicaments -->
				  <tbody id="plan_med" style="display: none;">
					  {{foreach from=$prescription->_ref_lines_med_for_plan key=_key_cat_ATC item=lines_unite_prise_cat name="foreach_line_cat"}}
					    {{foreach from=$lines_unite_prise_cat item=lines_unite_prise name="foreach_line"}}
					      {{foreach from=$lines_unite_prise key=unite_prise item=line_med name="foreach_med"}}
					        <!-- Si l'unite de prise est bien exprimé en format texte et pas en identifiant de prise -->
					        {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin_semaine.tpl" 
					                  line=$line_med 
					                  dosql=do_prescription_line_medicament_aed 
					                  nodebug=true
					                  type="med"
					                  first_foreach=foreach_med
					                  last_foreach=foreach_line}}    
						    {{/foreach}}
					    {{/foreach}}
					  {{/foreach}}
				  </tbody>
	        
					<!-- Affichage des elements -->
					{{foreach from=$prescription->_ref_lines_elt_for_plan key=name_chap item=elements_chap name="foreach_element"}}
					  {{if !$smarty.foreach.foreach_element.first}}
						</tbody>
						{{/if}}
						<tbody id="plan_cat-{{$name_chap}}" style="display: none;">  
						 {{foreach from=$elements_chap key=name_cat item=elements_cat}}
						   {{assign var=categorie value=$categories.$name_chap.$name_cat}}
						   {{foreach from=$elements_cat item=_element name="foreach_cat"}}
						     {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}} 
						       {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin_semaine.tpl" 
						                   line=$element 
						                   dosql=do_prescription_line_element_aed 
						                   nodebug=true
						                   first_foreach=foreach_cat
								               last_foreach=foreach_elt}} 
						      {{/foreach}}
						    {{/foreach}}
						  {{/foreach}}
						{{/foreach}}
	        </tbody>
	      </table>
	    </td>
	  </tr>
	</table>
{{else}}
  <div class="small-info">
    Ce dossier ne possède pas de prescription de séjour
  </div>
{{/if}} 