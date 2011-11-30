{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul id="tab_categories" class="control_tabs_vertical">   
  {{if $prescription->_ref_prescription_line_mixes_for_plan_by_type|@count}}
    {{foreach from=$prescription->_ref_prescription_line_mixes_for_plan_by_type key=type_line_mix item=_lines_mix}}
    <li onmousedown="refreshDossierSoin(null, '{{$type_line_mix}}');">
      <a href="#_{{$type_line_mix}}">
        {{tr}}CPrescription._chapitres.{{$type_line_mix}}{{/tr}} <small>({{$prescription->_nb_lines_plan_soins.$type_line_mix}})</small>
        {{if $count_recent_modif.$type_line_mix}}
          <img src="images/icons/ampoule.png" 
					{{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
					  title="Fermer les alertes du chapitre"
            onclick="PlanSoins.closeAllAlertes('_{{$type_line_mix}}', this, false, '_{{$type_line_mix}}');"
          {{else}}
           title="Ligne recemment modifiée" 
          {{/if}} />
        {{/if}} 
      </a>
    </li>
    {{/foreach}}
  {{/if}}
  
  {{if $prescription->_ref_injections_for_plan|@count}}
  <li onmousedown="refreshDossierSoin(null, 'inj');">
    <a href="#_inj">Injections <small>({{$prescription->_nb_lines_plan_soins.inj}})</small>
      {{if $count_recent_modif.inj}}
        <img src="images/icons/ampoule.png" 
				{{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
				    title="Fermer les alertes du chapitre"
            onclick="PlanSoins.closeAllAlertes('_inj', this, false, '_inj');"
        {{else}}
           title="Ligne recemment modifiée" 
        {{/if}} />
      {{/if}}

      {{if $count_urgence.inj}}
        <img src="images/icons/ampoule_urgence.png" 
				{{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
            title="Fermer les urgences"
            onclick="PlanSoins.closeAllAlertes('_inj', this, true, '_inj');"
        {{else}}
           title="Urgence" 
        {{/if}} />
      {{/if}}
    </a></li>
  {{/if}}
  
  {{if $prescription->_ref_lines_med_for_plan|@count}}
    <li onmousedown="refreshDossierSoin(null, 'med');">
      <a href="#_med">Médicaments <small>({{$prescription->_nb_lines_plan_soins.med}})</small>
        {{if $count_recent_modif.med}}
	        <img src="images/icons/ampoule.png"
					{{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
					  title="Fermer les alertes du chapitre"
					  onclick="PlanSoins.closeAllAlertes('_med', this, false, '_med');"
					{{else}}
					 title="Ligne recemment modifiée" 
					{{/if}} />
        {{/if}}
        {{if $count_urgence.med}}
          <img src="images/icons/ampoule_urgence.png" 
					{{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
            title="Fermer les urgences"
            onclick="PlanSoins.closeAllAlertes('_med', this, true, '_med');"
	        {{else}}
	           title="Urgence" 
	        {{/if}} />
        {{/if}}    
      </a>
    </li>
  {{/if}}
  {{assign var=specs_chapitre value=$categorie->_specs.chapitre}}
  {{foreach from=$specs_chapitre->_list item=_chapitre}}
    {{if @is_array($prescription->_ref_lines_elt_for_plan.$_chapitre)}}
    <li onmousedown="refreshDossierSoin(null, '{{$_chapitre}}');">
      <a href="#_cat-{{$_chapitre}}">{{tr}}CCategoryPrescription.chapitre.{{$_chapitre}}{{/tr}} <small>({{$prescription->_nb_lines_plan_soins.$_chapitre}})</small>
        {{if $count_recent_modif.$_chapitre}}
        <img src="images/icons/ampoule.png"
          {{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
					  title="Fermer les alertes du chapitre"
            onclick="PlanSoins.closeAllAlertes('_{{$_chapitre}}', this, false, '_cat-{{$_chapitre}}');"
          {{else}}
           title="Ligne recemment modifiée" 
          {{/if}} />
        {{/if}}
        {{if $count_urgence.$_chapitre}}
          <img src="images/icons/ampoule_urgence.png"
					{{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
            title="Fermer les urgences"
            onclick="PlanSoins.closeAllAlertes('_{{$_chapitre}}', this, true, '_cat-{{$_chapitre}}');"
	        {{else}}
	           title="Urgence" 
	        {{/if}} />
        {{/if}}
      </a></li>
    {{/if}}
  {{/foreach}}
	
	{{if $conf.dPprescription.CPrescription.show_inscription}}
	<!-- Volet Inscription --> 
	<li onmousedown="refreshDossierSoin(null, 'inscription');">
    <a href="#_inscription" {{if $prescription->_count_inscriptions == 0}}class="empty"{{/if}}>Inscription  <small>({{$prescription->_count_inscriptions}})</small></a>
	</li>
	{{/if}}
</ul> 