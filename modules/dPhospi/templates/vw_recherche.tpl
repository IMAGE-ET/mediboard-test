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
    <th class="title" colspan="4"">
      {{$date_recherche|date_format:$dPconfig.datetime}} : {{$libre|@count}} lit(s) disponible(s)
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
      {{$date_recherche|date_format:$dPconfig.datetime}} : {{$listAff|@count}} patient(s)
    </th>
  </tr>
  <tr>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
    <th>{{mb_label class=CSejour field=praticien_id}}</th>
    <th>{{mb_title class=CAffectation field=lit_id}}</th>
    <th colspan="2">
  	 {{tr}}CSejour{{/tr}} /
     {{mb_title class=CSejour field=_duree_prevue}}
		</th>
    <th colspan="2">
     {{tr}}CAffectation{{/tr}} /
     {{mb_title class=CAffectation field=_duree}}
		</th>
    <th>Bornes<br/>GHM</th>
  </tr>
  {{foreach from=$listAff item=curr_aff}}
  {{assign var=sejour value=$curr_aff->_ref_sejour}}
  {{assign var=patient value=$sejour->_ref_patient}}
  {{assign var=praticien value=$sejour->_ref_praticien}}
  {{assign var=GHM value=$sejour->_ref_GHM}}
  <tr>
    <td class="text">
      {{if $canPlanningOp->read && !$dialog}}
      <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
        <img src="images/icons/edit.png" alt="modifier" />
      </a>
      <a class="action" style="float: right"  title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
        <img src="images/icons/planning.png" alt="modifier" />
      </a>
      {{/if}}
			<span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
        {{$patient}}
			</span>
    </td>
    <td class="text">
      <div class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};">
        {{$praticien->_view}}
      </div>
    </td>
    <td class="text">{{$curr_aff->_view}}</td>
    <td class="text">
    	<span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
        {{mb_include module=system template=inc_interval_datetime from=$sejour->entree_prevue to=$sejour->sortie_prevue}}
      </span>
    </td>
    <td>{{$sejour->_duree_prevue}}</td>
    <td class="text">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_aff->_guid}}')">
        {{mb_include module=system template=inc_interval_datetime from=$curr_aff->entree to=$curr_aff->sortie}}
      </span>
    </td>
    <td>{{$curr_aff->_duree}}</td>
		
    <td style="text-align: center;">
      {{if $GHM->_DP}}
        De {{$GHM->_borne_basse}}
        à {{$GHM->_borne_haute}} nuits
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
      &mdash;
      {{/if}}
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>