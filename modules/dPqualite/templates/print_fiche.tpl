<table class="form" id="admission">
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
            {{if $fiche->type_incident}}
            risque d'incident
            {{else}}
            incident
            {{/if}}<br />
            survenu le {{$fiche->date_incident|date_format:"%A %d %B %Y à %Hh%M"}}
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
            Un
            {{if $fiche->elem_concerne==4}}    Matériel
            {{elseif $fiche->elem_concerne==3}}Médicament
            {{elseif $fiche->elem_concerne==2}}Membre du Personnel
            {{elseif $fiche->elem_concerne==1}}Visiteur
            {{else}}                           Patient
            {{/if}}
          </th>
          <td>{{$fiche->elem_concerne_detail|nl2br}}</td>
        </tr>
        <tr>
          <th>
            Lieu
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
        {{if $fiche->descr_faits}}
        <tr>
          <th>Description des faits</th>
          <td class="text">{{$fiche->descr_faits|nl2br}}</td>
        </tr>
        {{/if}}
        {{if $fiche->mesures}}
        <tr>
          <th>Mesures Prises</th>
          <td class="text">{{$fiche->mesures|nl2br}}</td>
        </tr>
        {{/if}}
        {{if $fiche->descr_consequences}}
        <tr>
          <th>Description des conséquences</th>
          <td class="text">{{$fiche->descr_consequences|nl2br}}</td>
        </tr>
        {{/if}}
        <tr>
          <th>Gravité estimée</th>
          <td>
            {{if $fiche->gravite==2}}    Importante
            {{elseif $fiche->gravite==1}}Modérée
            {{else}}                     Nulle
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Plainte prévisible</th>
          <td class="greedyPane">
            {{if $fiche->plainte}} Oui
            {{else}}               Non
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Commission concialition</th>
          <td>
            {{if $fiche->commission}} Oui
            {{else}}               Non
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Evénement déjà survenu à la connaissance de l'auteur</th>
          <td>
            {{if $fiche->deja_survenu!==null}}
              {{if $fiche->deja_survenu}} Oui
              {{else}}                    Non
              {{/if}}
            {{else}}                      Ne sais pas
            {{/if}}
          </td>
        </tr>
      </table>
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="4">
            Validation de la Fiche
          </th>
        </tr>
        <tr>
          <th>Degré d'Urgence</th>
          <td>{{$fiche->degre_urgence}}</td>
          <th>Validée par</th>
          <td>
            {{$fiche->_ref_user_valid->_view}}
            <br />le {{$fiche->date_validation|date_format:"%d %b %Y à %Hh%M"}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>    