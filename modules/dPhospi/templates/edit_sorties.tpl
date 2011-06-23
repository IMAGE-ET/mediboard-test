<script type="text/javascript">

Main.add(function () {
  Calendar.regField(getForm("typeVue").date, null, {noView: true});
});

Main.add(Control.Tabs.create.curry('tabs-edit-sorties', true));

function saveSortie(oFormSortie, oFormAffectation){
  if(oFormSortie){
    oFormAffectation.sortie.value = oFormSortie.sortie.value;
  }
}

</script>

<table class="main">
  <tr>
    <th>
      <form name="typeVue" action="?m={{$m}}" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <label for="vue" title="Choisir un type de vue">Type de vue</label>
        <select name="vue" onchange="this.form.submit()">
          <option value="0" {{if $vue == 0}} selected="selected"{{/if}}>Tout afficher</option>
          <option value="1" {{if $vue == 1}} selected="selected"{{/if}}>Ne pas afficher les validés</option>
        </select>
        {{$date|date_format:$conf.longdate}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
</table>

<ul id="tabs-edit-sorties" class="control_tabs">
  {{foreach from=$sorties item=_sorties key=type}}
  <li>
    <button class="print notext" style="float:right;" onclick="$('sorties-{{$type}}').print()">{{tr}}Print{{/tr}}</button>
    <a href="#sorties-{{$type}}">Sorties {{tr}}CSejour.type.{{$type}}{{/tr}} prévues ({{$_sorties|@count}})</a>
  </li>
  {{/foreach}}
  <li>
    <button class="print notext" style="float:right;" onclick="$('deplacements').print()">{{tr}}Print{{/tr}}</button>
    <a href="#deplacements">Déplacements prévus ({{$deplacements|@count}})</a>
  </li>
</ul>

<hr class="control_tabs" />

{{assign var=url value="?m=$m&tab=$tab"}}

{{foreach from=$sorties item=_sorties key=type}}
<div id="sorties-{{$type}}" style="display: none;">
  <table class="tbl">
    <tr>
      <th class="not-printable">Sortie</th>
      <th>{{mb_colonne class="CAffectation" field="_patient"   order_col=$order_col order_way=$order_way url=$url}}</th>
      <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way url=$url}}</th>
      <th>{{mb_colonne class="CAffectation" field="_chambre"   order_col=$order_col order_way=$order_way url=$url}}</th>
      <th>{{mb_colonne class="CAffectation" field="sortie"     order_col=$order_col order_way=$order_way url=$url}}</th>
    </tr>
    {{foreach from=$_sorties item=_sortie}}
    <tr>
      <td class="not-printable">
        <form name="Sortie-{{$_sortie->_guid}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          {{mb_key object=$_sortie}}
          {{if $_sortie->confirme}}
            <input type="hidden" name="confirme" value="0" />
            <button type="submit" class="cancel">
              Annuler
            </button>
          {{else}}
            <input type="hidden" name="confirme" value="1" />
            <button type="submit" class="tick">
              Autoriser
            </button>
          {{/if}}
        </form>
      </td>
      
      <td class="text {{if $_sortie->confirme}} arretee {{/if}}">
       {{assign var=sejour value=$_sortie->_ref_sejour}}
       {{if $canPlanningOp->read}}
       <a class="action" style="float: right"  title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
         <img src="images/icons/planning.png" alt="modifier" />
       </a>
       {{/if}}
        {{assign var=patient value=$sejour->_ref_patient}}
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient}}</strong>
      </td>
      <td class="text">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
      </td>
      <td class="text">
        {{$_sortie->_ref_lit}}
      </td>
      <td>
        {{if $_sortie->confirme}}
          {{$_sortie->sortie|date_format:$conf.time}}
        {{else}}
          {{assign var=sejour_guid value=$sejour->_guid}}
          <form name="editSortiePrevue-{{$sejour_guid}}" method="post" action="?m=dPhospi">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="dosql" value="do_sejour_aed" />
            <input type="hidden" name="del" value="0" />
            {{mb_key object=$sejour}}
            {{mb_field object=$sejour field=entree_prevue hidden=true}}
            {{mb_field object=$sejour field=sortie_prevue register=true form="editSortiePrevue-$sejour_guid" onchange="this.form.submit()"}}
          </form>
        {{/if}}
      </td>
    </tr>
    {{foreachelse}}
    <tr><td colspan="5" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
    {{/foreach}}
  </table>
</div>
{{/foreach}}

<div id="deplacements" style="display: none;">
  <table class="tbl">
    <tr>
      <th class="not-printable">Déplacement</th>
      {{assign var=url value="?m=$m&tab=$tab"}}
      <th>{{mb_colonne class="CAffectation" field="_patient"   order_col=$order_col order_way=$order_way url=$url}}</th>
      <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way url=$url}}</th>
      <th>{{mb_colonne class="CAffectation" field="_chambre"   order_col=$order_col order_way=$order_way url=$url}}</th>
      <th>Destination</th>
      <th>{{mb_colonne class="CAffectation" field="sortie"     order_col=$order_col order_way=$order_way url=$url}}</th>
    </tr>
    {{foreach from=$deplacements item=_sortie}}
    <tr>
      <td class="not-printable">
      <form name="Edit-{{$_sortie->_guid}}" action="?m={{$m}}" method="post" onsubmit="saveSortie(getForm('Sortie-{{$_sortie->_guid}}'), this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      {{mb_key object=$_sortie}}
      {{mb_field object=$_sortie field=sortie hidden=1}}

      {{if $_sortie->effectue}}
     
      <input type="hidden" name="effectue" value="0" />
      <button type="submit" class="cancel">
      Annuler
      </button>
      {{else}}
      <input type="hidden" name="effectue" value="1" />
      <button type="submit" class="tick">
      Effectuer
      </button>
      {{/if}}
      </form>
      </td>
      {{if $_sortie->effectue}}
      <td class="text" class="arretee">
      {{else}}
      <td class="text">
      {{/if}}
      {{assign var=sejour value=$_sortie->_ref_sejour}}
      {{assign var=patient value=$sejour->_ref_patient}}
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient}}</strong>
      </td>
      <td class="text">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
      </td>
      <td class="text">
        {{$_sortie->_ref_lit}}
      </td>
      <td class="text">
        {{$_sortie->_ref_next->_ref_lit}}
      </td>
      <td>
        {{$_sortie->sortie|date_format:$conf.time}}
      </td>
    </tr>
    {{foreachelse}}
    <tr><td colspan="6" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
    {{/foreach}}
  </table>
</div>