<!-- $Id$ -->

<tr>
  <th>Catégorie</th>
  <td>
	  <script type="text/javascript">
	   listCat = {{$listCat|@json}};
	   reloadIcone = function(icone){
	     $('iconeBackground').src = "./modules/dPcabinet/images/categories/"+listCat[icone];
	   }
	  </script>
	   
	  <select name="categorie_id" onchange="reloadIcone(this.value);">
	    <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
			{{foreach from=$categories item="categorie"}}
			  <option class="categorieConsult" {{if $categorie_id == $categorie->_id}} selected="selected" {{/if}}style="background-image:url(./modules/dPcabinet/images/categories/{{$categorie->nom_icone}});background-repeat:no-repeat;" value="{{$categorie->_id}}">{{$categorie->_view}}</option>
			{{/foreach}}
	  </select>
	  <img id="iconeBackground" />
  </td>
</tr>
