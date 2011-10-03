{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=advanced_prot value=0}}
{{assign var=element value=$line->_chapitre}}
<!-- Initialisation des variables -->
{{assign var=dosql value="do_prescription_line_element_aed"}}

{{if $line->inscription}}
  {{assign var=div_refresh value="inscription"}}
{{else}}
  {{assign var=div_refresh value=$element}}
{{/if}}

{{assign var=typeDate value=$element}}
{{assign var=category value=$line->_ref_element_prescription->_ref_category_prescription}}

{{assign var=line_guid value=$line->_guid}}

{{mb_include module="dPprescription" template="inc_header_line"}}

<table class="tbl elt" id="full_line_element_{{$line->_id}}">
  <!-- Header de la ligne d'element -->
  <tr>    
    <th colspan="8" class="{{if $line->perop}}perop{{/if}}">
      <div style="position: absolute">
      	<a title="Historique" class="button list notext" href="#1"
           onclick="Prescription.showLineHistory('{{$line->_guid}}')">
        </a>
				
        <!-- Formulaire ALD -->
        {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}} 
        
        {{if $line->_perm_edit}}
          <input name="perop" type="checkbox" {{if $line->perop}}checked="checked"{{/if}} onchange="submitPerop('{{$line->_class}}','{{$line->_id}}',this.checked)"  />
          {{mb_label object=$line field="perop"}}
        {{elseif !$line->_protocole}}
          {{mb_label object=$line field="perop"}}:
          {{if $line->perop}}Oui{{else}}Non{{/if}} 
        {{/if}}
        
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
        <button class="lock notext" onclick="modalPrescription.close(); Prescription.reload.defer('{{$prescription->_id}}', '', '{{$div_refresh}}', '', '{{$mode_pharma}}', null, '', null, {{$advanced_prot}});"></button>
      </div>
      <!-- View de l'element -->
      <strong style="font-size: 1.5em;">
        {{$line->_ref_element_prescription->_view|spancate:30}}
      </strong>
			
			{{mb_include module=forms template=inc_widget_ex_class_register object=$line event=prescription}}
    </th>
  </tr>
</table>

<table class="main layout">
	{{assign var=_category_chapitre value=$category->chapitre}}
  {{if $category->chapitre != "dmi"}}
	  <tr>
	    <!-- Dates -->
	    <td>
				<fieldset>
					<legend>
						  Durée de la prescription
					</legend>
	        {{include file="../../dPprescription/templates/line/inc_vw_dates.tpl"}}
				</fieldset>
				
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
		
	  <!-- Posologies -->
    <tr>
	    <td>
	      {{if $line->_can_modify_poso}}
				  <fieldset style="float:left; width: 48%;">
	          <legend>
	            Choix d'une posologie
	          </legend>
			      {{include file="../../dPprescription/templates/line/inc_vw_add_posologies.tpl" type="Soin"}}
					</fieldset>
	      {{/if}}
	        
				<fieldset {{if $line->_can_modify_poso}}style="float: right; width: 48%"{{/if}}>
	        <legend>
	          Posologie selectionnée
	        </legend>
					{{if $line->_can_modify_poso}}
	          <div id="prises-{{$typeDate}}{{$line->_id}}">
	       	    {{include file="../../dPprescription/templates/line/inc_vw_prises_posologie.tpl" type="Soin"}}
				    </div>
					{{else}}
	          <!-- Affichage des prises -->
	          {{if $line->_ref_prises|@count}}
	            <ul>
	              {{foreach from=$line->_ref_prises item=prise name=foreach_prise}}
	                <li>{{$prise->_view}}</li>
	              {{/foreach}}
	            </ul>
	          {{else}}
	            Aucune posologie, ce {{$line->_unite_prise}} sera donc effectué <strong>une fois</strong> à la date et heure de début de prescription.
	          {{/if}}
					{{/if}}
	      </fieldset>
	    </td>
	  </tr>
  {{/if}}
	
	<!-- Commentaire --> 
	{{if $line->_can_modify_comment || $line->commentaire}}
  <tr>
    <td class="text">
      
      <!-- Formulaire d'ajout de commentaire -->
      {{if $line->_protocole}}
        {{assign var=_line_praticien_id value=$app->user_id}}
      {{else}}
        {{assign var=_line_praticien_id value=$line->praticien_id}}
      {{/if}}
    
      <script type="text/javascript">
        Main.add( function(){
          var oFormCommentaireElement = getForm("editCommentaire-{{$line->_guid}}");
          if (!oFormCommentaireElement.commentaire) {
            return;
          }
          new AideSaisie.AutoComplete(oFormCommentaireElement.commentaire, {
            objectClass: "{{$line->_class}}", 
            contextUserId: "{{$_line_praticien_id}}",
            resetSearchField: false,
            validateOnBlur: false,
            strict: false,
            dependField1: getForm("libelleProduit").element_prescription_id
          });
        });
      </script>
      
     <!-- Input caché pour l'autocomplete -->
     <form name="libelleProduit" method="get" action="?">
       <input type="hidden" name="element_prescription_id" value="{{$line->_ref_element_prescription->libelle}}" />
     </form>
     
      <form name="editCommentaire-{{$line->_guid}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$line}}
        
				<fieldset>
					<legend>
						{{mb_label object=$line field="commentaire"}}
					</legend>
			
	        {{if $line->_can_modify_comment}}
	          {{mb_field object=$line field="commentaire" onblur="this.form.onsubmit();"}}
	        {{else}}
	          {{if $line->commentaire}}
	            {{mb_value object=$line field="commentaire"}}
	          {{/if}}
	        {{/if}}
				</fieldset>
      </form>
    </td>   
  </tr>
	{{/if}}
	
	<!-- Choix d'un DM associé à un soin --> 
  {{if ($category->chapitre == "soin" && $line->_perm_edit) || $line->cip_dm}}
  <tr>
  	<td>
  		<fieldset>
  			<legend>DM</legend>
				{{if $category->chapitre == "soin" && $line->_perm_edit}}
	        <button type="button" onclick="$('addDM-{{$line->_guid}}').toggle();" class="add">Ajouter DM</button>
	      {{/if}}
  		
				<span id="addDM-{{$line->_guid}}" {{if !$line->cip_dm}}style="display: none;"{{/if}}>
					<span id="vw_dm-{{$line->_id}}">
	          {{mb_include module="dPprescription" template="inc_vw_element_dm"}}
		      </span>
				</span>
			</fieldset>
  	</td>
	</tr>
	{{/if}}
  
	<!-- Executant -->
	{{if ($prescription->type != "sortie" || $line->_protocole) && @is_array($executants) && (array_key_exists($category_id, $executants.externes) || array_key_exists($category_id, $executants.users))}}
    <tr>
    	<td>
   		  <fieldset>
	        <legend>
	          Exécutant
	        </legend>
	        <!-- Formulaire de selection d'un executant -->
	        {{include file="../../dPprescription/templates/line/inc_vw_form_executants.tpl"}}
	      </fieldset>
      </td>
    </tr> 
	{{/if}}
	
	<!-- Evolution et actions -->
	{{if !$line->_protocole}}
	<tr>
		<td>
			{{if (($category->chapitre == "biologie" || $category->chapitre == "kine" || $category->chapitre == "soin" || $category->chapitre == "dm" || $category->chapitre == "med_elt") && $prescription->type != "sortie") && !$line->_protocole }}
        {{if ($prescription->type == "sejour" || $prescription->type == "pre_admission") && !$line->_protocole && $line->signee && !$line->recusee}}
          <fieldset style="float: left; width: 48%;">
				    <legend>Evolution</legend>
						<div id="stop-CPrescriptionLineElement-{{$line->_id}}"> 
		          {{include file="../../dPprescription/templates/line/inc_vw_stop_line.tpl" object_class="CPrescriptionLineElement"}}
		        </div>
			    </fieldset>
		     {{/if}}
		  {{/if}}

      {{if ($line->_can_delete_line || ($line->signee && ($app->user_id == $line->praticien_id || $line->inscription) || !$line->signee)) && !$advanced_prot}}
			<fieldset style="float: right; width: 48%;">
			 	<legend>
			 		Actions
			 	</legend>
				{{if $line->_can_delete_line}}
		      <button type="button" class="trash"
		              onclick="
				          if (Prescription.confirmDelLine('{{$line->_view|smarty:nodefaults|JSAttribute}}')) {
				            modalPrescription.close();
				            Prescription.delLineElement('{{$line->_id}}','{{$div_refresh}}');
				          }">
		        {{tr}}Delete{{/tr}}
		      </button>
		    {{/if}}
        <!-- Affichage du formulaire de signature du praticien --> 
        {{if $line->_can_view_form_signature_praticien}} 
          {{include file="../../dPprescription/templates/line/inc_vw_form_signature_praticien.tpl"}}
        {{/if}}
      </fieldset>
			{{/if}}
		</td>
	</tr>
	{{/if}}
</table>