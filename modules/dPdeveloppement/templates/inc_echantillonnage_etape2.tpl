<table class="main">
  <tr>
    <th>
      <label for="_nb_cab" title="Nombre de cabinets spécialistes à créer">Nombre de cabinets spécialistes à créer</label>
    </th>
    <td class="halfPane">
      {{html_options name="_nb_cab" options=$list_5}}
    </td>  
    <td colspan="4" class="greedyPane"></td>
  </tr>

  <tr>
    <th>
      <label for="_nb_anesth" title="Nombre de cabinets d'anesthésistes à créer">Nombre de cabinets d'anesthésistes à créer</label>
    </th>
    <td class="halfPane">
      {{html_options name="_nb_anesth" options=$list_5}}
    </td>
    <th>
      <label for="_nb_salles" title="Nombre de salles opératoires à créer">Nombre de salles</label>
    </th>
    <td>
      {{html_options name="_nb_salles" options=$list_5}}
    </td>
    <th>
      <label for="_nb_services" title="Nombre de services à créer">Nombre de services</label>
    </th>
    <td>
      {{html_options name="_nb_services" options=$list_5}}
    </td>
  </tr>

  {{if $group_id}}
  <tr>
    <th>
      <label for="fct_selected[]" title="Cabinets disponibles à l'utilisation">Cabinets existants disponibles</label>
    </th>
    <td>
      {{if $listCab|@count}}
        <select name="fct_selected[]" multiple="multiple" size="15">
        {{foreach from=$listCab item=curr_fct}}
        <option value="{{$curr_fct->function_id}}">
          {{$curr_fct->text}}
        </option>
        {{/foreach}}
        </select>      
      {{else}}
        Aucun cabinet disponible
      {{/if}}
    </td>
    
    <th>
      <label for="salles_selected[]" title="Salles disponibles à l'utilisation">Salles existantes disponibles</label>
    </th>
    <td>
      {{if $salles|@count}}
        <select name="salles_selected[]" multiple="multiple" size="15">
        {{foreach from=$salles item=curr_salle}}
        <option value="{{$curr_salle->salle_id}}">
          {{$curr_salle->nom}}
        </option>
        {{/foreach}}
        </select>
      {{else}}
        Aucune salle disponible
      {{/if}}
    </td>
    
    <th>
      <label for="services_selected[]" title="Services disponibles à l'utilisation">Services existants disponibles</label>
    </th>
    <td>
      {{if $services|@count}}
        <select name="services_selected[]" multiple="multiple" size="15">
        {{foreach from=$services item=curr_serv}}
        <option value="{{$curr_serv->service_id}}">
          {{$curr_serv->nom}}
        </option>
        {{/foreach}}
        </select>
      {{else}}
        Aucun service disponible
      {{/if}}
    </td>
  </tr>
  {{/if}}

  <tr>
    <th colspan="6" id="vwButtonEtap3">
      <a class="button" href="#" onclick="goto_etape3()">
        Etape Suivante <img align="top" src="images/icons/next.png" alt="Etape Suivante" />
      </a>
    </th>
  </tr>
</table>