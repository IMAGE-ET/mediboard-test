<div id="disabledEtape1" class="chargementMask" style="position:absolute;display:none;"></div>
<table class="main">
  <tr>
    <td colspan="2" class="button">
      <label for="_create_group_1">Choisissez votre action</label>
    </td>
  </tr>
  
  <tr>
    <td class="halfPane button">
      <input type="radio" name="_create_group" value="1" checked="checked" />
      <label for="_create_group_1">Créer un nouvel établissement</label>
    </td>
    <td class="halfPane button">
      {{if $etablissements|@count}}
      <input type="radio" name="_create_group" value="0" />
      <label for="_create_group_0">Utiliser un établissement existant</label>
      {{else}}
      Aucun établissement disponible
      {{/if}}
    </td>
  </tr>
  
  <tr>
    <td class="button">
      <label for="etablissement" title="Veuillez saisir un titre pour l'etablissement">Titre de l'etablissement</label>
      <input type="text" name="etablissement" value="" class="str" />
    </td>
    <td class="button">
      {{if $etablissements|@count}}
      <select name="groups_selected">
      {{foreach from=$etablissements item=curr_group}}
      <option value="{{$curr_group->group_id}}">
        {{$curr_group->text}}
      </option>
      {{/foreach}}
      </select>
      {{/if}}
    </td>
  </tr>
  
  <tr>
    <th><label for="debut">Date de début</label></th>
    <td class="date">
      <input type="hidden" name="debut" value="{{$today}}" />
    </td>
  </tr>
  
  <tr>
    <th><label for="duree" title="Durée de l'echantillonnage">Durée</label></th>
    <td>
      <select name="duree">
       <option value="1">1 semaine</option>
       <option value="4">4 semaines</option>
       <option value="12">12 semaines</option>
       <option value="24">24 semaines</option>
      </select>
    </td>
  </tr>
  
  <tr>
    <th colspan="2" id="vwButtonEtap2">
      <a class="button" href="#" onclick="goto_etape2()">
        Etape Suivante <img align="top" src="images/icons/next.png" alt="Etape Suivante" />
      </a>
    </th>
  </tr>
</table>