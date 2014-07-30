<table class="form">
  <tr>
    <th>{{mb_label object=$sejour field="exec_tarif"}}</th>
    <td>
      <!-- Formulaire date d'éxécution de tarif -->
      <form name="editExecTarif" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
        {{mb_key object=$sejour}}
        {{mb_class object=$sejour}}
        {{mb_field object=$sejour field="exec_tarif" form="editExecTarif" register=true onchange="this.form.onsubmit();"}}
      </form>
    </td>
    <th><label for="_tarif_id">Tarif</label></th>
    <td>
      <form name="selectTarif" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: loadActes.curry({{$sejour->_id}}, {{$sejour->praticien_id}})});">
        {{mb_class object=$sejour}}
        {{mb_key   object=$sejour}}
        <input type="hidden" name="_bind_tarif" value="1"/>
        <input type="hidden" name="_delete_actes" value="0"/>
        <input type="hidden" name="_datetime" value="{{$sejour->_datetime}}">
        <input type="hidden" name="entree_prevue" value="{{$sejour->entree_prevue}}">
        <input type="hidden" name="sortie_prevue" value="{{$sejour->sortie_prevue}}">

          <select name="_tarif_id" class="str" onchange="this.form.onsubmit();">
          <option value="" selected="selected">&mdash; {{tr}}Choose{{/tr}}</option>
          {{if $tarifs.user|@count}}
            <optgroup label="Tarifs praticien">
              {{foreach from=$tarifs.user item=_tarif}}
                <option value="{{$_tarif->_id}}" {{if $_tarif->_precode_ready}}class="checked"{{/if}}>{{$_tarif}}</option>
              {{/foreach}}
            </optgroup>
          {{/if}}
          {{if $tarifs.func|@count}}
            <optgroup label="Tarifs cabinet">
              {{foreach from=$tarifs.func item=_tarif}}
                <option value="{{$_tarif->_id}}" {{if $_tarif->_precode_ready}}class="checked"{{/if}}>{{$_tarif}}</option>
              {{/foreach}}
            </optgroup>
          {{/if}}
          {{if $conf.dPcabinet.Tarifs.show_tarifs_etab && $tarifs.group|@count}}
            <optgroup label="Tarifs établissement">
              {{foreach from=$tarifs.group item=_tarif}}
                <option value="{{$_tarif->_id}}" {{if $_tarif->_precode_ready}}class="checked"{{/if}}>{{$_tarif}}</option>
              {{/foreach}}
            </optgroup>
          {{/if}}
        </select>
      </form>
    </td>
  </tr>
</table>