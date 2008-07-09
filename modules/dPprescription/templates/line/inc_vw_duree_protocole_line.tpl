{{assign var=line_id value=$line->_id}}

{{if $typeDate != "mode_grille"}}
  {{assign var=onchange value="submitFormAjax(this.form, 'systemMsg');"}}
{{else}}
  {{assign var=onchange value=""}}
{{/if}}

<tr>
  <td></td>
  <td colspan="5">
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
			  {{mb_field object=$line field=jour_decalage onchange="$onchange" defaultOption="&mdash Choix"}}
			{{else}}
			J
			{{/if}}
			{{mb_field showPlus=1 object=$line field=decalage_line increment=1 form=editDuree-$typeDate-$line_id onchange="$onchange" size="3"}} (Jours) à 
			{{mb_field object=$line field=time_debut form=editDuree-$typeDate-$line_id onchange="$onchange"}}
			{{if ($typeDate != "anapath" && $typeDate != "imagerie" && $typeDate != "consult") && $prescription->object_class == "CSejour"}}
				{{if $typeDate == "mode_grille"}}<br />{{/if}}
				Jusqu'à 
				{{mb_field object=$line field=jour_decalage_fin onchange="this.form.duree.value = ''; $onchange" defaultOption="&mdash Choix"}}
				{{mb_field showPlus=1 object=$line field=decalage_line_fin increment=1 form=editDuree-$typeDate-$line_id onchange="this.form.duree.value = '';  $onchange" size="3"}} (Jours) à 
				{{mb_field object=$line field=time_fin form=editDuree-$typeDate-$line_id onchange="this.form.duree.value = '';  $onchange"}}			
		{{/if}}
    </form>
    <script type="text/javascript">
      Main.add( function(){
       prepareForm(document.forms['editDuree-{{$typeDate}}-{{$line->_id}}']); 
      } );
    </script>
  </td>
</tr>