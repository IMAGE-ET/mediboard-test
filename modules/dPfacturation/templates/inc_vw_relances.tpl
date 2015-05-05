<fieldset>
  <legend>{{tr}}CRelance{{/tr}}</legend>
  {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
    <div id="check_bill_relance" style="display:none;"></div>
  {{/if}}
  <table class="main tbl">
    <tr>
      <th>{{mb_label object=$facture->_ref_last_relance field=numero}}</th>
      <th>{{mb_label object=$facture->_ref_last_relance field=date}}</th>
      <th>{{mb_label object=$facture->_ref_last_relance field=_montant}}</th>
      <th>{{mb_label object=$facture->_ref_last_relance field=etat}}</th>
      <th>{{mb_label object=$facture->_ref_last_relance field=statut}}</th>
      <th class="narrow">Action</th>
    </tr>
    {{foreach from=$facture->_ref_relances item=relance}}
      <tr>
        <td>{{mb_value object=$relance field=numero}}</td>
        <td>{{mb_value object=$relance field=date}}</td>
        <td>{{mb_value object=$relance field=_montant}}</td>
        <td>{{mb_value object=$relance field=etat}}</td>
        <td>
          {{mb_value object=$relance field=statut}}
          {{if $relance->statut == "poursuite"}}({{mb_value object=$relance field=poursuite}}){{/if}}
        </td>
        <td>
          <button type="button" class="pdf notext" onclick="Relance.printRelance('{{$facture->_class}}', '{{$facture->_id}}', 'relance', '{{$relance->_id}}');">Pdf</button>
          <button type="button" class="edit notext" onclick="Relance.modify('{{$relance->_id}}');"> </button>
          {{if $facture->_ref_last_relance->_id == $relance->_id && $relance->etat != "regle"}}
            <form name="{{$relance->_guid}}" method="post" action="" onsubmit="return Relance.create(this);">
              {{mb_class object=$relance}}
              {{mb_key   object=$relance}}
              <input type="hidden" name="del" value="1"/>
              <input type="hidden" name="object_id" value="{{$relance->object_id}}"/>
              <input type="hidden" name="object_class" value="{{$relance->object_class}}"/>
              <button type="submit" class="trash notext"></button>
            </form>
          {{/if}}
          {{if $relance->facture != 1 && $facture->_ref_last_relance->_id == $relance->_id}}
            <form name="XML_{{$relance->_guid}}" method="post" action="" onsubmit="return false;">
              {{mb_class object=$relance}}
              {{mb_key   object=$relance}}
              <input type="hidden" name="del" value="1"/>
              <input type="hidden" name="object_id" value="{{$facture->_id}}"/>
              <input type="hidden" name="object_class" value="{{$facture->_class}}"/>
              <input type="hidden" name="prat_id" value="{{$facture->praticien_id}}"/>
              <button type="button" class="send notext" onclick="Relance.checkRelance(this.form);">Générer le XML de relance</button>
            </form>
          {{/if}}
        </td>
      </tr>
    {{/foreach}}
  </table>
</fieldset>