{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Initialisation des variables -->
{{assign var=line value=$_line_element}}
{{assign var=dosql value="do_prescription_line_element_aed"}}
{{assign var=div_refresh value=$element}}
{{assign var=typeDate value=$element}}
{{assign var=category value=$line->_ref_element_prescription->_ref_category_prescription}}

{{assign var=line_guid value=$line->_guid}}

<table class="tbl elt {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}line_stopped{{/if}} {{if $full_line_guid == $line->_guid}}active{{/if}}" id="line_element_{{$line->_id}}">
<tbody class="hoverable">
  <!-- Header de la ligne d'element -->
  <tr>    
    <th id="th_line_CPrescriptionLineElement_{{$line->_id}}" colspan="8"
        class="element {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}} arretee{{/if}}">
      <script type="text/javascript">
         Main.add( function(){
           moveTbodyElt($('line_element_{{$line->_id}}'),'{{$category->_id}}');
         });
      </script>
      <div style="position: absolute">
        <!-- Formulaire ALD -->
        {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}} 
        <!-- Formulaire conditionnel -->
 		   {{include file="../../dPprescription/templates/line/inc_vw_form_conditionnel.tpl"}} 
			 {{if $category->chapitre == "soin"}}
			   {{include file="../../dPprescription/templates/line/inc_vw_form_ide_domicile.tpl"}} 
			 {{/if}}
      </div>
    <div class="div_signature mediuser" {{if !$line->_protocole}}style="border-color: #{{$line->_ref_praticien->_ref_function->color}};"{{/if}}>
        <!-- Affichage de la signature du praticien -->
        {{if $line->_can_view_signature_praticien}}
          {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
        {{elseif !$line->_protocole}}
          {{$line->_ref_praticien->_view}}    
        {{/if}}  
	      <!-- Affichage du formulaire de signature du praticien --> 
        {{if $line->_can_view_form_signature_praticien}} 
	        {{include file="../../dPprescription/templates/line/inc_vw_form_signature_praticien.tpl"}}
	      {{/if}}
	      {{if $full_line_guid == $line->_guid}}
	        <button class="lock notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', '{{$category->chapitre}}', '', '{{$mode_pharma}}', null, '');"></button>
	      {{/if}}
	    </div>
	    <!-- View de l'element -->
	    <strong style="font-size: 1.5em;">
	      {{$line->_ref_element_prescription->_view}}
	    </strong>
	  </th>
	</tr>
	
	{{if $category->chapitre != "dmi"}}
  <tr>
    <td style="width: 25px" {{if $category->chapitre != "dmi"}}rowspan="3"{{/if}} >
      {{if $line->_can_delete_line}}
      <button type="button" class="trash notext" onclick="Prescription.delLineElement('{{$line->_id}}','{{$element}}')">
        {{tr}}Delete{{/tr}}
      </button>
      {{/if}}
    </td>
    <!-- Gestion des dates -->
    <td colspan="2">
      {{include file="../../dPprescription/templates/line/inc_vw_dates.tpl"}}
	      <script type="text/javascript">
          var oForm;
  	      if(oForm = getForm("editDates-{{$typeDate}}-{{$line->_id}}", true)){
  		      Calendar.regField(oForm.debut, dates);
  		      Calendar.regField(oForm._fin, dates);
  		      Calendar.regField(oForm.fin, dates);
  	      }
	      </script>
    </td>
  </tr>
  <!-- Affichage des pososlogies -->
  {{if $category->chapitre != "anapath" && $category->chapitre != "consult" && $category->chapitre != "imagerie"}}
  <tr>
    <td colspan="3">
      {{if $line->_can_modify_poso}}
	      <table style="width: 100%">
	       <tr>
			    <td style="border:none; border-right: 1px solid #999; width:5%; text-align: left;">
			      {{include file="../../dPprescription/templates/line/inc_vw_add_posologies.tpl" type="Soin"}}
			    </td>
			    <td style="border:none">
			      <img src="images/icons/a_right.png" />
			    </td>
				  <td style="border:none; text-align: left;" id="prises-{{$typeDate}}{{$line->_id}}">
			        <!-- Parcours des prises -->
			        {{include file="../../dPprescription/templates/line/inc_vw_prises_posologie.tpl" type="Soin"}}
			    </td>
	      </table>
      {{else}}
        <table>
          <tr>
			      <td style="border:none;"> 
			        <!-- Affichage des prises -->
			        {{if $line->_ref_prises|@count}}
			        <ul>
			        {{foreach from=$line->_ref_prises item=prise name=foreach_prise}}
			          <li>{{$prise->_view}}</li>
			        {{/foreach}}
			        </ul>
			        {{else}}
			         Aucune posologie
			        {{/if}}
			      </td>
          </tr>
        </table>
      {{/if}}
    </td>
  </tr>
  {{/if}}
  {{/if}}
  <tr>
    {{if $category->chapitre == "dmi"}}
    <td style="width: 25px">
      {{if $line->_can_delete_line}}
      <button type="button" class="trash notext" onclick="Prescription.delLineElement('{{$line->_id}}','{{$element}}')">
        {{tr}}Delete{{/tr}}
      </button>
      {{/if}}
    </td>
    {{/if}}
    <td {{if $category->chapitre != "dmi"}}colspan="3"{{else}}colspan="6"{{/if}}>
      {{if $prescription->type != "sortie" || $line->_protocole}}
	      <div style="float: right">
	        <!-- Formulaire de selection d'un executant -->
	        {{include file="../../dPprescription/templates/line/inc_vw_form_executants.tpl"}}
	      </div>
      {{/if}}
      <!-- Formulaire d'ajout de commentaire -->
      {{if $line->_protocole}}
	      {{assign var=_line_praticien_id value=$app->user_id}}
	    {{else}}
	      {{assign var=_line_praticien_id value=$line->praticien_id}}
	    {{/if}}
      <form name="commentaire-{{$line->_guid}}">
	      {{include file="../../dPprescription/templates/line/inc_vw_form_add_comment.tpl"}}
	        {{if $line->_perm_edit}}
		      <select name="_helpers_commentaire" size="1" onchange="pasteHelperContent(this); this.form.commentaire.onchange();" style="width: 110px;">
		        <option value="">&mdash; Aide</option>
		        {{html_options options=$aides_prescription.$_line_praticien_id.CPrescriptionLineElement}}
		      </select>
		      <input type="hidden" name="_hidden_commentaire" value="" />
		      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CPrescriptionLineElement', this.form._hidden_commentaire, 'commentaire');">
		        Nouveau
		      </button>
		      {{/if}}
	    </form>
    </td>   
  </tr>
 
	{{if (($category->chapitre == "biologie" || $category->chapitre == "kine" || $category->chapitre == "soin" || $category->chapitre == "dm") && $prescription->type != "sortie") && !$line->_protocole }}
  <tr>
  <td></td>
    <td>
     {{if ($prescription->type == "sejour" || $prescription->type == "pre_admission") && !$line->_protocole && $line->signee && ($is_praticien || @$operation_id || $can->admin)}}
        <div id="stop-CPrescriptionLineElement-{{$line->_id}}"> 
          {{include file="../../dPprescription/templates/line/inc_vw_stop_line.tpl" object_class="CPrescriptionLineElement"}}
        </div>
     {{/if}}
    </td>
  </tr>
  {{/if}}
</tbody>
</table>