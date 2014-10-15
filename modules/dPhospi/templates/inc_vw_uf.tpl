<script>
  refreshUM = function(form) {
      var url = new Url('dPhospi',  'ajax_refresh_um');
      url.addParam("um_id", $V(form.elements.type_autorisation_um_id));
      url.addParam("uf_id", $V(form.elements.uf_id));
      url.requestUpdate('um_mode_hospi');
  }
</script>

<!-- Formulaire d'une unité fonctionnelle -->
<form name="Edit-CUniteFonctionnelle" action="" method="post" onsubmit="return submit_Ajax(this, 'UF')">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="dosql" value="do_uf_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$uf}}

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$uf}}
    <tr>
      <th>{{mb_label object=$uf field=group_id}}</th>
      <td>{{mb_field object=$uf field=group_id options=$etablissements}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$uf field=type}}</th>
      <td>{{mb_field object=$uf field=type typeEnum=radio}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$uf field=type_sejour}}</th>
      <td>{{mb_field object=$uf field=type_sejour emptyLabel="Choose"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$uf field=code}}</th>
      <td>{{mb_field object=$uf field=code}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$uf field=type_autorisation_um_id}}</th>
      <td>
        <select name="type_autorisation_um_id" onchange="refreshUM(this.form)" style="width:14em;">
          <option disabled selected>{{tr}}Choose{{/tr}}</option>
          {{foreach from=$ums item=_um}}
            <option value="{{$_um->_id}}" {{if $uf->type_autorisation_um_id == $_um->_id}}selected{{/if}}>{{$_um}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$uf field=type_autorisation_mode_hospitalisation}}</th>
      <td id="um_mode_hospi">
        {{mb_include module=dPhospi template=inc_vw_um_mode_hospit um=$uf->_ref_um}}
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$uf field=nb_lits_um}}</th>
      <td>{{mb_field object=$uf field=nb_lits_um}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$uf field=libelle}}</th>
      <td>{{mb_field object=$uf field=libelle}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$uf field=date_debut}}</th>
      <td>{{mb_field object=$uf field=date_debut form="Edit-CUniteFonctionnelle" register=true}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$uf field=date_fin}}</th>
      <td>{{mb_field object=$uf field=date_fin form="Edit-CUniteFonctionnelle" register=true}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$uf field=description}}</th>
      <td>{{mb_field object=$uf field=description}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        {{if $uf->_id}}
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'lUF',objName: $V(this.form.libelle), ajax: 1})">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>