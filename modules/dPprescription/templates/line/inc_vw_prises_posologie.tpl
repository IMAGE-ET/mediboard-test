{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

{{if $type == "Med"}}
{{if $line->_ref_prescription->object_id}}
{{if !$line->signee && !$line->fin}}
	var oForm = document.forms["editDates-Med-{{$line->_id}}"];
	$V(oForm.debut,'{{$line->debut}}');
	{{if $line->debut}}
	  dDate = Date.fromDATE(oForm.debut.value);
	  oForm.debut_da.value = dDate.toLocaleDate();
	{{/if}}
	$V(oForm.duree,'{{$line->duree}}');
	$V(oForm.unite_duree,'{{$line->unite_duree}}');
{{/if}}
{{/if}}


// On grise le formulaire de signature de la ligne si aucune prise n'est créée
var oButton = $('signature_{{$line->_id}}'); 
if(oButton){
{{if $line->_count_prises_line}}
  oButton.disabled = false;
  oButton.setOpacity(1.0);
{{else}}
  oButton.disabled = true;
  oButton.setOpacity(0.3);
{{/if}}
}
{{/if}}

Main.add(function () {
	{{foreach from=$line->_ref_prises item=prise}}
	  prepareForm('addPrise-{{$prise->_id}}');
	{{/foreach}}
	{{if $line->_protocole}}
	 modifFormDate('{{$line->_nb_prises_interv}}','editDuree-{{$typeDate}}-{{$line->_id}}','1','{{$line->_id}}');
	{{else}}
	 modifFormDate('{{$line->_nb_prises_interv}}','editDates-{{$typeDate}}-{{$line->_id}}','0','{{$line->_id}}');
	{{/if}}
});

</script>


{{assign var=line_id value=$line->_id}}

{{foreach from=$line->_ref_prises item=prise}}
  {{assign var=prise_id value=$prise->_id}}
  
  <form name="addPrise-{{$prise->_id}}" action="?" method="post" style="display: block; clear: both;">
    <button style="float: right" type="button" class="remove notext" onclick="this.form.del.value = 1; testPharma({{$line_id}}); onSubmitPrise(this.form ,'{{$typeDate}}'); ">Supprimer</button>
	  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="m" value="dPprescription" />
	  <input type="hidden" name="prise_posologie_id" value="{{$prise->_id}}" />
	  <input type="hidden" name="object_id" value="{{$line_id}}" />
	  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
    
	  <!-- Formulaire de selection de la quantite -->
	  {{mb_field object=$prise field=quantite size="3" increment=1 min=1 form=addPrise-$prise_id onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg');"}}	  
	  
	  {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	  {{$prise->unite_prise}}
	  {{/if}}
	  {{if $line->_class_name == "CPrescriptionLineElement"}}
		  {{$line->_unite_prise}}
		{{/if}}
		
		{{if $prise->urgence_datetime}}
		  le {{$prise->urgence_datetime|date_format:$dPconfig.datetime}} (Urgence)
		{{/if}}
		
	  <!-- Cas d'un moment unitaire_id -->
	  {{if $prise->moment_unitaire_id}}
		  {{$prise->_ref_moment->_view}}
	  {{/if}}
	  
	  <!-- Cas des fois par -->
	  {{if $prise->nb_fois && $prise->unite_fois}}
      {{mb_value object=$prise field=nb_fois size=3 increment=1 min=1 form=addPrise-$prise_id onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}} fois par 
			{{mb_value object=$prise field=unite_fois onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}}
		{{/if}}
  
    <!-- Cas des tous les -->
    {{if $prise->unite_tous_les && (!$prise->unite_fois || ($prise->unite_fois == "jour" && $prise->nb_tous_les))}}
      tous les
			{{mb_value object=$prise field=nb_tous_les size=3 increment=1 min=1 form=addPrise-$prise_id onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}}				   
			{{mb_value object=$prise field=unite_tous_les onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}}
		  (J+{{mb_value object=$prise field=decalage_prise size=1 increment=1 min=0 form=addPrise-$prise_id onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}})
		{{/if}}
		
		<!-- Cas du decalage par rapport à l'intervention -->
		{{if $prise->decalage_intervention != NULL}}
		  {{if $prise->decalage_intervention >= 0}}
		    {{assign var=signe_decalage_intervention value="+"}}
		  {{else}}
		    {{assign var=signe_decalage_intervention value=""}}
		  {{/if}}
		  à {{if $line->_protocole}}
		  	I {{$signe_decalage_intervention}}{{mb_value object=$prise showPlus="true" field=decalage_intervention}} heures 
		 	{{else}}
		 	  {{mb_value object=$prise field="heure_prise"}}
		 	{{/if}}
		{{/if}}
		
  </form>
{{/foreach}}