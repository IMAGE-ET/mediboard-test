<!-- $Id$ -->

<tr>
  <th>{{mb_label class=CConsultation field="categorie_id"}}</th>
  <td>
    <script type="text/javascript">
     listCat = {{$listCat|@json}};
     
     reloadIcone = function(cat_id, updateFields){
       var img = $('iconeBackground');
       var form = getForm('editFrm');
       if (!img) return;
       
       if (!listCat[cat_id]) {
         img.hide();
       }
       else {
         img.show().src = "./modules/dPcabinet/images/categories/"+listCat[cat_id]['nom_icone'];
         if(updateFields) {
           $V(form.duree, listCat[cat_id]['duree']);
           $V(form.rques, ($V(form.rques) ? $V(form.rques) + '\n' : '' ) + listCat[cat_id]['commentaire']);
         }
       }
       $V(form.duree, listCat);
     }

     Main.add(function() {
       reloadIcone('{{$categorie_id}}', false);
     });
    </script>
    
    {{if !empty($categories|smarty:nodefaults)}}
    <select name="categorie_id" style="width: 15em;" onchange="reloadIcone(this.value, true);">
      <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
      {{foreach from=$categories item="categorie"}}
        <option class="categorieConsult" {{if $categorie_id == $categorie->_id}} selected="selected"{{/if}}
          style="background-image:url(./modules/dPcabinet/images/categories/{{$categorie->nom_icone|basename}});
          background-repeat:no-repeat;" value="{{$categorie->_id}}">{{$categorie->_view}}
        </option>
      {{/foreach}}
    </select>
    <img id="iconeBackground" />
    {{else}}
    <div class="empty">
      {{tr}}CConsultation-categorie_id.none{{/tr}}
    </div>
    {{/if}}
  </td>
</tr>
