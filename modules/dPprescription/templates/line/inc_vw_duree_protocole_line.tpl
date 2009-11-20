{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=line_id value=$line->_id}}

{{if $typeDate != "mode_grille"}}
  {{assign var=onchange value="submitFormAjax(this.form, 'systemMsg');"}}
{{else}}
  {{assign var=onchange value=""}}
{{/if}}

<table class="form">
	<tr>
	  <td style="border: none;">
	    {{if $line->_perm_edit || $typeDate == "mode_grille"}}
	    <form name="editDuree-{{$typeDate}}-{{$line->_id}}" action="?" method="post">
	      <input type="hidden" name="m" value="dPprescription" />
	      <input type="hidden" name="dosql" value="{{$dosql}}" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
	      
	      <!-- Durée -->
	      {{if $typeDate != "anapath" && $typeDate != "imagerie" && $typeDate != "consult"}}
		      Durée de 
		      {{mb_field object=$line field=duree increment=1 min=1 form=editDuree-$typeDate-$line_id size="3" 
		      					onchange="if(this.form.jour_decalage){ this.form.jour_decalage_fin.value = ''; this.form.decalage_line_fin.value = ''; this.form.time_fin.value = '';} $onchange"}} jour(s)
					à partir de 
				{{/if}}
				{{if $prescription->object_class == "CSejour"}}
				  {{mb_field object=$line field=jour_decalage onchange="modifUniteDecal(this, this.form.unite_decalage); $onchange" defaultOption="&mdash Choix"}}
				{{else}}
				J
				{{/if}}
				{{mb_field showPlus=1 object=$line field=decalage_line increment=1 form=editDuree-$typeDate-$line_id onchange="$onchange" size="3"}}
				 {{if $prescription->object_class == "CSejour"}}
				 {{mb_field showPlus=1 object=$line field=unite_decalage onchange="$onchange"}}
				 {{else}}
				 (jours)
				 {{/if}}
				  à 
				{{mb_field object=$line field=time_debut form=editDuree-$typeDate-$line_id onchange="$onchange"}}
				{{if ($typeDate != "anapath" && $typeDate != "imagerie" && $typeDate != "consult") && $prescription->object_class == "CSejour"}}
					{{if $typeDate == "mode_grille"}}<br />{{/if}}
					Jusqu'à 
					{{mb_field object=$line field=jour_decalage_fin onchange="modifUniteDecal(this, this.form.unite_decalage_fin); this.form.duree.value = ''; $onchange" defaultOption="&mdash Choix"}}
					{{mb_field showPlus=1 object=$line field=decalage_line_fin increment=1 form=editDuree-$typeDate-$line_id onchange="this.form.duree.value = '';  $onchange" size="3"}}
					 {{mb_field showPlus=1 object=$line field=unite_decalage_fin onchange="$onchange"}} à 
					{{mb_field object=$line field=time_fin form=editDuree-$typeDate-$line_id onchange="this.form.duree.value = '';  $onchange"}}			
			{{/if}}
	    </form>
	    {{else}}
	 	
			{{if $line->duree}}
	     Durée de {{mb_value object=$line field=duree}} jour(s) 
	    {{/if}}
	    {{if $line->jour_decalage && $line->unite_decalage}} 
		    A partir de
				{{if $prescription->object_class == "CSejour"}}
				 {{mb_value object=$line field=jour_decalage}}
				{{else}}
				 J
				{{/if}}
				{{if $line->decalage_line >= 0}}+{{/if}} {{mb_value object=$line field=decalage_line size="3"}}
				{{if $prescription->object_class == "CSejour"}}
				  {{mb_value object=$line field=unite_decalage}}
				{{else}}
				 (jours)
				{{/if}} 
				 <!-- Heure de debut -->
				 {{if $line->time_debut}}
					 à {{mb_value object=$line field=time_debut}}
				 {{/if}}
			 {{/if}}
			 
			 {{if $line->jour_decalage_fin && $line->unite_decalage_fin}}
				 <!-- Date de fin -->
				 Jusqu'à {{mb_value object=$line field=jour_decalage_fin}}
				 {{if $line->decalage_line_fin >= 0}}+{{/if}} {{mb_value object=$line field=decalage_line_fin increment=1 }}
				 {{mb_value object=$line field=unite_decalage_fin }}
				 <!-- Heure de fin -->
				 {{if $line->time_fin}} 
					à {{mb_value object=$line field=time_fin}}		
				 {{/if}}	
			 {{/if}}
	    {{/if}}
	  </td>
	</tr>
</table>