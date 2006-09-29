        <tr>
          {{if !$fiche->valid_user_id}}
          <th colspan="2" class="title" style="color:#f00;">
            Validation de la fiche {{$fiche->_view}}
          {{else}}
          <th colspan="2" class="title">
            Visualisation de la fiche {{$fiche->_view}}
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>Evenement</th>
          <td>
            <strong>
              Signalement d'un
              {{tr}}{{$fiche->type_incident}}{{/tr}}
            </strong>
            <br /> le {{$fiche->date_incident|date_format:"%A %d %B %Y à %Hh%M"}}
          </td>
        </tr>
        <tr>
          <th>Auteur de la Fiche</th>
          <td class="text">
            {{$fiche->_ref_user->_view}}
            <br />{{$fiche->_ref_user->_ref_function->_view}}
          </td>
        </tr>
       <tr>
          <th>Concernant</th>
          <td class="text">
            {{tr}}{{$fiche->elem_concerne}}{{/tr}}<br />
            {{$fiche->elem_concerne_detail|nl2br}}
          </td>
        </tr>
        <tr>
          <th>Lieu</th>
          <td class="text">
            {{$fiche->lieu}}
          </td>
        </tr>
        <tr>
          <th colspan="2" class="category">Description de l'événement</th>
        </tr>
        {{foreach from=$catFiche item=currEven key=keyEven}}
        <tr>
          <th><strong>{{$keyEven}}</strong></th>
          <td>
            <ul>
              {{foreach from=$currEven item=currItem}}
              <li>{{$currItem->nom}}</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>  
        {{/foreach}}
        <tr>
          <th colspan="2" class="category">Informations complémentaires</th>
        </tr>
        {{if $fiche->autre}}
        <tr>
          <th>Autre</th>
          <td class="text">{{$fiche->autre|nl2br}}</td>
        </tr>
        {{/if}}
        <tr>
          <th>Description des faits</th>
          <td class="text">{{$fiche->descr_faits|nl2br}}</td>
        </tr>
        <tr>
          <th>Mesures Prises</th>
          <td class="text">{{$fiche->mesures|nl2br}}</td>
        </tr>
        <tr>
          <th>Description des conséquences</th>
          <td class="text">{{$fiche->descr_consequences|nl2br}}</td>
        </tr>
        <tr>
          <th>Gravité estimée</th>
          <td>
            {{tr}}{{$fiche->gravite}}{{/tr}}
          </td>
        </tr>
        <tr>
          <th>Plainte prévisible</th>
          <td>
            {{tr}}{{$fiche->plainte}}{{/tr}}
          </td>
        </tr>
        <tr>
          <th>Commission concialition</th>
          <td>
            {{tr}}{{$fiche->commission}}{{/tr}}
          </td>
        </tr>
        <tr>
          <th>Suite de l'évènement</th>
          <td>
            {{tr}}{{$fiche->suite_even}}{{/tr}}
          </td>
        </tr>
        <tr>
          <th>Evénement déjà survenu à<br /> la connaissance de l'auteur</th>
          <td>
            {{if $fiche->deja_survenu!==null}}
              {{tr}}{{$fiche->deja_survenu}}{{/tr}}
            {{else}}
              Ne sais pas
            {{/if}}
          </td>
        </tr>
        <tr>
          <th colspan="2" class="category">Validation de la Fiche</th>
        </tr>
        {{if $fiche->date_validation}}
        <tr>
          <th>Degré d'Urgence</th>
          <td>{{$fiche->degre_urgence}}</td>
        </tr>
        <tr>
          <th>Validée par</th>
          <td>
            {{$fiche->_ref_user_valid->_view}}
            <br />le {{$fiche->date_validation|date_format:"%d %b %Y à %Hh%M"}}
          </td>
        </tr>
        <tr>
          <th>Transmise à</th>
          <td>
            {{$fiche->_ref_service_valid->_view}}
          </td>
        </tr>
        {{/if}}
        
        {{if $fiche->service_date_validation}}
        <tr>
          <th colspan="2" class="category">Validation du Chef de Service</th>
        </tr>
        <tr>
          <th>Mesures Prises par</th>
          <td>
            {{$fiche->_ref_service_valid->_view}}
            <br />le {{$fiche->service_date_validation|date_format:"%d %b %Y à %Hh%M"}}
          </td>
        </tr>
        <tr>
          <th>Actions mises en place</th>
          <td class="text">{{$fiche->service_actions|nl2br}}</td>
        </tr>
        <tr>
          <th>Description des conséquences</th>
          <td class="text">{{$fiche->service_descr_consequences|nl2br}}</td>
        </tr>
        {{/if}}
        
        {{if $fiche->qualite_date_validation}}
        <tr>
          <th colspan="2" class="category">Validation du Service Qualité</th>
        </tr>
        <tr>
          <th>Validée par</th>
          <td>
            {{$fiche->_ref_qualite_valid->_view}}
            <br />le {{$fiche->qualite_date_validation|date_format:"%d %b %Y à %Hh%M"}}
          </td>
        </tr>
        {{if $fiche->qualite_date_verification}}
        <tr>
          <th>Vérifié le</th>
          <td>{{$fiche->qualite_date_verification|date_format:"%d %b %Y"}}</td>
        </tr>
        {{/if}}
        {{if $fiche->qualite_date_controle}}
        <tr>
          <th>Contrôlé le</th>
          <td>{{$fiche->qualite_date_controle|date_format:"%d %b %Y"}}</td>
        </tr>
        {{/if}}
        {{/if}}