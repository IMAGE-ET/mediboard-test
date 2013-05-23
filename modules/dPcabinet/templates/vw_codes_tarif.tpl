{{mb_script module=cabinet script=tarif}}

<script>
Main.add(function () {
  var form = getForm("modifTarif");
  {{if "tarmed"|module_active && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
    // Autocomplete Tarmed
    var url = new Url("tarmed", "ajax_do_tarmed_autocomplete");
    url.autoComplete(form.code_tarmed, null, {
      minChars: 0,
      dropdown: true,
      select: "newcode",
      updateElement: function(selected) {
        $V(form.code_tarmed, selected.down(".newcode").getText(), false);
      }
    });
    // Autocomplete Caisse
    var url2 = new Url("tarmed", "ajax_do_prestation_autocomplete");
      url2.autoComplete(form.code_caisse, null, {
      minChars: 0,
      dropdown: true,
      select: "newcode",
      updateElement: function(selected) {
        $V(form.code_caisse, selected.down(".newcode").getText(), false);
      }
    });
  {{/if}}
});
</script>

<form name="modifTarif" action="?m=dPcabinet&tab=vw_edit_tarifs" method="post">
  {{mb_key    object=$tarif}}
  {{mb_class  object=$tarif}}
  <input type="hidden" name="_add_code"   value="0">
  <input type="hidden" name="_dell_code"  value="0">
  <input type="hidden" name="_code"       value="0">
  <input type="hidden" name="_quantite"   value="0">
  <input type="hidden" name="_type_code"  value="">
  <input type="hidden" name="_code_ref"  value="">
  <table class="tbl">
  {{mb_include module=system template=inc_form_table_header object=$tarif colspan="5"}}
    <tr>
      <td></td>
      <th>Code</th>
      <th>Quantité</th>
      <th>Code Ref</th>
      <th>Action</th>
    </tr>
    {{foreach from=$tab item=code_libelle key=nom}}
      <tr>
        <th class="narrow">Code {{$nom}}</th>
        <td>
          <input type="text" name="code_{{$nom}}" value="" style="width:250px;"/>
        </td>
        <td>
          <input type="text" name="quantite_{{$nom}}" value="1" style="width:20px;"/>
          <script>
            Main.add(function () {
              getForm("modifTarif")["quantite_{{$nom}}"].addSpinner({min:0, step:1});
            });
          </script>
        </td>
        <td>
          {{if $nom == "tarmed"}}
            {{assign var=nom_code value=_codes_$nom}}
            <select name="code_ref_{{$nom}}">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{foreach from=$tarif->$nom_code item=code}}
                <option value="{{$code->code}}">
                  {{$code->code}}
                </option>
              {{/foreach}}
            </select>
          {{else}}
            <input type="hidden" name="code_ref_{{$nom}}" value=""/>
          {{/if}}
        </td>
        <td class="button">
          <button onclick="Code.addCode(this.form, this.form.code_{{$nom}}.value, this.form.quantite_{{$nom}}.value, '{{$nom}}', this.form.code_ref_{{$nom}}.value)"
                  class="add notext" type="button"></button>
        </td>
      </tr>
      {{assign var=nom_code value=_codes_$nom}}
      {{foreach from=$tarif->$nom_code item=code}}
        {{assign var=code_acte value=$code->code}}
        <tr>
          <td></td>
          <td>
            <strong>{{$code_acte}}</strong>
            {{$code->$code_libelle->libelle|truncate:50:"..."}}
          </td>
          <td>{{$code->quantite}}</td>
          <td>
            {{if $nom == "tarmed"}}
              {{$code->code_ref}}
            {{/if}}
          </td>
          <td class="button"><button class="trash notext" type="button" onclick="Code.dellCode(this.form, '{{$code_acte}}', '{{$nom}}')"></button></td>
        </tr>
      {{/foreach}}
    {{/foreach}}
    <tr>
      <td class="button" colspan="5">
        <button class="close" onclick="Code.modal.close();">{{tr}}Close{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>