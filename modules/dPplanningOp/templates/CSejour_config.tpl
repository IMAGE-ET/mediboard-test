<script type="text/javascript">
  Main.add(function() {
    var form = getForm("editConfigSejour");
    form["dPplanningOp[CSejour][max_cancel_time]"    ].addSpinner({min:0, max:24});
    form["dPplanningOp[CSejour][hours_sejour_proche]"].addSpinner({min:0, max:96});
    form["dPplanningOp[CSejour][anonymous_naissance]"].type = "hidden";
    Calendar.regField(form["dPplanningOp[CSejour][anonymous_naissance]"]);
  });
  toggleSpecsPat = function(statut) {
    var area_pat = $("specs_anonymous_pat");
    if (statut == 1) {
      area_pat.show();
    }
    else {
      area_pat.hide();
    }
  }
</script>

<form name="editConfigSejour" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  {{assign var="class" value="CSejour"}}
  <table class="form">
    <tr>
      <th class="title" colspan="2">Général</th>
    </tr>

    {{mb_include module=system template=inc_config_enum var=patient_id values=$patient_ids}}
    {{mb_include module=system template=inc_config_enum var=check_collisions values="no|date|datetime"}}
    {{mb_include module=system template=inc_config_bool var=entree_modifiee}}

    {{mb_include module=system template=inc_config_enum var=heure_deb  values=$hours skip_locales=true}}
    {{mb_include module=system template=inc_config_enum var=heure_fin  values=$hours skip_locales=true}}

    {{mb_include module=system template=inc_config_enum var=min_intervalle  values=$intervals skip_locales=true}}

    {{mb_include module=system template=inc_config_bool var=blocage_occupation}}
    {{mb_include module=system template=inc_config_bool var=service_id_notNull}}
    {{mb_include module=system template=inc_config_bool var=consult_accomp}}
    {{mb_include module=system template=inc_config_bool var=delete_only_admin}}
    {{mb_include module=system template=inc_config_str  var=max_cancel_time     size=2 suffix=h}}
    {{mb_include module=system template=inc_config_str  var=hours_sejour_proche size=2 suffix=h}}
    {{mb_include module=system template=inc_config_bool var=fix_doc_edit}}
    {{mb_include module=system template=inc_config_bool var=create_anonymous_pat onchange="toggleSpecsPat(this.value)"}}
    <tbody id="specs_anonymous_pat" {{if !$conf.dPplanningOp.CSejour.create_anonymous_pat}}style="display: none;"{{/if}}>
      {{mb_include module=system template=inc_config_enum var=anonymous_sexe values=m|f}}
      {{mb_include module=system template=inc_config_str var=anonymous_naissance}}
    </tbody>
    {{mb_include module=system template=inc_config_bool var=use_recuse}}
    {{mb_include module=system template=inc_config_enum var=systeme_isolement values=standard|expert}}

    <tr>
      <th class="title" colspan="2">
        Modes de traitement / entrée / sortie
      </th>
    </tr>
    <tr>
      <td colspan="2">
        <script type="text/javascript">
          Main.add(function(){
            Control.Tabs.create("custom-cpi-mode-entre-sortie");
          });
        </script>
        <ul class="control_tabs" id="custom-cpi-mode-entre-sortie">
          <li><a href="#tab-CChargePriceIndicator">{{tr}}CChargePriceIndicator{{/tr}}</a></li>
          <li><a href="#tab-CModeEntreeSejour">{{tr}}CModeEntreeSejour{{/tr}}</a></li>
          <li><a href="#tab-CModeSortieSejour">{{tr}}CModeSortieSejour{{/tr}}</a></li>
        </ul>

        <div id="tab-CChargePriceIndicator" style="display: none;">
          <table class="main form">
            {{mb_include module=system template=inc_config_bool class=CSejour var=use_charge_price_indicator}}
            {{mb_include module=system template=inc_config_bool class=CSejour var=show_only_charge_price_indicator}}
          </table>
          {{mb_include template=CChargePriceIndicator_config}}
        </div>

        <div id="tab-CModeEntreeSejour" style="display: none;">
          <table class="main form">
            {{mb_include module=system template=inc_config_bool class=CSejour var=use_custom_mode_entree}}
          </table>
          {{mb_include template=CModeEntreeSejour_config list_modes=$list_modes_entree}}
        </div>

        <div id="tab-CModeSortieSejour" style="display: none;">
          <table class="main form">
            {{mb_include module=system template=inc_config_bool class=CSejour var=use_custom_mode_sortie}}
          </table>
          {{mb_include template=CModeSortieSejour_config list_modes=$list_modes_sortie}}
        </div>
      </td>
    </tr>

    <tr>
      <th class="title" colspan="2">{{tr}}CRegleSectorisation{{/tr}}</th>
    </tr>

    {{mb_include module=system template=inc_config_bool class=CRegleSectorisation var=use_sectorisation }}

    <tr>
      <th class="title" colspan="2">Affichage des champs</th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=accident}}
    {{mb_include module=system template=inc_config_bool var=assurances}}
    {{mb_include module=system template=inc_config_bool var=show_type_pec}}
    {{mb_include module=system template=inc_config_bool var=show_discipline_tarifaire }}
    {{mb_include module=system template=inc_config_bool var=fiche_rques_sej}}
    {{mb_include module=system template=inc_config_bool var=fiche_conval}}
    {{mb_include module=system template=inc_config_bool var=show_cmu_ald}}
    {{mb_include module=system template=inc_config_bool var=show_days_duree}}
    {{mb_include module=system template=inc_config_bool var=show_isolement}}
    {{mb_include module=system template=inc_config_bool var=show_chambre_part}}
    {{mb_include module=system template=inc_config_bool var=show_facturable}}
    {{mb_include module=system template=inc_config_bool var=show_atnc}}

    <tr>
      <th class="title" colspan="2">Heure par defaut du séjour</th>
    </tr>

    <tr>
      <th class="category" colspan="2">Heure d'entree</th>
    </tr>
    {{mb_include module=system template=inc_config_enum var=heure_entree_veille values=$hours skip_locales=true}}
    {{mb_include module=system template=inc_config_enum var=heure_entree_jour   values=$hours skip_locales=true}}
    <tr>
      <th class="category" colspan="2">Heure de sortie</th>
    </tr>
    {{mb_include module=system template=inc_config_enum var=heure_sortie_ambu  values=$hours skip_locales=true}}
    {{mb_include module=system template=inc_config_enum var=heure_sortie_autre values=$hours skip_locales=true}}

    {{assign var="var" value="sortie_prevue"}}
    <tr>
      <th class="title" colspan="2">{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}</th>
    </tr>

    {{foreach from=$conf.$m.$class.$var key=type item=value}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$type}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$type}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$type}}{{/tr}}
        </label>
      </th>
      <td>
        <select class="num" name="{{$m}}[{{$class}}][{{$var}}][{{$type}}]">
          <option value="04" {{if "04" == $conf.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 04h</option>
          <option value="24" {{if "24" == $conf.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 24h</option>
          <option value="48" {{if "48" == $conf.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 48h</option>
          <option value="72" {{if "72" == $conf.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 72h</option>
        </select>
      </td>
    </tr>
    {{/foreach}}

    <tr>
      <td class="button" colspan="100">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>