<script type="text/javascript">

function viewItems(iCategorie){
  var oForm = document.FrmEI;
  $('Items' + oForm._elemOpen.value).style.display = "none";
  $('Items' + iCategorie).style.display = "";
  oForm._elemOpen.value = iCategorie;
}

function checkCode(oElement){
  if(oElement.checked == true){
    putCode(oElement.name);
  }else{
    delCode(oElement.name);
  }
}
function delCode(iCode){
  var oForm = document.FrmEI;
  var aEvenements = oForm.evenements.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aEvenements.removeByValue("");
  aEvenements.removeByValue(iCode, true);
  oForm.evenements.value = aEvenements.join("|");
  
  var oElement = eval("oForm._ItemsSel_cat_" + oForm._elemOpen.value);
  var aEvenements = oElement.value.split("|");
  aEvenements.removeByValue("");
  aEvenements.removeByValue(iCode, true);
  oElement.value = aEvenements.join("|");
  
  refreshListChoix();
}

function putCode(iCode){
  var oForm = document.FrmEI;
  var aEvenements = oForm.evenements.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aEvenements.removeByValue("");
  aEvenements.push(iCode);
  oForm.evenements.value = aEvenements.join("|");
  
  var oElement = eval("oForm._ItemsSel_cat_" + oForm._elemOpen.value);
  var aEvenements = oElement.value.split("|");
  aEvenements.removeByValue("");
  aEvenements.push(iCode);
  oElement.value = aEvenements.join("|");
  
  refreshListChoix();
}

function refreshListChoix(){
  var oForm = document.FrmEI;
  var oCategorie = oForm._cat_evenement.options;
  var sListeChoix = "";
  for(i=0; i< oCategorie.length; i++){
    var oElement = eval("oForm._ItemsSel_cat_" + oCategorie[i].value);
    if(oElement.value){
      sListeChoix += "<strong>" + oCategorie[i].text + "</strong><ul>";
      var aItems = oElement.value.split("|");
      aItems.removeByValue("");
      iCode = 0;
      while (sCode = aItems[iCode++]) {
        sListeChoix += "<li>" + $('titleItem' + sCode).title + "</li>";
      }
      sListeChoix += "</ul>";
    }
  }
  $('listChoix').innerHTML = sListeChoix;
}

function pageMain() {
  regFieldCalendar("FrmEI", "_incident_date");
}
</script>
<table class="main">
  <tr>
    <td>
      <form name="FrmEI" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_ficheEi_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" /> 
      <input type="hidden" name="user_id" value="{{$user_id}}" />    
    
      <table class="form">
        <tr>
          <th colspan="4" class="category">Fiche d'Incident Prévention - Gestion des Riques</th>
        </tr>
        
        <tr>
          <th>
            <label for="type_incident" title="Veuillez Sélectionner un type de signalement">Type de Signalement</label>
          </th>
          <td>
            <select name="type_incident" title="{{$fiche->_props.type_incident}}">
            <option value="">&mdash;Veuillez Choisir &mdash;</option>
            <option value="0">Incident</option>
            <option value="1">Risque D'incident</option>
            </select>
          </td>
          <th><label for="_incident_date" title="Date de l'événement">Date de l'événement</label></th>
          <td class="date">
            <div id="FrmEI__incident_date_da">{{$datenow|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="_incident_date" title="date|notNull" value="{{$datenow}}" />
            <img id="FrmEI__incident_date_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date pour l'évènement"/>
         </td>
        </tr>
        <tr>
          <th>
            <label for="elem_concerne" title="Veuillez choisir à qui ou à quoi se réfère cette fiche">Cette Fiche concerne</label>
          </th>
          <td>
            <select name="elem_concerne" title="{{$fiche->_props.elem_concerne}}">
            <option value="">&mdash;Veuillez Choisir &mdash;</option>
            <option value="0">Un Patient</option>
            <option value="1">Un Visiteur</option>
            <option value="2">Un Membre du Personnel</option>
            <option value="3">Un Médicament</option>
            <option value="4">Un Matériel</option>
            </select>            
          </td>
          <th><label for="_incident_heure" title="Heure de l'événement">Heure de l'événement</label></th>
          <td>
            <select name="_incident_heure">
            {{foreach from=$heures item=curr_heure}}
              <option value="{{$curr_heure}}">{{$curr_heure}}</option>
            {{/foreach}}
            </select> h
            <select name="_incident_min">
            {{foreach from=$mins item=minute}}
              <option value="{{$minute}}">{{$minute}}</option>
            {{/foreach}}
            </select> min
          </td>
        </tr>
        <tr>
          <th>
            <label for="elem_concerne_detail" title="Détails concernant l'objet ou la personne concerné">
              Détails
            </label>
          </th>
          <td>
            <textarea name="elem_concerne_detail" title="{{$fiche->_props.elem_concerne_detail}}"></textarea>
          </td>
          <th><label for="lieu" title="Veuillez saisir le lieu de l'événement">Lieu</label></th>
          <td>
            <input type="text" name="lieu" value="" title="{{$fiche->_props.lieu}}" />
          </td>
        </tr>
        <tr>
          <th colspan="4" class="category"><label for="evenements" title="Veuillez choisir ce qui décrit le mieux l'événement">Description de l'événement</label></th>
        </tr>

        <tr style="height:1%;">
          <td colspan="2"rowspan="2" class="halfPane" id="listChoix"></td>
          <th>
            <label for="_cat_evenement" title="Veuillez Sélectionner un catégorie d'événement">Catégorie d'événement</label>
          </th>
          <td>
            <input type="hidden" name="evenements" value="" title="{{$fiche->_props.evenements}}" />
            <input type="hidden" name="_elemOpen" value="{{$firstdiv}}" />
            <select name="_cat_evenement" onchange="javascript:viewItems(this.value);">
            {{foreach from=$listCategories item=curr_evenement}}
            <option value="{{$curr_evenement->ei_categorie_id}}"{{if $curr_evenement->ei_categorie_id==$firstdiv}} selected="selected"{{/if}}>
              {{$curr_evenement->nom}}
            </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        {{foreach from=$listCategories item=curr_evenement}}        
        <tr id="Items{{$curr_evenement->ei_categorie_id}}" {{if $curr_evenement->ei_categorie_id!=$firstdiv}}style="display:none;"{{/if}}>
          <td colspan="2">
            <input type="hidden" name="_ItemsSel_cat_{{$curr_evenement->ei_categorie_id}}" value="" />
            <table class="tbl">
            {{counter start=0 skip=1 assign=curr_data}}
            {{foreach name=itemEvenement from=$curr_evenement->_ref_items item=curr_item}}
            {{if $curr_data is div by 3 || $curr_data==0}}
            <tr>
            {{/if}}
              <td class="text">
                <input type="checkbox" name="{{$curr_item->ei_item_id}}" onchange="javascript:checkCode(this);"/><label for="{{$curr_item->ei_item_id}}" id="titleItem{{$curr_item->ei_item_id}}" title="{{$curr_item->nom}}">{{$curr_item->nom}}</label>
              </td>
            {{if (($curr_data+1) is div by 3 || $smarty.foreach.itemEvenement.last)}}
            </tr>
            {{/if}}
            {{counter}}
            {{foreachelse}}
            <tr>
              <td>
                Pas d'Item dans cette catégorie
              </td>
            </tr>
            {{/foreach}}
            </table>
          </td>
        </tr>
        {{foreachelse}}
        <tr>
         <td colspan="2">
           Aucun Item disponible
         </td>
        </tr>
        {{/foreach}}
        <tr>
          <th colspan="4" class="category">Informations complémentaires</th>
        </tr>
        <tr>
          <th>
            <label for="autre" title="Veuillez saisir les événements non listés ci-dessous">Autre</label>
          </th>
          <td>
            <textarea name="autre" title="{{$fiche->_props.autre}}"></textarea>
          </td>
          <th><label for="gravite" title="Veuillez Sélectionner la gravitée estimée de l'événement">Gravitée Estimée</label></th>
          <td>
            <select name="gravite" title="{{$fiche->_props.gravite}}">
              <option value="">&mdash;Veuillez Choisir &mdash;</option>
              <option value="0">Nulle</option>
              <option value="1">Modérée</option>
              <option value="2">Importante</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>
            <label for="descr_faits" title="Veuillez décrire les faits">Description des faits</label>
          </th>
          <td>
            <textarea name="descr_faits" title="{{$fiche->_props.descr_faits}}"></textarea>
          </td>
          <th><label for="plainte" title="Une plainte est-elle prévisible pour cet événement">Plainte prévisible</label></th>
          <td>
            <select name="plainte" title="{{$fiche->_props.plainte}}">
              <option value="">&mdash;Veuillez Choisir &mdash;</option>
              <option value="0">Non</option>
              <option value="1">Oui</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>
            <label for="mesures" title="Veuillez décrire les mesures prises">Mesures Prises</label>
          </th>
          <td>
            <textarea name="mesures" title="{{$fiche->_props.mesures}}"></textarea>
          </td>
          <th><label for="commission" title="Y aura t'il une Commission de conciliation">Commission conciliation</label></th>
          <td>
            <select name="commission" title="{{$fiche->_props.commission}}">
              <option value="">&mdash;Veuillez Choisir &mdash;</option>
              <option value="0">Non</option>
              <option value="1">Oui</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>
            <label for="descr_consequences" title="Veuillez décrire les conséquences">Description des conséquences</label>
          </th>
          <td>
            <textarea name="descr_consequences" title="{{$fiche->_props.descr_consequences}}"></textarea>
          </td>
          <th><label for="deja_survenu" title="Avez-vous déjà eu connaissance d'un evenement similaire">Evénement déjà survenu à votre connaissance</label></th>
          <td>
            <select name="deja_survenu" title="{{$fiche->_props.deja_survenu}}">
              <option value="">Je ne sais pas</option>
              <option value="0">Non</option>
              <option value="1">Oui</option>
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="4" class="button">
            <button class="submit" type="submit">
              Envoyer la Fiche
            </button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>