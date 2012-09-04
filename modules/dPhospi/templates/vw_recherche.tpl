{{if !$dialog}}
<script type="text/javascript">

function printRecherche() {
  var form = document.typeVue;
  var url = new Url("dPhospi", "vw_recherche");
  url.addElement(form.typeVue);
  url.addElement(form.selPrat);
  url.addElement(form.selService);
  url.addElement(form.date_recherche);
  url.popup(800, 700, 'Planning');
}

Main.add(function () {
  Calendar.regField(getForm("typeVue").date_recherche);
});

</script>

<form name="typeVue" action="?m={{$m}}" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  
  {{if $typeVue}}
  <button type="button" class="button print" style="float: right;" onclick="printRecherche()">{{tr}}Print{{/tr}}</button>
  
  <select name="selPrat" onchange="this.form.submit()" style="float: right;">
    <option value="">&mdash; Tous les praticiens</option>
    {{foreach from=$listPrat item=curr_prat}}
      <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $selPrat == $curr_prat->user_id}}selected="selected"{{/if}}>
        {{$curr_prat->_view}}
      </option>
    {{/foreach}}
  </select>
  {{/if}}
  <select name="typeVue" onchange="this.form.submit()">
    <option value="0" {{if $typeVue == 0}}selected="selected"{{/if}}>Afficher les lits disponibles</option>
    <option value="1" {{if $typeVue == 1}}selected="selected"{{/if}}>Afficher les patients présents</option>
  </select>

  <select name="selService" onchange="this.form.submit()">
    <option value="">&mdash; Tous les services</option>
    {{foreach from=$services item="service"}}
      <option value="{{$service->_id}}" {{if $selService == $service->_id}}selected="selected"{{/if}}>{{$service->_view}}</option>
    {{/foreach}}
  </select>
  <input type="hidden" name="date_recherche" class="dateTime" value="{{$date_recherche}}" onchange="this.form.submit()" />
</form>
{{/if}}

<table class="tbl main">
  {{if $typeVue == 0}}
  <tr>
    <th class="title" colspan="4">
      {{$date_recherche|date_format:$conf.datetime}} : {{$libre|@count}} lit(s) disponible(s)
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
    <td class="text">
      {{$curr_lit.chambre}}
      {{if $curr_lit.caracteristiques != ""}}
        <div class="compact">
          {{$curr_lit.caracteristiques}}
        </div>
      {{/if}}
    </td>
    <td class="text">{{$curr_lit.lit}}</td>
    <td class="text">{{$curr_lit.limite|date_format:"%A %d %B %Y à %Hh%M"}}
  </tr>
  {{/foreach}}
  
  {{else}}
  <tr>
    <th class="title" colspan="9">
      {{if $selPrat}}
        Dr {{$listPrat.$selPrat->_view}} -
      {{/if}}
      {{$date_recherche|date_format:$conf.datetime}} : {{$listAff.Aff|@count}} patient(s) placé(s)
      {{if $listAff.NotAff|@count}}- {{$listAff.NotAff|@count}} patient(s) non placé(s){{/if}}
    </th>
  </tr>
  <tr>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
    <th>{{mb_label class=CSejour field=praticien_id}}</th>
    <th>{{mb_title class=CAffectation field=lit_id}}</th>
    <th colspan="2">
     {{tr}}CAffectation{{/tr}} /
     {{mb_title class=CAffectation field=_duree}}
    </th>
    <th>Motif</th>
    <th>Bornes<br/>GHM</th>
  </tr>
  {{foreach from=$listAff key=_type_aff item=_liste_aff}}
  {{foreach from=$_liste_aff item=_affectation}}
  {{if $_type_aff == "Aff"}}
  {{assign var=_sejour value=$_affectation->_ref_sejour}}
  {{else}}
  {{assign var=_sejour value=$_affectation}}
  {{/if}}
  {{assign var=_patient   value=$_sejour->_ref_patient}}
  {{assign var=_praticien value=$_sejour->_ref_praticien}}
  {{assign var=_GHM       value=$_sejour->_ref_GHM}}
  <tr>
    <td class="text">
      {{if $canPlanningOp->read && !$dialog}}
      <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$_patient->_id}}">
        <img src="images/icons/edit.png" alt="modifier" />
      </a>
      <a class="action" style="float: right"  title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$_sejour->_id}}">
        <img src="images/icons/planning.png" alt="modifier" />
      </a>
      {{/if}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}')">
        {{$_patient}}
      </span>
    </td>
    <td class="text">
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_praticien}}
    </td>
    {{if $_type_aff == "Aff"}}
    <td class="text">{{$_affectation->_view}}</td>
    <td class="text">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}')">
        {{mb_include module=system template=inc_interval_datetime from=$_affectation->entree to=$_affectation->sortie}}
      </span>
    </td>
    {{else}}
    <td class="text empty">Non placé</td>
    <td class="text">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
        {{mb_include module=system template=inc_interval_datetime from=$_sejour->entree to=$_sejour->sortie}}
      </span>
    </td>
    {{/if}}
    <td>{{$_affectation->_duree}}</td>
    
    <td class="text">
      {{if $_sejour->libelle}}
        {{$_sejour->libelle}}
      {{else}}
        {{foreach from=$_sejour->_ref_operations item=_operation}}
          {{mb_include module=planningOp template=inc_vw_operation operation=$_operation}}
        {{/foreach}}
      {{/if}}
    </td>
    
    <td style="text-align: center;">
      {{if $_GHM->_DP}}
        De {{$_GHM->_borne_basse}}
        à {{$_GHM->_borne_haute}} nuits
        <br />
        {{if $_GHM->_borne_basse > $_GHM->_duree}}
        <div class="warning">Séjour trop court</div>
        <img src="images/icons/cross.png" alt="alerte" />
        {{elseif $_GHM->_borne_haute < $_GHM->_duree}}
        <div class="warning">Séjour trop long</div>
        {{else}}
        <div class="info">Dans les bornes</div>
        {{/if}}
      {{else}}
      &mdash;
      {{/if}}
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{/if}}
</table>