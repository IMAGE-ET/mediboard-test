<script language="JavaScript" type="text/javascript">

function popPlanning(date) {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_affectations");
  url.addParam("date", date);
  url.popup(700, 550, 'Planning');
}

Main.add(function () {
  Calendar.regRedirectFlat("{{$date_recherche}}", "?m={{$m}}&tab={{$tab}}&date_recherche=", null, true);
});

</script>

<table class="main">
  <tr>
    <td>
      <form name="typeVue" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <select name="typeVue" onchange="submit()">
        <option value="0" {{if $typeVue == 0}}selected="selected"{{/if}}>Afficher les lits disponible</option>
        <option value="1" {{if $typeVue == 1}}selected="selected"{{/if}}>Afficher les patients présents</option>
      </select>
      </form>
    </td>
    <td>
    <form name="typeService" action="?m={{$m}}" method="get">
    <input type="hidden" name="m" value="{{$m}}" />
    <select name="selService" onchange="submit()">
    <option value="">&mdash; Tous les services</option>
    {{foreach from=$services item="service"}}
      <option value="{{$service->_id}}" {{if $selService == $service->_id}}selected="selected"{{/if}}>{{$service->_view}}</option>
    {{/foreach}}
    </select>
    </form>
    </td>
    <td style="text-align: right;">
      {{if $typeVue}}
      <form name="choosePrat" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <select name="selPrat" onchange="submit()">
      <option value="">&mdash; Tous les praticiens</option>
      {{foreach from=$listPrat item=curr_prat}}
        <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $selPrat == $curr_prat->user_id}}selected="selected"{{/if}}>
          {{$curr_prat->_view}}
        </option>
      {{/foreach}}
      </select>
      </form>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td><div id="calendar-container"></div></td>
    <td colspan="2" class="greedyPane">
      <table class="tbl">
        {{if $typeVue == 0}}
        <tr>
          <th class="title" colspan="4">
            {{$date_recherche|date_format:"%A %d %B %Y à %Hh%M"}} : {{$libre|@count}} lit(s) disponible(s)
          </th>
        </tr>
        <tr>
          <th>Service</th>
          <th>Chambre</th>
          <th>Lit</th>
          <th>Fin de disponibilité</th>
        </tr>
        {{foreach from=$libre item=curr_lit}}
        <tr>
          <td class="text">{{$curr_lit.service}}</td>
          <td class="text">{{$curr_lit.chambre}}</td>
          <td class="text">{{$curr_lit.lit}}</td>
          <td class="text">{{$curr_lit.limite|date_format:"%A %d %B %Y à %Hh%M"}}
        </tr>
        {{/foreach}}
        {{else}}
        <tr>
          <th class="title" colspan="8">
            {{if $selPrat}}
              Dr {{$listPrat.$selPrat->_view}} -
            {{/if}}
            {{$date_recherche|date_format:"%A %d %B %Y"}} : {{$listAff|@count}} patient(s)
          </th>
        </tr>
        <tr>
          <th>Patient</th>
          <th>Praticien</th>
          <th>Service</th>
          <th>Chambre</th>
          <th>Lit</th>
          <th>Séjour</th>
          <th>Occupation du lit</th>
          <th>Bornes GHM</th>
        </tr>
        {{foreach from=$listAff item=curr_aff}}
        {{assign var=sejour value=$curr_aff->_ref_sejour}}
        {{assign var=patient value=$sejour->_ref_patient}}
        {{assign var=praticien value=$sejour->_ref_praticien}}
        {{assign var=GHM value=$sejour->_ref_GHM}}
        <tr>
          <td class="text">
            {{if $canPlanningOp->read}}
            <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
              <img src="images/icons/edit.png" alt="modifier" />
            </a>
            <a class="action" style="float: right"  title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
              <img src="images/icons/planning.png" alt="modifier" />
            </a>
            {{/if}}
            {{$patient->_view}}
          </td>
          <td class="text">
            <div class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};">
              {{$praticien->_view}}
            </div>
          </td>
          <td class="text">{{$curr_aff->_ref_lit->_ref_chambre->_ref_service->nom}}</td>
          <td class="text">{{$curr_aff->_ref_lit->_ref_chambre->nom}}</td>
          <td class="text">{{$curr_aff->_ref_lit->nom}}</td>
          <td class="text">
            Du {{$sejour->entree_prevue|date_format:"%d/%m/%Y à %Hh%M"}}
            au {{$sejour->sortie_prevue|date_format:"%d/%m/%Y à %Hh%M"}}
            ({{$sejour->_duree_prevue}} jours)
          </td>
          <td class="text">
            Du {{$curr_aff->entree|date_format:"%d/%m/%Y à %Hh%M"}}
            au {{$curr_aff->sortie|date_format:"%d/%m/%Y à %Hh%M"}}
            ({{$curr_aff->_duree}} jours)
          </td>
          <td>
            {{if $GHM->_DP}}
              De {{$GHM->_borne_basse}}
              à {{$GHM->_borne_haute}} jours
              <br />
              {{if $GHM->_borne_basse > $GHM->_duree}}
              <div class="warning">Séjour trop court</div>
              <img src="images/icons/cross.png" alt="alerte" />
              {{elseif $GHM->_borne_haute < $GHM->_duree}}
              <div class="warning">Séjour trop long</div>
              {{else}}
              <div class="message">Dans les bornes</div>
              {{/if}}
            {{else}}
            -
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
        {{/if}}
      </table>
    </td>
  </tr>
</table>