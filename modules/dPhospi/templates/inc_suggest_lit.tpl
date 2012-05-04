<table class="tbl">
  <tr>
    <th class="title" colspan="4">
      <form name="searchLit" method="get" onsubmit="return onSubmitFormAjax(this, null, this.up('div'))">
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="a" value="ajax_suggest_lit" />
        <input type="hidden" name="_link_affectation" value="{{$_link_affectation}}" />
        {{if $affectation_id}}
          <input type="hidden" name="affectation_id" value="{{$affectation_id}}" />
        {{/if}}
        {{if $sejour_id}}
          <input type="hidden" name="affectation_id" value="{{$sejour_id}}" />
        {{/if}}
        <label>
          <input type="checkbox" name="all_services" {{if $all_services}}checked="checked"{{/if}} onclick="this.form.onsubmit();"
          {{if $sejour_id}}disabled="disabled"{{/if}}/>
            Rechercher dans tous les services
        </label>
      </form>
    </th>
  </tr>
  <tr>
    <th></th>
    <th>
      Libre depuis
    </th>
    <th>{{tr}}CLit{{/tr}}</th>
    <th>Occup� apr�s</th>
  </tr>
  {{foreach from=$lits item=_lit}}
    {{assign var=lit_id value=$_lit->_id}}
    {{math equation="(x/y) * 100" x=$_lit->_dispo_depuis y=$max_entree assign=width_entree}}
    {{if $_lit->_occupe_dans == "libre"}}
      {{assign var=width_sortie value="100"}}
    {{else}}
      {{math equation="(x/y) * 100" x=$_lit->_occupe_dans y=$max_sortie assign=width_sortie}}
    {{/if}}
    <tr>
      <td>
        <button type="button" class="tick notext"
        onclick="
        {{if $_link_affectation}}
          submitLiaison('{{$lit_id}}');
        {{else}}
          {{if $affectation_id}}
            moveAffectation('{{$affectation_id}}', '{{$lit_id}}');
          {{else}}
            moveAffectation(null, '{{$lit_id}}', '{{$sejour_id}}');
          {{/if}}
        {{/if}}
        Control.Modal.close();"></button>
      </td>
      <td style="width: 30%;">
        <div style="width: {{$width_entree}}%; background: #dcd;">
          {{if $_lit->_dispo_depuis_friendly}} 
            {{$_lit->_dispo_depuis_friendly.count}} {{tr}}{{$_lit->_dispo_depuis_friendly.unit}}{{/tr}}
          {{else}}
            &mdash;
          {{/if}}
        </div>
      </td>
      <td>
        {{$_lit}}
      </td>
      <td style="width: 30%;">
        {{if isset($_lit->_occupe_dans_friendly.count|smarty:nodefaults)}}
          {{$_lit->_occupe_dans_friendly.count}} {{tr}}{{$_lit->_occupe_dans_friendly.unit}}{{/tr}}
        {{else}}
        &mdash;
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
</table>