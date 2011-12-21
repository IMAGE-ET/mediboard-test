<script type="text/javascript">
  $("count_{{$type}}").update("{{$update_count}}");
  {{if $type != "deplacements"}}
  Main.add(function () {
    controlTabs = new Control.Tabs.create('tabs-edit-sorties-{{$type}}', true);
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
      <th class="title" colspan="100">
        Déplacements prévus (<span id="count_{{$type}}">{{$deplacements|@count}}</span>)
        &mdash; {{$date|date_format:$conf.longdate}}
      </th>
    </tr>
    <tr>
      <th class="not-printable">
        <button class="print notext" style="float:left;" onclick="$('deplacements').print()">{{tr}}Print{{/tr}}</button>
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
      {{mb_include module=dPhospi template=inc_check_deplacement_line}}
    {{foreachelse}}
      <tr><td colspan="6" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
    {{/foreach}}
  </table>
{{else}}
  <script type="text/javascript">
    refreshList_{{$type}} = function(order_col, order_way) {
      refreshList(order_col, order_way, '{{$type}}');
    }
  </script>
  <ul id="tabs-edit-sorties-{{$type}}" class="control_tabs">
    <li>
      <a href="#places-{{$type}}">Placés (<span id="count_deplacements">{{$sorties|@count}}</span>)</a>
    </li>
    <li>
      <a href="#non-places-{{$type}}">Non placés (<span id="count_presents">{{$sortiesNP|@count}}</span>)</a>
    </li>
  </ul>
  <hr class="control_tabs" />
  <div id="places-{{$type}}" style="display: none;">
  <table class="tbl">
    <tr class="only-printable">
      <th class="title" colspan="100">
        {{if $type == "presents"}}
          Patients présents (<span id="count_{{$type}}">{{$sorties|@count}}/{{$sortiesNP|@count}}</span>)
        {{else}}
          Sorties {{tr}}CSejour.type.{{$type}}{{/tr}} prévues (<span id="count_{{$type}}">{{$sorties|@count}}/{{$sortiesNP|@count}}</span>)
        {{/if}}
        &mdash; {{$date|date_format:$conf.longdate}}
      </th>
    </tr>
    <tr>
      <th class="not-printable">
        <button class="print notext" style="float:left;" onclick="$('places-{{$type}}').print()">{{tr}}Print{{/tr}}</button>
        Sortie
      </th>
      <th>{{mb_colonne class="CAffectation" field="_patient"   order_col=$order_col order_way=$order_way function=refreshList_$type}}</th>
      <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way function=refreshList_$type}}</th>
      <th>Motif</th>
      <th>{{mb_colonne class="CAffectation" field="_chambre"   order_col=$order_col order_way=$order_way function=refreshList_$type}}</th>
      {{if $type == "presents"}}
      <th>Entree</th>
      {{/if}}
      <th>{{mb_colonne class="CAffectation" field="sortie"     order_col=$order_col order_way=$order_way function=refreshList_$type}}</th>
    </tr>
    {{foreach from=$sorties item=_sortie}}
      {{mb_include module=dPhospi template=inc_check_sortie_line}}
    {{foreachelse}}
      <tr><td colspan="5" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
    {{/foreach}}
  </table>
  </div>
  <div id="non-places-{{$type}}" style="display: none;">
    <table class="tbl">
      <tr>
        <th>
          <button class="print notext not-printable" style="float:left;" onclick="$('non-places-{{$type}}').print()">{{tr}}Print{{/tr}}</button>
          {{mb_colonne class="CAffectation" field="_patient" order_col=$order_col order_way=$order_way function=refreshList_$type}}
        </th>
        <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way function=refreshList_$type}}</th>
        <th>Motif</th>
        {{if $type == "presents"}}
        <th>Entree</th>
        {{/if}}
        <th>{{mb_colonne class="CAffectation" field="sortie" order_col=$order_col order_way=$order_way function=refreshList_$type}}</th>
      </tr>
      {{foreach from=$sortiesNP item=_sortie}}
        {{mb_include module=dPhospi template=inc_check_sortieNP_line}}
      {{foreachelse}}
        <tr><td colspan="5" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
    {{/foreach}}
    </table>
  </div>
{{/if}}