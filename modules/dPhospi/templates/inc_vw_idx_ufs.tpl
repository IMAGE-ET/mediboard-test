<table class="main">
	
<tr>
  <td class="halfPane">
    <a href="?m={{$m}}&amp;tab={{$tab}}&amp;uf_id=0" class="button new">
      {{tr}}CUniteFonctionnelle-title-create{{/tr}}
    </a>
    
    <!-- Liste des services -->
    <table class="tbl">
	    <tr>
	      <th colspan="3" class="title">
	        {{tr}}CUniteFonctionnelle.all{{/tr}}
	      </th>
	    </tr>
	    <tr>
	      <th>{{mb_title class=CUniteFonctionnelle field=code}}</th>
	      <th>{{mb_title class=CUniteFonctionnelle field=libelle}}</th>
        <th>{{mb_title class=CUniteFonctionnelle field=description}}</th>
	    </tr>
	
			{{foreach from=$ufs item=_uf}}
	    <tr {{if $_uf->_id == $uf->_id}}class="selected"{{/if}}>
	      <td>
	      	<a href="?m={{$m}}&amp;tab={{$tab}}&amp;uf_id={{$_uf->_id}}">
	          {{mb_value object=$_uf field=code}}
					</a>
				</td>
	      <td class="text">{{mb_value object=$_uf field=libelle}}</td>
	      <td class="text">{{mb_value object=$_uf field=description}}</td>
	    </tr>
	    {{/foreach}}
    </table>
  </td> 

  <td class="halfPane">
  	<!-- Formulaire d'une unité foncitonnemme -->
    <form name="Edit-CUniteFonctionnelle" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_uf_aed" />
    <input type="hidden" name="del" value="0" />
		{{mb_key object=$uf}}

    <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$uf}}	
			
    <tr>
      <th>{{mb_label object=$uf field=group_id}}</th>
      <td>{{mb_field object=$uf field=group_id options=$etablissements}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$uf field=code}}</th>
      <td>{{mb_field object=$uf field=code}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$uf field=libelle}}</th>
      <td>{{mb_field object=$uf field=libelle}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$uf field=description}}</th>
      <td>{{mb_field object=$uf field=description}}</td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $uf->_id}}
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l'UF ',objName: $V(this.form.libelle)})">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>

    </table>   

    </form>
  </td>
</tr>

</table>
