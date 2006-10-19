    </td>
  </tr>
</table>
<table class="form" id="admission">
  <tr>
    <td>
      <div style="float:right;">
      {{$fiche->_view}}
      </div>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%" style="font-size: 110%;padding-bottom: 10px;">
        <tr>
          <th class="title">
            <a href="javascript:window.print()">
              Fiche d'Incident - Prévention - Gestion des Risques
            </a>
          </th>
        </tr>
       </table>
       <table width="100%" style="font-size: 110%;padding-bottom: 10px;">
        <tr>
          <td style="text-align:center;">
            <strong>Signalement d'un
            {{tr}}CFicheEi.type_incident.{{$fiche->type_incident}}{{/tr}}
            <br />survenu le {{$fiche->date_incident|date_format:"%A %d %B %Y à %Hh%M"}}
            </strong>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table class="form" id="admission">
  <tr>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;padding-bottom: 10px;">
        <tr>
          <th class="category" colspan="2">Auteur de la Fiche</th>
        </tr>
        <tr>
          <th>Identité</th>
          <td>{{$fiche->_ref_user->_view}}</td>
        </tr>
        <tr>
          <th>Fonction</th>
          <td>{{$fiche->_ref_user->_ref_function->_view}}</td>
        </tr>
      </table>
    </td>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;padding-bottom: 10px;">
        <tr>
          <th class="category" colspan="2">Fiche concernant</th>
        </tr>
        <tr>
          <th>
            {{tr}}CFicheEi.elem_concerne.{{$fiche->elem_concerne}}{{/tr}}
          </th>
          <td>{{$fiche->elem_concerne_detail|nl2br}}</td>
        </tr>
        <tr>
          <th>
            Service
          </th>
          <td>{{$fiche->lieu}}</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table width="100%" style="font-size: 100%;padding-bottom: 10px;">
        <tr>
          <th class="category" colspan="2">
            Description de l'événement
          </th>
        </tr>
        {{foreach from=$catFiche item=currEven key=keyEven}}
        <tr>
          <th>{{$keyEven}}</th>
          <td>
            <ul>
              {{foreach from=$currEven item=currItem}}
              <li>{{$currItem->nom}}</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>  
        {{/foreach}}
      </table>
      <table width="100%" style="font-size: 100%;padding-bottom: 10px;">
        <tr>
          <th class="category" colspan="2">
            Informations complémentaires
          </th>
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
            {{tr}}CFicheEi.gravite.{{$fiche->gravite}}{{/tr}}
          </td>
        </tr>
        <tr>
          <th>Plainte prévisible</th>
          <td class="greedyPane">
            {{tr}}CFicheEi.plainte.{{$fiche->plainte}}{{/tr}}
          </td>
        </tr>
        <tr>
          <th>Commission concialition</th>
          <td>
            {{tr}}CFicheEi.commission.{{$fiche->commission}}{{/tr}}
          </td>
        </tr>
        <tr>
          <th>Suite de l'évènement</th>
          <td>
            {{tr}}CFicheEi.suite_even.{{$fiche->suite_even}}{{/tr}}
            {{if $fiche->suite_even=="autre"}}
              <br />{{$fiche->suite_even_descr|nl2br}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Evénement déjà survenu à la connaissance de l'auteur</th>
          <td>
            {{tr}}CFicheEi.deja_survenu.{{$fiche->deja_survenu}}{{/tr}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<br style="page-break-after: always;" />
<table class="form" id="admission">      
  <tr>
    <td>
      <div style="float:right;">
      {{$fiche->_view}}
      </div>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%" style="font-size: 100%;padding-bottom:20px;">
        <tr>
          <th class="category" colspan="4">
            Enregistrement Qualité
          </th>
        </tr>
        <tr>
          <th>Degré d'Urgence</th>
          <td>{{tr}}CFicheEi.degre_urgence.{{$fiche->degre_urgence}}{{/tr}}</td>
          <th>Validée par</th>
          <td class="text">
            {{$fiche->_ref_user_valid->_view}}
            <br />le {{$fiche->date_validation|date_format:"%d %b %Y à %Hh%M"}}
          </td>
        </tr>
        <tr>
          <td colspan="2"></td>
          <th>Transmise à</th>
          <td class="text">
            {{$fiche->_ref_service_valid->_view}}
          </td>
        </tr>
      </table>
      
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">
            Validation du Chef de Service
          </th>
        </tr>
        {{if $fiche->service_date_validation}}
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
        {{else}}
        <tr>
          <td colspan="2">
            Aucune Mesures Prises actuellement
          </td>
        </tr>
        {{/if}}
      
        {{if $fiche->service_date_validation}}
        <tr>
          <td colspan="2" style="padding-bottom:20px;"></td>
        </tr>
        <tr>
          <th class="category" colspan="2">
            Validation du Service Qualité
          </th>
        </tr>
        {{if $fiche->qualite_date_validation}}
        <tr>
          <th>Validée par</th>
          <td>
           {{$fiche->_ref_qualite_valid->_view}}
            <br />le {{$fiche->qualite_date_validation|date_format:"%d %b %Y à %Hh%M"}}
          </td>
        </tr>
        <tr>
          <th>Vérification</th>
          <td>
            {{if $fiche->qualite_date_verification}}
              le {{$fiche->qualite_date_verification|date_format:"%d %B %Y"}}
            {{else}}
            Actions Non Vérifiées
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Contrôle</th>
          <td>
            {{if $fiche->qualite_date_controle}}
              le {{$fiche->qualite_date_controle|date_format:"%d %B %Y"}}            
            {{else}}
            Actions Non Contrôlées
            {{/if}}          
          </td>
        </tr>
        {{else}}
        <tr>
          <td colspan="2">
            Ces mesures n'ont pas encore été validées
          </td>
        </tr>
        {{/if}}
        {{/if}}
      </table>
    </td>
  </tr>
</table>
<table class="main">
  <tr>
    <td>