<table class="tbl">
	<tr>
	  <th>{{mb_title class=CCompteRendu field=nom}}</th>
	  <th>{{mb_title class=CCompteRendu field=object_class}}</th>
	  <th>{{mb_title class=CCompteRendu field=type}}</th>
	  <th>{{tr}}Action{{/tr}}</th>
	</tr>

	{{foreach from=$modeles item=_modele}}
	<tr>
	  <td>
	    <a href="?m={{$m}}&amp;tab=addedit_modeles&amp;compte_rendu_id={{$_modele->compte_rendu_id}}">
	   	{{mb_value object=$_modele field=nom}}
	    </a>
	  </td>
	
	  <td>{{mb_value object=$_modele field=object_class}}</td>
	
	  <td>{{mb_value object=$_modele field=type}}</td>
	  
	  <td>
	    <form name="editFrm" action="?m={{$m}}" method="post">
	    <input type="hidden" name="m" value="{{$m}}" />
	    <input type="hidden" name="del" value="1" />
	    <input type="hidden" name="dosql" value="do_modele_aed" />
	    {{mb_field object=$_modele field="compte_rendu_id" hidden=1 prop=""}}
	    <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le modèle',objName:'{{$_modele->nom|smarty:nodefaults|JSAttribute}}'})">
	      {{tr}}Delete{{/tr}}
	    </button>
	    </form>
	  </td>
	</tr>

	{{foreachelse}}
	<tr>
	  <td colspan="10">
	    <em>{{tr}}CCompteRendu.none{{/tr}}</em>
	  </td>
	</tr>
  {{/foreach}}
</table>


