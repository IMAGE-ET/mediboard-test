{{if $line->_class_name == "CPrescriptionLineComment"}}
  <tr>
    <td colspan="8">
    <div style="float: right">
      {{$line->_ref_prescription->_view}}
    </div>
    {{$line->_view}}</td>
  </tr>
{{else}}
  <tr>
    <th colspan="8">
    <div style="float: left">
	      {{mb_label object=$line field="conditionnel"}}:
	      {{if $line->conditionnel}} Oui
	      {{else}} Non
	      {{/if}}
    </div>
    <div style="float: right">
    {{if ($line->_class_name == "CPrescriptionLineMedicament" && !$line->substitute_for) || ($line->_class_name != "CPrescriptionLineMedicament")}}
	    <form name="modifProtocole" method="get" action="?">
	      <input type="hidden" name="m" value="dPprescription" />
	      <input type="hidden" name="tab" value="vw_edit_protocole" />
	      <input type="hidden" name="prescription_id" value="{{$line->_ref_prescription->_id}}" />
	      <button class="edit">{{$line->_ref_prescription->_view}}</button>
	    </form>
	    {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	      <button type="button" class="search" onclick="Prescription.viewSubstitutionLines('{{$line->_id}}','1')">
	         Substitution
	         ({{$line->_count_substitution_lines}})
	      </button>
	    {{/if}}
    {{/if}}
    </div>
    {{$line->_view}}
    </th>
  </tr>
  <tr>
  <td colspan="8">
    <!-- Duree de la ligne -->
    {{if $line->duree}}
     Durée de {{mb_value object=$line field=duree}} jour(s) 
    {{/if}}
    
    <!-- Date de debut de la ligne -->
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
			 Jusqu'à {{mb_value showPlus=1 object=$line field=jour_decalage_fin}}
			 {{if $line->decalage_line_fin >= 0}}+{{/if}} {{mb_value object=$line field=decalage_line_fin increment=1 }}
			 {{mb_value showPlus=1 object=$line field=unite_decalage_fin }}
			 <!-- Heure de fin -->
			 {{if $line->time_fin}} 
				à {{mb_value showPlus=1 object=$line field=time_fin}}		
			 {{/if}}	
		 {{/if}}
		 
		 {{if !$line->duree && !($line->jour_decalage && $line->unite_decalage) && !($line->jour_decalage_fin && $line->unite_decalage_fin)}}
		 Aucune date
		 {{/if}}
      {{if $line->commentaire}}
        , {{mb_value object=$line field="commentaire"}}
      {{/if}}
	</td>
  </tr>
  <tr>
    <td colspan="8">
    Posologie:<br />
			<ul>
			{{foreach from=$line->_ref_prises item=_prise}}
			  <li>{{$_prise->_view}}</li>
			{{/foreach}}
			</ul>
    </td>
  </tr>
{{/if}}
