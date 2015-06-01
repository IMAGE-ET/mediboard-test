{{if $rpu->_id && $rpu->_ref_cts_degre|@count}}
  {{assign var=constants_list value="CConstantesMedicales"|static:"list_constantes"}}

  <table class="form">
    <tr>
      <th class="title" colspan="3">Paramètres vitaux</th>
    </tr>
    <tr>
      <th class="category" style="width: 33%;">Degré 1</th>
      <th class="category" style="width: 33%;">Degré 2</th>
      <th class="category" style="width: 33%;">Degré 3 / Degré 4</th>
    </tr>
    <tr>
      {{foreach from=$rpu->_ref_cts_degre key=degre item=_ctes}}
        <td>
          <table style="width: 100%;">
          {{foreach from=$_ctes item=_cte key=key_cte}}
            <tr>
              <td>
                <strong>
                  <label for="{{$_cte}}" title="{{tr}}CConstantesMedicales-{{$_cte}}-desc{{/tr}}">
                  {{tr}}CConstantesMedicales-{{$_cte}}-court{{/tr}}
                  </label>
                </strong>
                {{if isset($rpu->_ref_latest_constantes.0->$_cte|smarty:nodefaults)}}
                  {{assign var=_params value=$constants_list.$_cte}}
                  {{if $_params.unit}}
                    <small class="opacity-50">
                      ({{$_params.unit}})
                    </small>
                  {{/if}}
                {{/if}}
                <span style="float: right;text-align:right;">
                  {{if isset($rpu->_ref_latest_constantes.0->$_cte|smarty:nodefaults)}}
                    {{$rpu->_ref_latest_constantes.0->$_cte}}
                    {{if $_cte == "peak_flow"}}
                      <br/><small class="opacity-50" style="float: right;">(Prédit {{$key_cte}})</small>
                    {{/if}}
                  {{elseif $_cte == "index_de_choc"}}
                    {{if $degre == 2}}Pouls > TAS{{else}}Pouls &le; TAS{{/if}}
                  {{elseif $_cte == "liquide" || $_cte == "proteinurie"}}
                    {{mb_value object=$rpu->_ref_echelle_tri field=$_cte}}
                  {{elseif $_cte == "pupilles"}}
                    {{if $degre == 2}}Anormales{{else}}Normales{{/if}}
                  {{/if}}
                </span>
              </td>
            </tr>
          {{/foreach}}
          </table>
        </td>
      {{/foreach}}
    </tr>
    {{if isset($rpu->_ref_latest_constantes.0->comment|smarty:nodefaults)}}
      <tr>
        <td colspan="6" class="compact">
          Commentaire: {{$rpu->_ref_latest_constantes.0->comment}}
        </td>
      </tr>
    {{/if}}
    {{if !$rpu->_ref_cts_degre[1]|@count && !$rpu->_ref_cts_degre[2]|@count && !$rpu->_ref_cts_degre[3]|@count}}
      <tr>
        <td colspan="3" class="empty">Les informations renseignées ne permettent pas de déterminer le degré de l'urgence</td>
      </tr>
    {{/if}}
  </table>
{{/if}}