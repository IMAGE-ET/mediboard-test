{{mb_script module=hospi script=affectation_uf}}

<form name="affect_uf" action="?m={{$m}}" id="affecter_uf" method="post" onsubmit="return AffectationUf.onSubmit(this);" style="text-align:left;">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_affectation_aed" />
  {{if $callback}}
    <input type="hidden" name="callback" value="{{$callback}}" />
  {{/if}}
  <input type="hidden" name="affectation_id" value="{{$affectation->_id}}" />

  <table class="main">
    <tr>
      <td>
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
            <td colspan="2" id="select_prat_uf_med">
              {{mb_include template=inc_vw_select_prat_uf}}
             </td>
          </tr>
          {{assign var=affectation_guid value=$affectation->_guid}}
          {{assign var=lit_guid         value=$lit->_guid}}
          {{mb_include template=inc_options_ufs_context context=medicale ufs=$ufs_medicale
              callback_uf="AffectationUf.reloadPratUfMed(this, '$affectation_guid', '$lit_guid', '$see_validate')"}}
        </table>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td class="button">
      {{if $see_validate}}
        <button class="submit" type="submit">{{tr}}Validate{{/tr}}</button>
      {{/if}}
    </td>
  </tr>
</form>