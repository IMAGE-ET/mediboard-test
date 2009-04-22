<table class="main">
  <tr>
    <!-- Rubrique --> 
    <td class="halfPane" rowspan="3">
      <a class="button new" href="?m=dPgestionCab&amp;tab=configure&amp;facture_id=0">
        Créer une nouvelle rubrique
      </a>
      <table class="tbl">
        <tr>
          <th class="title" colspan="2">Rubrique</th>
        </tr>
         <tr>
          <th class="title">{{$etablissement}}</th>
        </tr>
        <tr>
          <th>Libellé</th>
        </tr>
        {{foreach from=$listRubriqueGroup item=_item}}
        <tr {{if $_item->_id == $rubrique->_id}}class="selected"{{/if}}>
          <td>
           <a href="?m=dPgestionCab&amp;tab=edit_rubrique&amp;rubrique_id={{$_item->_id}}" title="Modifier la rubrique">
              {{mb_value object=$_item field="nom"}}
            </a>
          </td>
        </tr>
        {{/foreach}}
        {{foreach from=$listRubriqueFonction key=keyRubrique item=_itemRubrique}}
        {{if $_itemRubrique|@count}}
        <tr>
          <th class="title">{{$keyRubrique}}</th>
        </tr>
         <tr>
          <th>Libellé</th>
        </tr>
        	{{foreach from=$_itemRubrique item=_item}}
	        <tr {{if $_item->_id == $rubrique->_id}}class="selected"{{/if}}>
	          <td>
	           <a href="?m=dPgestionCab&amp;tab=edit_rubrique&amp;rubrique_id={{$_item->_id}}" title="Modifier la rubrique">
	              {{mb_value object=$_item field="nom"}}
	            </a>
	           </td>
	        </tr>
        	{{/foreach}}
        {{/if}}
        {{/foreach}}
      </table>
  	</td>
  	
  	<!-- Opération sur les rubriques --> 
  	<td class="halfPane">
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
	  <input type="hidden" name="dosql" value="do_rubrique_aed" />
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="rubrique_id" value="{{$rubrique->_id}}" />
	  
      <table class="form">
        <tr>
          {{if $rubrique->_id}}
          <th class="title modify" colspan="2">
     	 	Modification de la {{$rubrique->_view}}
          </th>
          {{else}}
          <th class="title" colspan="2">
      		Création d'une nouvelle rubrique
          </th>
          {{/if}}
        </tr>
        <tr>
          	<th>{{mb_label object=$rubrique field="nom"}}</th>
            <td>{{mb_field object=$rubrique field="nom"}} </td>
        </tr>
        <tr>	
          	<th>{{mb_label object=$rubrique field="function_id"}}</th>
            <td>
            	<select name="function_id" class="{{$rubrique->_props.function_id}}" >
			        <option value="">&mdash; Associer à une fonction &mdash;</option>
			        {{foreach from=$listFunc item=curr_func}}
			          <option class="mediuser" style="border-color: #{{$curr_func->color}};" value="{{$curr_func->function_id}}" {{if $curr_func->function_id == $rubrique->function_id}} selected="selected" {{/if}}>
			            {{$curr_func->_view}}
			          </option>
			        {{/foreach}}
				</select>
            </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $rubrique->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la rubrique',objName:'{{$rubrique->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
 </table>