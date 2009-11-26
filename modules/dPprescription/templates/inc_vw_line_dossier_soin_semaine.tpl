{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=line_id value=$line->_id}}
{{assign var=line_class value=$line->_class_name}}


<tr>
  {{if $smarty.foreach.$first_foreach.first && $smarty.foreach.$last_foreach.first}}
    {{if $line_class == "CPrescriptionLineMedicament"}}
      {{assign var=libelle_ATC value=$line->_ref_produit->_ref_ATC_2_libelle}}
      <!-- Cas d'une ligne de medicament -->
      <th class="text {{if @$transmissions.ATC.$libelle_ATC|@count}}transmission{{else}}transmission_possible{{/if}}" 
          rowspan="{{$prescription->_nb_produit_by_cat.$type.$_key_cat_ATC}}"
          onclick="addCibleTransmission('','','{{$libelle_ATC}}','{{$libelle_ATC}}');">

	      <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$libelle_ATC}}')">
            {{$libelle_ATC}}
        </span>
        <div id="tooltip-content-{{$libelle_ATC}}" style="display: none; color: black; text-align: left">
       		{{if @is_array($transmissions.ATC.$libelle_ATC)}}
  		      <ul>
  			  {{foreach from=$transmissions.ATC.$libelle_ATC item=_trans}}
  			    <li>{{$_trans->_view}} le {{$_trans->date|date_format:$dPconfig.datetime}}:<br /> {{$_trans->text}}</li>
  			  {{/foreach}}
  		      </ul>
  			{{else}}
  			  Aucune transmission
  			{{/if}}
		  </div>
      </th>
    {{else}}
        <!-- Cas d'une ligne d'element, possibilité de rajouter une transmission à la categorie -->
        {{assign var=categorie_id value=$categorie->_id}}
        <th class="text {{if @$transmissions.CCategoryPrescription.$name_cat|@count}}transmission{{else}}transmission_possible{{/if}}" 
            rowspan="{{$prescription->_nb_produit_by_cat.$name_cat}}" 
            onclick="addCibleTransmission('CCategoryPrescription','{{$name_cat}}','{{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} - {{$categorie->nom}}');">
          <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$name_cat}}')">
            {{$categorie->nom}}
          </span>
          <div id="tooltip-content-{{$name_cat}}" style="display: none; color: black; text-align: left">
       		{{if @is_array($transmissions.CCategoryPrescription.$name_cat)}}
  		      <ul>
  			  {{foreach from=$transmissions.CCategoryPrescription.$name_cat item=_trans}}
  			    <li>{{$_trans->_view}} le {{$_trans->date|date_format:$dPconfig.datetime}}:<br /> {{$_trans->text}}</li>
  			  {{/foreach}}
  		      </ul>
  			{{else}}
  			  Aucune transmission
  			{{/if}}
		  </div>
	    </th>
    {{/if}}
  {{/if}}	
  
   <!-- Affichage du libelle de la ligne -->
   <td style="width: 1%;" class="text">
     <div class="mediuser" style="border-color: #{{$line->_ref_praticien->_ref_function->color}}">
       <div onclick='addCibleTransmission("{{$line_class}}","{{$line->_id}}","{{$line->_view}}");' 
	       class="{{if @$transmissions.$line_class.$line_id|@count}}transmission{{else}}transmission_possible{{/if}}">
	    <a href="#{{$line->_guid}}" onmouseover="ObjectTooltip.createEx(this, '{{$line->_guid}}')">
	      {{if $line_class == "CPrescriptionLineMedicament"}}
	        {{$line->_ucd_view}}  - <span style="font-size: 0.8em">{{$line->_forme_galenique}}</span>
	        {{if $line->traitement_personnel}} (Traitement perso){{/if}}
	        {{if $line->commentaire}}<br /> ({{$line->commentaire}}){{/if}}
	      {{else}}
	        {{$line->_view}}
	      {{/if}} 
	    </a>
	  </div>
	  {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	    {{$line->voie}}
	  {{/if}}
	  </div>
   </td>
   
   <!-- Affichage de la prise -->
	 <td class="text">
	   <small>
	   {{if @$line->_prises_for_plan.$unite_prise}}
	     {{if is_numeric($unite_prise)}}
	       <!-- Cas des posologies de type "tous_les", "fois par" ($unite_prise == $prise->_id) -->
	       <div style="white-space: nowrap;">
		       {{assign var=prise value=$line->_prises_for_plan.$unite_prise}}
		       {{$prise->_short_view}}
		       <br />
		       {{if $line->_class_name == "CPrescriptionLineMedicament"}}
					   {{if $line->_ref_produit_prescription->_id}}
						   ({{$prise->_ref_object->_ref_produit_prescription->unite_prise}})<br />
						 {{else}}
		           ({{$prise->_ref_object->_unite_administration}})<br />
						 {{/if}}
		       {{/if}}
	       </div>
	     {{else}}
	       <!-- Cas des posologies sous forme de moments -->
	       {{foreach from=$line->_prises_for_plan.$unite_prise item=_prise}}
	         <div style="white-space: nowrap;">
	           {{$_prise->_short_view}}
					 </div>
	       {{/foreach}}
	       {{if $line->_class_name == "CPrescriptionLineMedicament"}}
				   {{if $line->_ref_produit_prescription->_id}}
             ({{$_prise->_ref_object->_ref_produit_prescription->unite_prise}})<br />
					 {{else}}
	           ({{$_prise->_ref_object->_unite_administration}})<br />
	         {{/if}}
				 {{/if}}
	     {{/if}}
	   {{/if}}
	   </small>
	 </td>
   
   {{foreach from=$dates item=date name="foreach_date"}}
     <td style="{{if $date < $line->debut || $line->_fin_reelle && $date > $line->_fin_reelle|date_format:'%Y-%m-%d'}}background-color: #ddd;{{/if}}width: 20%; text-align: center"> 		          
	    {{if $date < $line->debut || $line->_fin_reelle && $date > $line->_fin_reelle|date_format:'%Y-%m-%d'}}
	      - 
	    {{else}}
	      {{assign var=nb_administre value="0"}}
	      {{if @$line->_administrations_by_line.$unite_prise.$date}}
	        {{assign var=nb_administre value=$line->_administrations_by_line.$unite_prise.$date}}
	      {{/if}}
	      {{assign var=nb_prevue value="0"}}
	      {{if @$line->_quantity_by_date.$unite_prise.$date.total}}
	        {{assign var=nb_prevue value=$line->_quantity_by_date.$unite_prise.$date.total}} 
	      {{/if}}
	      
	      {{assign var=list_administrations value=""}}
	      {{if @$line->_administrations.$unite_prise.$date.list}}
	        {{assign var=list_administrations value=$line->_administrations.$unite_prise.$date.list}}
	      {{/if}}
	 
	      <span id="span-{{$line_id}}-{{$unite_prise}}-{{$date}}" class="administration 
	      						
			              {{if $nb_prevue > 0}}
					            {{if $nb_administre}}
						            {{if $nb_administre == $nb_prevue}} administre
						            {{elseif $nb_administre != $nb_prevue}} administration_partielle
						            {{/if}}
					            {{else}}
					              {{if $date < $now}} non_administre
					              {{else}} a_administrer
					              {{/if}}
						          {{/if}}
					          {{/if}}"
					          onclick='addAdministrationPlan("{{$line->_id}}","{{$line->_class_name}}","{{$unite_prise}}","{{$date}}","{{$list_administrations}}");' 
	                  onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$date}}")'>
	        <!-- Affichage des administrations effectuée et des prises prevues -->
		      {{$nb_administre}} / {{$nb_prevue}}
	    </span>    
	    
	    <span id="tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$date}}" style="display: none; text-align: left; border: none;">
		   {{if @is_array($line->_administrations.$unite_prise.$date)}}
			     <ul>
			      {{foreach from=$line->_administrations.$unite_prise.$date key=hour item=administrations_by_hours}}
			      {{if $hour != 'list'}}
			      {{if @$administrations_by_hours.administrations}}
					   {{foreach from=$administrations_by_hours.administrations item=_log_administration}}
					     {{assign var=administration_id value=$_log_administration->_ref_object->_id}}
					    
					     {{if $line_class == "CPrescriptionLineMedicament"}}
						     <li>{{$_log_administration->_ref_object->quantite}} {{$_log_administration->_ref_object->_ref_object->_ref_produit->libelle_unite_presentation}} administré par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->_ref_object->dateTime|date_format:$dPconfig.datetime}}</li>		 
						   {{else}}
							   <li>{{$_log_administration->_ref_object->quantite}} {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} effectué par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->_ref_object->dateTime|date_format:$dPconfig.datetime}}</li>		         				        
						   {{/if}}        
						
						   {{if @$line->_transmissions.$unite_prise.$date.$hour.list.$administration_id}}
							 <script type="text/javascript">
					       $("span-{{$line_id}}-{{$unite_prise}}-{{$date}}").addClassName('transmission');
					     </script>  
					     <ul>
						     {{foreach from=$line->_transmissions.$unite_prise.$date.$hour.list.$administration_id item=_transmission}}
			             <li>{{$_transmission->_view}} le {{$_transmission->date|date_format:$dPconfig.datetime}}:<br /> {{$_transmission->text}}</li>
						     {{/foreach}}
						   </ul>
						   {{/if}}
					   {{/foreach}}
					   {{/if}}
					   {{/if}}
					   {{/foreach}}
				   </ul>
		     {{else}}
			     {{if $line_class == "CPrescriptionLineMedicament"}}
			       Aucune administration
			     {{else}}
			       Pas de {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}}
			     {{/if}}
		   {{/if}}
       </span>
    {{/if}}
    <br />
   
     {{if $line->debut == $date || $line->_fin == $date}}
	     <table>
	       <tr>
	         <td style="border: none;">
			      <form name="modifDates-{{$line_class}}-{{$line_id}}-{{$unite_prise}}-{{$date}}" action="?" method="post" style="display: block; white-space: nowrap; height :0.1%;">
			        <input type="hidden" name="m" value="dPprescription" />
			        <input type="hidden" name="del" value="0" />
			        <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}"/>
			        <input type="hidden" name="dosql" value="{{$dosql}}" />
			        <input type="hidden" name="duree" value="{{$line->duree}}" />
	   {{/if}}  
     
     <!-- Affichage de la date de debut si date courante -->
     {{if $date == $line->debut}}
       {{mb_field object=$line field="debut" canNull=false form="modifDates-$line_class-$line_id-$unite_prise-$date"}}
       <button class="tick notext" type="button" 
               onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ 
         		        calculSoinSemaine('{{$now}}','{{$prescription->_id}}'); } });"></button>
       
         <script type="text/javascript">
					Main.add(function(){
					  Calendar.regField(getForm("modifDates-{{$line_class}}-{{$line_id}}-{{$unite_prise}}-{{$date}}").debut, dates);
					} );
		     </script>
     {{/if}}
     
     {{assign var=line_fin value=$line->_fin}}
     <!-- Affichage de la date de fin si date courante -->
     {{if ($date == $line->_fin) && ($line->debut != $line->_fin)}}
       {{mb_field object=$line field="_fin" canNull=false form="modifDates-$line_class-$line_id-$unite_prise-$date"}}
         <button class="tick notext" type="button" onclick="calculDuree('{{$line_fin}}', this.form._fin.value, this.form, '{{$now}}', '{{$prescription->_id}}');"></button>
         <script type="text/javascript">
					Main.add( function(){
					  Calendar.regField(getForm("modifDates-{{$line_class}}-{{$line_id}}-{{$unite_prise}}-{{$date}}")._fin, dates);
					} );
		     </script>
     {{/if}}
     
    {{if $line->debut == $date || $line->_fin == $date}}
            </form>
          </td>
        </tr>
      </table>
    {{/if}}
    </td>
  {{/foreach}}
</tr>