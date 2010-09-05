{{mb_include_script module=dPhospi script=vw_affectations}}

<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("chgAff").date, null, {noView: true, inline: true, container: $('calendar-container')});
  //initServicesState();
});
</script>

<table class="main">
  <tr>
    <td colspan="2">
      <div style="float:right;">
        <strong>Planning du {{$date|date_format:$dPconfig.longdate}}</strong>
      </div>
      {{if $alerte}}
      <div class="warning">
        <a href="#1" onclick="showAlerte('{{$emptySejour->_type_admission}}')">
          Il y a {{$alerte}} patient(s) non placés dans la semaine qui vient
          {{if $emptySejour->_type_admission}}
          ({{tr}}CSejour._type_admission.{{$emptySejour->_type_admission}}{{/tr}})
          {{/if}}
        </a>
      </div>
      {{else}}
      <div class="message">
        Tous les patients sont placés pour la semaine à venir
      </div>
      {{/if}}
    </td>
    <th>
      {{$totalLits}} place(s) de libre
    </th>
  </tr>
  <tr>
    <td>
      <button type="button" onclick="showLegend()" class="search">Légende</button>
      <button type="button" onclick="showRapport('{{$date}}')" class="print">Rapport</button>
    </td>
    <td>
      <form name="chgAff" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
			
      {{foreach from=$services item=curr_service}}
        <label title="Afficher le service {{$curr_service->nom}}">
        <input
          type="checkbox"
          name="list_services[]"
          value="{{$curr_service->_id}}"
					{{if in_array($curr_service->_id, $list_services)}}
          checked="checked" 
					{{/if}}
          />
          {{$curr_service->nom}}
        </label>
      {{/foreach}}
			  <button class="search" type="button" onclick="this.form.submit();">Afficher</button> 
      </form>
    </td>
    {{include file="inc_mode_hospi.tpl"}}
  </tr>

  <tr>
    <td class="greedyPane" colspan="2">
      <table class="affectations">
        <tr>
        {{foreach from=$services item=curr_service}}
				  {{if $curr_service->_ref_chambres|@count}}
          <td style="width: 1%" class="fullService" id="service{{$curr_service->service_id}}">
          {{include file="inc_affectations_services.tpl"}}
          </td>
          {{/if}}
        {{/foreach}}
        </tr>
      </table>
    </td>
    <td>
      <div id="calendar-container"></div>
      {{if $can->edit}}
      
      <form name="chgFilter" action="?m={{$m}}" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <table class="form">
          <tr>
            <td class="button" colspan="2">
              {{mb_field object=$emptySejour field="_type_admission" defaultOption="&mdash; Toutes les admissions" onchange="this.form.submit()"}}
            </td>
          </tr>
          <tr>
            <th><label for="triAdm">Trier par</label></th>
            <td>
              <select name="triAdm" onchange="this.form.submit()">
                <option value="praticien" {{if $triAdm == "praticien"}}selected="selected"{{/if}}>Praticien</option>
                <option value="date_entree" {{if $triAdm == "date_entree"}}selected="selected"{{/if}}>Heure d'entrée</option>
              </select>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <select name="filterFunction" style="width: 16em;" onchange="this.form.submit()">
                <option value=""> &mdash; Toutes les fonctions</option>
                {{foreach from=$functions_filter item=_function}}
                  <option value="{{$_function->_id}}" {{if $_function->_id == $filterFunction}}selected="selected"{{/if}} class="mediuser" style="border-color: #{{$_function->color}};">{{$_function}}</option>
                {{/foreach}}
              </select>
            </td>
          </tr>
        </table>
      </form>
      
      <form name="addAffectationsejour" action="?m={{$m}}" method="post">
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="lit_id" value="" />
      <input type="hidden" name="sejour_id" value="" />
            
      <table class="sejourcollapse" id="sejour_bloque">
        <tr>
        <td class="selectsejour">
          <input type="radio" id="hospitalisation" onclick="selectHospitalisation()" />
          <script type="text/javascript">new Draggable('sejour_bloque', {revert:true})</script>
        </td>
        <td class="patient" onclick="flipSejour('bloque')">
          <strong><a name="sejourbloque">[BLOQUER UN LIT]</a></strong>
        </td>
        </tr>
        <tr>
          <td><em>Entrée</em></td>
          <td>{{mb_field object=$affectation field="entree" form="addAffectationsejour" register=true}}</td>
        </tr>
        <tr>
          <td><em>Sortie</em></td>
          <td>{{mb_field object=$affectation field="sortie" form="addAffectationsejour" register=true}}</td>
      </tr>
      <tr>
        <td class="date highlight" colspan="2">
          <label for="rques">Remarques</label> : 
          <textarea name="rques"></textarea>
        </td>
      </tr>
      </table>
      </form>

      {{foreach from=$groupSejourNonAffectes key=group_name item=sejourNonAffectes}}
        {{include file="inc_affectations_liste.tpl"}}
      {{/foreach}}
      {{/if}}
    </td>
  </tr>

</table>