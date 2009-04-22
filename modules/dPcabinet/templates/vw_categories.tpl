{{mb_include_script module="dPcabinet" script="icone_selector"}}

<table class="main">
  <tr>
    <td>
      <form name="choixCabinet" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <select name="selCabinet" onchange="submit()">
        <option value="">&mdash; Choix du cabinet</option>
        {{foreach from=$listFunctions item="cabinet"}}
        <option class="mediuser" style="border-color: #{{$cabinet->color}}" value="{{$cabinet->_id}}" {{if $selCabinet == $cabinet->_id}}selected=selected{{/if}}>{{$cabinet->_view}}</option>
        {{/foreach}}
      </select>
      </form>
    </td>
  </tr>
  
  {{if $selCabinet && $droit}}
  <tr>
    <td class="halfPane">
	  <a href="?m={{$m}}&amp;tab={{$tab}}&amp;categorie_id=0" class="button new">
		Créer une catégorie
	  </a>
	  <table class="tbl">
	    <tr>
	      <th colspan="3">Liste des catégories du cabinet</th>
		</tr>
		<tr>
		  <th>Catégorie</th>
		  <th>Icone</th>
		</tr>
		{{foreach from=$categories item=_categorie}}
		<tr {{if $_categorie->_id == $categorie->_id}}class="selected"{{/if}}>
		  <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;categorie_id={{$_categorie->_id}}">{{$_categorie->nom_categorie}}</a></td>
		  <td><img src="./modules/dPcabinet/images/categories/{{$_categorie->nom_icone}}" /></td>
		</tr>
		{{/foreach}}
	  </table>
	</td> 
	
	<td class="halfPane">
	  <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
	    <input type="hidden" name="dosql" value="do_categorie_aed" />
		<input type="hidden" name="categorie_id" value="{{$categorie->_id}}" />
		<input type="hidden" name="function_id" value="{{$selCabinet}}" />
		<input type="hidden" name="del" value="0" />
		<table class="form">
		  <tr>
		  {{if $categorie->_id}}
		    <th class="title modify" colspan="2">
		      <div class="idsante400" id="{{$categorie->_class_name}}-{{$categorie->_id}}"></div>
		      <a style="float:right;" href="#nothing" onclick="view_log('{{$categorie->_class_name}}',{{$categorie->_id}})">
		      <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
		      </a>
		      Modification de la catégorie &lsquo;{{$categorie->nom_categorie}}&rsquo;
		    </th>
		    {{else}}
		    <th class="title" colspan="2">
		      Création d'une catégorie
		    </th>
		    {{/if}}
		  </tr>
		  <tr>
		    <th>{{mb_label object=$categorie field="nom_categorie"}}</th>
		    <td>{{mb_field object=$categorie field="nom_categorie"}}</td>
		  </tr>       
		  <tr>
		    <th>{{mb_label object=$categorie field="nom_icone"}}</th>
		    <td>
		    {{if $categorie->_id}}
		     <img id="iconeBackground" onclick="IconeSelector.init()" src="./modules/dPcabinet/images/categories/{{$categorie->nom_icone}}" />
		    {{else}}
		      <img id="iconeBackground" src="images/icons/search.png" onclick="IconeSelector.init()" />
		     {{/if}}
		     <input type="hidden" name="nom_icone" value="{{$categorie->nom_icone}}"  class="notNull" />
         <script type="text/javascript">
            IconeSelector.init = function(){
              this.sForm = "editFrm";
              this.sView = "nom_icone";
              this.pop();
            }
         </script>
       </td>
		  </tr>
		  <tr>
		    <td class="button" colspan="2">
		      {{if $categorie->_id}}
		      <button class="modify" type="submit">Valider</button>
		      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la catégorie ',objName:'{{$categorie->nom_categorie|smarty:nodefaults|JSAttribute}}'})">
		        Supprimer
		      </button>
		      {{else}}
		      <button class="submit" name="btnFuseAction" type="submit">Créer</button>
		      {{/if}}
		    </td>
		  </tr>
		</table>   
      </form>
    </td>  
  </tr>
  {{/if}}
</table>