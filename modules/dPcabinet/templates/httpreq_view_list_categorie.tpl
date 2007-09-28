<script type="text/javascript">

var listCat = {{$listCat|@json}};

var reloadIcone = function(icone){
  $('iconeBackground').src = "./modules/dPcabinet/categories/"+listCat[icone];
}

</script>




<tr>
  <th>Catégorie</th>
    <td>
	  <select name="categorie_id" onchange="reloadIcone(this.value);">
	    <option value="">&mdash; Choix d'une categorie</option>
		{{foreach from=$categories item="categorie"}}
		<option class="categorieConsult" {{if $categorie_id == $categorie->_id}} selected="selected" {{/if}}style="background-image:url(./modules/dPcabinet/categories/{{$categorie->nom_icone}});background-repeat:no-repeat;" value="{{$categorie->_id}}">{{$categorie->_view}}</option>
		{{/foreach}}
	  </select>
    <img id="iconeBackground" />
   </td>
</tr>

