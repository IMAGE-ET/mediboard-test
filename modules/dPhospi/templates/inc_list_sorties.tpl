<script type="text/javascript">
  $("count_{{$type}}").update("{{$update_count}}");
</script>

{{if $type == "deplacements"}}
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
      <th>{{mb_colonne class="CAffectation" field="_patient"   order_col=$order_col order_way=$order_way function=refreshList}}</th>
      <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way function=refreshList}}</th>
      <th>{{mb_colonne class="CAffectation" field="_chambre"   order_col=$order_col order_way=$order_way function=refreshList}}</th>
      <th>Destination</th>
      <th>{{mb_colonne class="CAffectation" field="sortie"     order_col=$order_col order_way=$order_way function=refreshList}}</th>
    </tr>
    {{foreach from=$deplacements item=_sortie}}
    <tr>
      <td class="not-printable">
      <form name="Edit-{{$_sortie->_guid}}" action="?m={{$m}}" method="post"
        onsubmit="saveSortie(getForm('Sortie-{{$_sortie->_guid}}'), this); return onSubmitFormAjax(this, { onComplete: refreshList })">
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
{{else}}
  <table class="tbl">
    <tr class="only-printable">
      <th class="title" colspan="100">
        Sorties {{tr}}CSejour.type.{{$type}}{{/tr}} prévues (<span id="count_{{$type}}">{{$sorties|@count}}</span>)
        &mdash; {{$date|date_format:$conf.longdate}}
      </th>
    </tr>
    <tr>
      <th class="not-printable">
        <button class="print notext" style="float:left;" onclick="$('{{$type}}').print()">{{tr}}Print{{/tr}}</button>
        Sortie
      </th>
      <th>{{mb_colonne class="CAffectation" field="_patient"   order_col=$order_col order_way=$order_way function=refreshList}}</th>
      <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way function=refreshList}}</th>
      <th>{{mb_colonne class="CAffectation" field="_chambre"   order_col=$order_col order_way=$order_way function=refreshList}}</th>
      <th>{{mb_colonne class="CAffectation" field="sortie"     order_col=$order_col order_way=$order_way function=refreshList}}</th>
    </tr>
    {{foreach from=$sorties item=_sortie}}
    <tr>
      <td class="not-printable">
        <form name="Sortie-{{$_sortie->_guid}}" action="?m={{$m}}" method="post"
          onsubmit="return onSubmitFormAjax(this, {onComplete: refreshList})">
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
          <form name="editSortiePrevue-{{$sejour_guid}}" method="post" action="?"
            onsubmit="return onSubmitFormAjax(this, { onComplete: function() { refreshList(); } })">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="dosql" value="do_sejour_aed" />
            <input type="hidden" name="del" value="0" />
            {{mb_key object=$sejour}}
            {{mb_field object=$sejour field=entree_prevue hidden=true}}
            <button class="add" type="button" onclick="addDays(this, 1)">1J</button>
            {{mb_field object=$sejour field=sortie_prevue register=true form="editSortiePrevue-$sejour_guid" onchange="this.form.onsubmit()"}}
          </form>
        {{/if}}
      </td>
    </tr>
    {{foreachelse}}
    <tr><td colspan="5" class="empty">{{tr}}CSejour.none{{/tr}}</td></tr>
    {{/foreach}}
  </table>
{{/if}}