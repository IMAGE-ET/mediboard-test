<script type="text/javascript">

Main.add( function(){
  prepareForm('editDuree-{{$typeDate}}-{{$line->_id}}'); 
});

</script>
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
  </td>
</tr>