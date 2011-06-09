<!-- $Id$ -->

<tr>
  <th>{{mb_label class=CConsultation field="categorie_id"}}</th>
  <td>
    <script type="text/javascript">
     listCat = {{$listCat|@json}};
     reloadIcone = function(icone){
       var img = $('iconeBackground');
       if (!img) return;
       
       if (!listCat[icone]) {
         img.hide();
       }
       else {
         img.show().src = "./modules/dPcabinet/images/categories/"+listCat[icone];
       }
     }

     Main.add(function() {
       reloadIcone('{{$categorie_id}}');
     });
    </script>
    
    {{if !empty($categories|smarty:nodefaults)}}
    <select name="categorie_id" onchange="reloadIcone(this.value);">
      <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
      {{foreach from=$categories item="categorie"}}
        <option class="categorieConsult" {{if $categorie_id == $categorie->_id}} selected="selected"{{/if}}
          style="background-image:url(./modules/dPcabinet/images/categories/{{$categorie->nom_icone}});
          background-repeat:no-repeat;" value="{{$categorie->_id}}">{{$categorie->_view}}
        </option>
      {{/foreach}}
    </select>
    <img id="iconeBackground" />
    {{else}}
      {{tr}}CConsultation-categorie_id.none{{/tr}}
    {{/if}}
  </td>
</tr>
