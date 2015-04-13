{{assign var=show_duree_preop value=$conf.dPplanningOp.COperation.show_duree_preop}}
{{assign var=show_age_sexe_mvt value=$conf.dPhospi.show_age_sexe_mvt}}
{{assign var=show_hour_anesth_mvt value=$conf.dPhospi.show_hour_anesth_mvt}}
{{assign var=show_retour_mvt value=$conf.dPhospi.show_retour_mvt}}
{{assign var=show_collation_mvt value=$conf.dPhospi.show_collation_mvt}}
{{assign var=show_sortie_mvt value=$conf.dPhospi.show_sortie_mvt}}

{{mb_script module=dPadmissions script=admissions ajax=true}}

<script>
  $('count_{{$type}}_{{$type_mouvement}}').update('('+'{{$update_count}}'+')');
  Main.add(function () {
    {{if $type != "deplacements"}}
      controlTabs = Control.Tabs.create('tabs-edit-mouvements-{{$type}}_{{$type_mouvement}}', true);
    {{else}}
      controlTabs = Control.Tabs.create('tabs-edit-mouvements-{{$type}}', true);
    {{/if}}
    {{if $isImedsInstalled}}
      ImedsResultsWatcher.loadResults();
    {{/if}}
  });
</script>

{{assign var=update_splitted value="/"|split:$update_count}}

{{if $type == "deplacements"}}
  <script>
    refreshList_deplacements = function(order_col, order_way) {
      refreshList(order_col, order_way, 'deplacements');
    }
  </script>
  <ul id="tabs-edit-mouvements-{{$type}}" class="control_tabs">
    <li>
      <a href="#places-{{$type}}_entrants">Entrants <small id="count_dep_entrants">({{$update_splitted.0}})</small></a>
    </li>
    <li>
      <a href="#places-{{$type}}_sortants">Sortants <small id="count_dep_sortants">({{$update_splitted.1}})</small></a>
    </li>
  </ul>

  <div id="places-{{$type}}_entrants" style="display: none;">
    <table class="tbl">
      <tr class="only-printable">
        <th class="title text" colspan="100">
          Déplacements entrants ({{$dep_entrants|@count}})
          {{if $praticien->_id}}
            &mdash; Dr {{$praticien}}
          {{/if}}
          &mdash; {{$date|date_format:$conf.longdate}}
        </th>
      </tr>
      <tr>
        <th class="not-printable">
          <button class="print notext" style="float:left;" onclick="$('deplacements_').print()">{{tr}}Print{{/tr}}</button>
          Déplacements entrants
        </th>
        {{assign var=url value="?m=$m&tab=$tab"}}
        <th>{{mb_colonne class="CAffectation" field="_patient"   order_col=$order_col order_way=$order_way function=refreshList_deplacements}}</th>
        {{if $show_age_sexe_mvt}}
          <th class="narrow">
            <label title="Sexe">S</label>
          </th>
          <th class="narrow">
            {{mb_label class=CPatient field=_age}}
          </th>
        {{/if}}
        <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way function=refreshList_deplacements}}</th>
        <th>Motif</th>
        {{if $show_hour_anesth_mvt}}
          <th>
            {{mb_colonne class="CAffectation" field="_hour" order_col=$order_col order_way=$order_way function=refreshList_deplacements}}
          </th>
          <th>
            {{mb_colonne class="CAffectation" field="_anesth" order_col=$order_col order_way=$order_way function=refreshList_deplacements}}
          </th>
        {{/if}}
        <th>{{mb_colonne class="CAffectation" field="_chambre"   order_col=$order_col order_way=$order_way function=refreshList_deplacements}}</th>
        <th>Provenance</th>
        <th>{{mb_colonne class="CAffectation" field="entree"     order_col=$order_col order_way=$order_way function=refreshList_deplacements}}</th>
      </tr>
      {{foreach from=$dep_entrants item=_dep_entrants_by_service key=_service_id}}
        <tr>
          <th class="title text" colspan="100">
            {{$services.$_service_id}}
          </th>
        </tr>
        {{foreach from=$_dep_entrants_by_service item=_sortie}}
          {{mb_include module=hospi template=inc_check_deplacement_line sens="entrants"}}
        {{/foreach}}
      {{foreachelse}}
        <tr><td colspan="100" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
      {{/foreach}}
    </table>
  </div>
  <div id="places-{{$type}}_sortants" style="display: none;">
    <table class="tbl">
      <tr class="only-printable">
        <th class="title text" colspan="100">
          Déplacements sortants ({{$dep_sortants|@count}})
          {{if $praticien->_id}}
            &mdash; Dr {{$praticien}}
          {{/if}}
          &mdash; {{$date|date_format:$conf.longdate}}
        </th>
      </tr>
      <tr>
        <th class="not-printable">
          <button class="print notext" style="float:left;" onclick="$('deplacements_').print()">{{tr}}Print{{/tr}}</button>
          Déplacements sortants
        </th>
        {{assign var=url value="?m=$m&tab=$tab"}}
        <th>{{mb_colonne class="CAffectation" field="_patient"   order_col=$order_col order_way=$order_way function=refreshList_deplacements}}</th>
        {{if $show_age_sexe_mvt}}
          <th class="narrow">
            <label title="Sexe">S</label>
          </th>
          <th class="narrow">{{mb_label class="CPatient" field="_age"}}</th>
        {{/if}}
        <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way function=refreshList_deplacements}}</th>
        <th>Motif</th>
        {{if $show_hour_anesth_mvt}}
          <th>
            {{mb_colonne class="CAffectation" field="_hour" order_col=$order_col order_way=$order_way function=refreshList_deplacements}}
          </th>
          <th>
            {{mb_colonne class="CAffectation" field="_anesth" order_col=$order_col order_way=$order_way function=refreshList_deplacements}}
          </th>
        {{/if}}
        <th>{{mb_colonne class="CAffectation" field="_chambre"   order_col=$order_col order_way=$order_way function=refreshList_deplacements}}</th>
        <th>Destination</th>
        <th>{{mb_colonne class="CAffectation" field="sortie"     order_col=$order_col order_way=$order_way function=refreshList_deplacements}}</th>
      </tr>
      {{foreach from=$dep_sortants item=_dep_sortants_by_service key=_service_id}}
        {{if isset($services.$_service_id|smarty:nodefaults)}}
          <tr>
            <th class="title text" colspan="100">
              {{$services.$_service_id}}
            </th>
          </tr>
        {{/if}}
        {{foreach from=$_dep_sortants_by_service item=_sortie key=_service_id}}
          {{mb_include module=hospi template=inc_check_deplacement_line sens="sortants"}}
        {{/foreach}}
      {{foreachelse}}
        <tr><td colspan="100" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
      {{/foreach}}
    </table>
  </div>
{{else}}
  <script>
    refreshList_{{$type}}{{$type_mouvement}} = function(order_col, order_way) {
      refreshList(order_col, order_way, '{{$type}}', '{{$type_mouvement}}');
    }
  </script>
  <ul id="tabs-edit-mouvements-{{$type}}_{{$type_mouvement}}" class="control_tabs">
    <li>
      <a href="#places-{{$type}}_{{$type_mouvement}}">Placés <small id="count_deplacements">({{$update_splitted.0}})</small></a>
    </li>
    <li>
      <a href="#non-places-{{$type}}_{{$type_mouvement}}">Non placés <small id="count_presents">({{$update_splitted.1}})</small></a>
    </li>
    {{if $type == "presents"}}
      <li>
        <div>
          <form name="chgAff" method="get">
            <input type="hidden" name="type" value="{{$type}}" />
            <input type="hidden" name="type_mouvement" value="{{$type_mouvement}}" />
            <select name="mode" onchange="$V(getForm('typeVue').mode, this.value)">
              <option value="0" {{if $mode == 0}}selected="selected"{{/if}}>{{tr}}Instant view{{/tr}}</option>
              <option value="1" {{if $mode == 1}}selected="selected"{{/if}}>{{tr}}Day view{{/tr}}</option>
            </select>
            <label>
              Heure pour vue instantanée :
              <select name="hour_instantane" onchange="$V(getForm('typeVue').hour_instantane, this.value)">
                {{foreach from=0|range:23 item=i}}
                  {{assign var=j value=$i|str_pad:2:"0":$smarty.const.STR_PAD_LEFT}}
                  <option value="{{$j}}" {{if $j == $hour_instantane}}selected{{/if}}>{{$j}}h</option>
                {{/foreach}}
              </select>
            </label>
          </form>
        </div>
      </li>
    {{/if}}
  </ul>

  <div id="places-{{$type}}_{{$type_mouvement}}" style="display: none;">
  <table class="tbl">
    <tr class="only-printable">
      <th class="title text" colspan="100">
        {{if $type == "presents"}}
          Patients présents
        {{elseif $type == "ambu"}}
          {{tr}}CSejour.type.{{$type}}{{/tr}}
        {{else}}
          {{tr}}CSejour.type_mouvement.{{$type_mouvement}}{{/tr}} {{tr}}CSejour.type.{{$type}}{{/tr}}
        {{/if}}
        placé
        ({{$update_splitted.0}})
        {{if $praticien->_id}}
          &mdash; Dr {{$praticien}}
        {{/if}}
        &mdash; {{$date|date_format:$conf.longdate}}
      </th>
    </tr>
    <tr>
      {{if $show_duree_preop && $type_mouvement != "sorties"}}
        <th class="narrow">Heure US</th>
      {{/if}}
      <th>
        <button class="print notext" style="float:left;" onclick="$('places-{{$type}}_{{$type_mouvement}}').print()">{{tr}}Print{{/tr}}</button>
        {{mb_colonne class="CAffectation" field="_patient"   order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
      </th>
      {{if $show_age_sexe_mvt}}
        <th class="narrow">
          <label title="Sexe">S</label>
        </th>
        <th class="narrow">{{mb_label class="CPatient" field="_age"}}</th>
      {{/if}}
      <th>
        {{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
      </th>
      <th>Motif</th>
      {{if $show_hour_anesth_mvt}}
        <th>
          {{mb_colonne class="CAffectation" field="_hour" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
        </th>
        <th>
          {{mb_colonne class="CAffectation" field="_anesth" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
        </th>
      {{/if}}
      {{if "dmi"|module_active}}
        <th class="narrow">{{tr}}CDMI{{/tr}}</th>
      {{/if}}
      <th>
        {{mb_colonne class="CAffectation" field="_chambre"   order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
      </th>
      <th>
        {{mb_colonne class="CAffectation" field="entree"     order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
      </th>
      <th>
        {{mb_colonne class="CAffectation" field="sortie"     order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
      </th>
      {{if $type == "ambu"}}
        {{if $show_retour_mvt}}
          <th style="min-width: 100px; max-width: 100px; width: 100px;">Retour de bloc</th>
        {{/if}}
        {{if $show_collation_mvt}}
          <th style="min-width: 100px; max-width: 100px; width: 100px;">Collation</th>
        {{/if}}
        {{if $show_sortie_mvt}}
          <th style="min-width: 100px; max-width: 100px; width: 100px;">Sortie</th>
        {{/if}}
      {{/if}}
    </tr>
    {{foreach from=$mouvements item=_mouvements_by_service key=_service_id}}
      {{if isset($services.$_service_id|smarty:nodefaults)}}
        <tr>
          <th class="title text" colspan="100">
            {{$services.$_service_id}}
          </th>
        </tr>
      {{/if}}
      {{foreach from=$_mouvements_by_service item=_affectation}}
        {{mb_include module=hospi template=inc_check_sortie_line affectation=$_affectation sejour=$_affectation->_ref_sejour}}
      {{/foreach}}
    {{foreachelse}}
      <tr><td colspan="100" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
    {{/foreach}}
  </table>
  </div>
  <div id="non-places-{{$type}}_{{$type_mouvement}}" style="display: none;">
    <table class="tbl">
      <tr class="only-printable">
        <th class="title text" colspan="100">
          {{if $type == "presents"}}
            Patients présents
          {{elseif $type == "ambu"}}
            {{tr}}CSejour.type.{{$type}}{{/tr}}
          {{else}}
            {{tr}}CSejour.type_mouvement.{{$type_mouvement}}{{/tr}} {{tr}}CSejour.type.{{$type}}{{/tr}}
          {{/if}}
          non placé
          ({{$update_splitted.1}})
        {{if $praticien->_id}}
          &mdash; Dr {{$praticien}}
        {{/if}}
          &mdash; {{$date|date_format:$conf.longdate}}
        </th>
      </tr>
      <tr>
        {{if $show_duree_preop && $type_mouvement != "sorties"}}
          <th class="narrow">Heure US</th>
        {{/if}}
        <th>
          <button class="print notext not-printable" style="float:left;" onclick="$('non-places-{{$type}}_{{$type_mouvement}}').print()">{{tr}}Print{{/tr}}</button>
          {{mb_colonne class="CAffectation" field="_patient" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
        </th>
        {{if $show_age_sexe_mvt}}
          <th class="narrow">
            <label title="Sexe">S</label>
          </th>
          <th class="narrow">{{mb_label class="CPatient" field="_age"}}</th>
        {{/if}}
        <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}</th>
        <th>Motif</th>
        {{if $show_hour_anesth_mvt}}
          <th>
            {{mb_colonne class="CAffectation" field="_hour" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
          </th>
          <th>
            {{mb_colonne class="CAffectation" field="_anesth" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
          </th>
        {{/if}}
        {{if "dmi"|module_active}}
          <th class="narrow">{{tr}}CDMI{{/tr}}</th>
        {{/if}}
        <th>
          {{mb_colonne class="CAffectation" field="_chambre"   order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
        </th>
        <th>{{mb_colonne class="CAffectation" field="entree" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}</th>
        <th>{{mb_colonne class="CAffectation" field="sortie" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}</th>
        {{if $type == "ambu"}}
          {{if $show_retour_mvt}}
            <th style="min-width: 100px; max-width: 100px; width: 100px;">Retour de bloc</th>
          {{/if}}
          {{if $show_collation_mvt}}
            <th style="min-width: 100px; max-width: 100px; width: 100px;">Collation</th>
          {{/if}}
          {{if $show_sortie_mvt}}
            <th style="min-width: 100px; max-width: 100px; width: 100px;">Sortie</th>
          {{/if}}
        {{/if}}
      </tr>
      {{foreach from=$mouvementsNP item=_mouvemementsNP_by_service key=_service_id}}
        {{if isset($services.$_service_id|smarty:nodefaults)}}
          <tr>
            <th class="title text" colspan="100">
              {{$services.$_service_id}}
            </th>
          </tr>
        {{/if}}
        {{foreach from=$_mouvemementsNP_by_service item=_sejour}}
          {{mb_include module=hospi template=inc_check_sortie_line affectation=0 sejour=$_sejour}}
        {{/foreach}}
      {{foreachelse}}
        <tr><td colspan="100" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
    {{/foreach}}
    </table>
  </div>
{{/if}}