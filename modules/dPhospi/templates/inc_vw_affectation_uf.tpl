{{mb_script module=hospi script=affectation_uf}}

<form name="affect_uf" action="?m={{$m}}" id="affecter_uf" method="post" onsubmit="return AffectationUf.onSubmit(this);" style="text-align:left;">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_affectation_aed" />
  {{if $callback}}
    <input type="hidden" name="callback" value="{{$callback}}" />
  {{/if}}
  <input type="hidden" name="affectation_id" value="{{$affectation->_id}}" />

  <fieldset>
    <legend>
      <img src="style/mediboard/images/buttons/search.png" onclick="$(this).up('fieldset').down('tbody').toggle();"/>
      {{mb_label class=CAffectation field=uf_hebergement_id}}
    </legend>
    <table class="form">
      <tbody style="display: none;">
      {{mb_include template=inc_vw_ufs_object object=$sejour  ufs=$uf_sejour_hebergement}}
      {{mb_include template=inc_vw_ufs_object object=$service ufs=$ufs_service}}
      {{mb_include template=inc_vw_ufs_object object=$chambre ufs=$ufs_chambre}}
      {{mb_include template=inc_vw_ufs_object object=$lit     ufs=$ufs_lit    }}
      </tbody>

      {{mb_include template=inc_options_ufs_context context=hebergement ufs=$ufs_hebergement}}
    </table>
  </fieldset>
    
  <fieldset>
    <legend>
      <img src="style/mediboard/images/buttons/search.png" onclick="$(this).up('fieldset').down('tbody').toggle();"/>
      {{mb_label class=CAffectation field=uf_soins_id}}
    </legend>
    <table class="form">
      <tbody style="display: none;">
      {{mb_include template=inc_vw_ufs_object object=$sejour  ufs=$uf_sejour_soins}}
      {{mb_include template=inc_vw_ufs_object object=$service ufs=$ufs_service}}
      </tbody>

      {{mb_include template=inc_options_ufs_context context=soins ufs=$ufs_soins}}
    </table>
  </fieldset>

  <fieldset>
    <legend>
      <img src="style/mediboard/images/buttons/search.png" onclick="$(this).up('fieldset').down('tbody').toggle();"/>
      {{mb_label class=CAffectation field=uf_medicale_id}}
    </legend>
    <table class="form">
      <tbody style="display: none;">
      {{mb_include template=inc_vw_ufs_object object=$sejour    ufs=$uf_sejour_medicale}}
      {{mb_include template=inc_vw_ufs_object object=$function  ufs=$ufs_function }}
      {{mb_include template=inc_vw_ufs_object object=$praticien      ufs=$ufs_praticien_sejour name="Praticien séjour"}}
      {{mb_include template=inc_vw_ufs_object object=$prat_placement ufs=$ufs_prat_placement   name="Praticien placement"}}
      </tbody>
      <tr>
        <th>{{tr}}CAffectation-praticien_id{{/tr}}</th>
        <td colspan="2">
          <select name="praticien_id" style="width: 15em;" onchange="AffectationUf.onSubmitRefresh(this.form, '{{$affectation->_guid}}', '{{$lit->_guid}}', '{{$see_validate}}')">
            <option value="" {{if !$praticien->_id}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
            {{mb_include module=mediusers template=inc_options_mediuser selected=$prat_placement->_id list=$praticiens}}
          </select>
         </td>
      </tr>
      {{mb_include template=inc_options_ufs_context context=medicale ufs=$ufs_medicale}}
    </table>
  </fieldset>
  {{if $see_validate}}
    <button class="submit" type="submit">{{tr}}Validate{{/tr}}</button>
  {{/if}}
</form>