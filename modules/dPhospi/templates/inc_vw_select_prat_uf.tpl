<select name="praticien_id" style="width: 15em;"
        onchange="AffectationUf.onSubmitRefresh(this.form, '{{$affectation->_guid}}', '{{$lit->_guid}}', '{{$see_validate}}')">
  <option value="" {{if !$praticien->_id}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
  {{mb_include module=mediusers template=inc_options_mediuser selected=$prat_placement->_id list=$praticiens}}
</select>
