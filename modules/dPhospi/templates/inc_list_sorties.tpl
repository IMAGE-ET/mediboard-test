<script type="text/javascript">
  $('count_{{$type}}_{{$type_mouvement}}').update('('+'{{$update_count}}'+')');
  {{if $type != "deplacements"}}
  Main.add(function () {
    controlTabs = new Control.Tabs.create('tabs-edit-mouvements-{{$type}}_{{$type_mouvement}}', true);
  });
  {{/if}}
</script>

{{if $type == "deplacements"}}
  <script type="text/javascript">
    refreshList_deplacements = function(order_col, order_way) {
      refreshList(order_col, order_way, 'deplacements');
    }
  </script>
  <table class="tbl">
    <tr class="only-printable">
      <th class="title text" colspan="100">
        Déplacements ({{$deplacements|@count}})
        {{if $service->_id}}
          &mdash; {{$service}}
        {{/if}}
        {{if $praticien->_id}}
          &mdash; Dr {{$praticien}}
        {{/if}}
        &mdash; {{$date|date_format:$conf.longdate}}
      </th>
    </tr>
    <tr>
      <th class="not-printable">
        <button class="print notext" style="float:left;" onclick="$('deplacements_').print()">{{tr}}Print{{/tr}}</button>
        Déplacement
      </th>
      {{assign var=url value="?m=$m&tab=$tab"}}
      <th>{{mb_colonne class="CAffectation" field="_patient"   order_col=$order_col order_way=$order_way function=refreshList_deplacements}}</th>
      <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way function=refreshList_deplacements}}</th>
      <th>{{mb_colonne class="CAffectation" field="_chambre"   order_col=$order_col order_way=$order_way function=refreshList_deplacements}}</th>
      <th>Destination</th>
      <th>{{mb_colonne class="CAffectation" field="sortie"     order_col=$order_col order_way=$order_way function=refreshList_deplacements}}</th>
    </tr>
    {{foreach from=$deplacements item=_sortie}}
      {{mb_include module=hospi template=inc_check_deplacement_line}}
    {{foreachelse}}
      <tr><td colspan="6" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
    {{/foreach}}
  </table>
{{else}}
  <script type="text/javascript">
    refreshList_{{$type}}{{$type_mouvement}} = function(order_col, order_way) {
      refreshList(order_col, order_way, '{{$type}}', '{{$type_mouvement}}');
    }
  </script>
  <ul id="tabs-edit-mouvements-{{$type}}_{{$type_mouvement}}" class="control_tabs">
    <li>
      <a href="#places-{{$type}}_{{$type_mouvement}}">Placés <small id="count_deplacements">({{$mouvements|@count}})</small></a>
    </li>
    <li>
      <a href="#non-places-{{$type}}_{{$type_mouvement}}">Non placés <small id="count_presents">({{$mouvementsNP|@count}})</small></a>
    </li>
  </ul>
  <hr class="control_tabs" />
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
        ({{$mouvements|@count}})
        {{if $service->_id}}
          &mdash; {{$service}}
        {{/if}}
        {{if $praticien->_id}}
          &mdash; Dr {{$praticien}}
        {{/if}}
        &mdash; {{$date|date_format:$conf.longdate}}
      </th>
    </tr>
    <tr>
      <th>
        <button class="print notext" style="float:left;" onclick="$('places-{{$type}}_{{$type_mouvement}}').print()">{{tr}}Print{{/tr}}</button>
        {{mb_colonne class="CAffectation" field="_patient"   order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
      </th>
      <th>
        {{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
      </th>
      <th>Motif</th>
      {{if "dmi"|module_active}}
        <th class="narrow">{{tr}}CDMI{{/tr}}</th>
      {{/if}}
      <th>
        {{mb_colonne class="CAffectation" field="_chambre"   order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
      </th>
      <th>Entree</th>
      <th colspan="2">
        {{mb_colonne class="CAffectation" field="sortie"     order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
      </th>
    </tr>
    {{foreach from=$mouvements item=_sortie}}
      {{mb_include module=hospi template=inc_check_sortie_line}}
    {{foreachelse}}
      <tr><td colspan="100" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
    {{/foreach}}
  </table>
  </div>
  <div id="non-places-{{$type}}_{{$type_mouvement}}" style="display: none;">
    <table class="tbl">
      <tr class="only-printable">
        <th class="title text" colspan="10">
          {{if $type == "presents"}}
            Patients présents
          {{elseif $type == "ambu"}}
            {{tr}}CSejour.type.{{$type}}{{/tr}}
          {{else}}
            {{tr}}CSejour.type_mouvement.{{$type_mouvement}}{{/tr}} {{tr}}CSejour.type.{{$type}}{{/tr}}
          {{/if}}
          non placé
          ({{$mouvementsNP|@count}})
        {{if $service->_id}}
          &mdash; {{$service}}
        {{/if}}
        {{if $praticien->_id}}
          &mdash; Dr {{$praticien}}
        {{/if}}
          &mdash; {{$date|date_format:$conf.longdate}}
        </th>
      </tr>
      <tr>
        <th>
          <button class="print notext not-printable" style="float:left;" onclick="$('non-places-{{$type}}_{{$type_mouvement}}').print()">{{tr}}Print{{/tr}}</button>
          {{mb_colonne class="CAffectation" field="_patient" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
        </th>
        <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}</th>
        <th>Motif</th>
        {{if "dmi"|module_active}}
          <th class="narrow">{{tr}}CDMI{{/tr}}</th>
        {{/if}}
        <th>
          {{mb_colonne class="CAffectation" field="_chambre"   order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}
        </th>
        <th>Entree</th>
        <th>{{mb_colonne class="CAffectation" field="sortie" order_col=$order_col order_way=$order_way function=refreshList_$type$type_mouvement}}</th>
      </tr>
      {{foreach from=$mouvementsNP item=_sortie}}
        {{mb_include module=hospi template=inc_check_sortieNP_line}}
      {{foreachelse}}
        <tr><td colspan="10" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
    {{/foreach}}
    </table>
  </div>
{{/if}}