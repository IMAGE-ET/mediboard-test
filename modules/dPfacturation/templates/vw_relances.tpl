<script>
function addRelances(facture_class, type_relance){
  var form = getForm("printFrm");
  var relances = getForm("add-relances");
  relances.type_relance.value   = type_relance;
  relances.facture_class.value  = facture_class;
  relances._date_min.value      = form._date_min.value;
  relances._date_max.value      = form._date_max.value;
  relances.chir.value           = form.chir.value;
  relances.submit();
}
</script>

{{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
  <div id="check_bill_relance" style="display:none;"></div>
{{/if}}

<form name="printRelance" action="?" method="get" onSubmit="return checkRapport()">
  <input type="hidden" name="a" value="" />
  <input type="hidden" name="dialog" value="1" />
  <table class="form">
    {{if $conf.dPfacturation.CFactureCabinet.view_bill}}
      <tr>
        <th class="category" colspan="5">Relance de Cabinet</th>
      </tr>
      <tr>
        <td class="button" rowspan="2">
          <label for="typerelance_CFactureCabinet">Type de relance</label>
          <select name="typerelance_CFactureCabinet">
            <option value="1">Première Relance</option>
            <option value="2">Seconde Relance</option>
            <option value="3">Troisième Relance</option>
          </select>
        </td>
        <td class="button">
          <button type="button" class="search" onclick="ListeFacture.load('CFactureCabinet', this.form.typerelance_CFactureCabinet.value);">Voir les factures à relancer</button>
        </td>
        <td class="button">
          <label for="typereglement_CFactureCabinet">Règlement</label>
          <select name="typereglement_CFactureCabinet">
          <option value="0">&mdash; Tous</option>
          <option value="1">Emises</option>
          <option value="2">Réglées</option>
          <option value="3">Renouvelées</option>
          </select>
        </td>
        {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
          <td rowspan="2" class="button">
            <button type="button" class="send" onclick="Relance.checkBills('CFactureCabinet');">Générer le dossier de relance</button>
          </td>
        {{/if}}
      </tr>
      <tr>
        <td class="button">
          <button type="button" class="add" onclick="addRelances('CFactureCabinet', this.form.typerelance_CFactureCabinet.value);">Générer les relances</button>
        </td>
        <td class="button">
          <button type="button" class="search" onclick="ListeFacture.view('CFactureCabinet', this.form.typerelance_CFactureCabinet.value, this.form.typereglement_CFactureCabinet.value);">Voir les relances émises</button>
        </td>
      </tr>
    {{/if}}
    {{if $conf.dPfacturation.CFactureEtablissement.view_bill}}
      <tr>
        <th class="category" colspan="4">Relance d'établissement</th>
      </tr>
      <tr>
        <td class="button" rowspan="2">
          <label for="typerelance_CFactureEtablissement">Type de relance</label>
          <select name="typerelance_CFactureEtablissement">
            <option value="1">Première Relance</option>
            <option value="2">Seconde Relance</option>
            <option value="3">Troisième Relance</option>
          </select>
        </td>
        <td class="button">
          <button type="button" class="search" onclick="ListeFacture.load('CFactureEtablissement', this.form.typerelance_CFactureEtablissement.value);">Voir les factures à relancer</button>
        </td>
        <td class="button">
          <label for="typereglement_CFactureEtablissement">Règlement</label>
          <select name="typereglement_CFactureEtablissement">
          <option value="0">&mdash; Tous</option>
          <option value="emise">Emises</option>
          <option value="regle">Réglées</option>
          <option value="renouvelle">Renouvelées</option>
          </select>
        </td>
        {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
          <td rowspan="2" class="button">
            <button type="button" class="send" onclick="Relance.checkBills('CFactureEtablissement');">Générer le dossier de relance</button>
          </td>
        {{/if}}
      </tr>
      <tr>
        <td class="button">
          <button type="button" class="add" onclick="addRelances('CFactureEtablissement', this.form.typerelance_CFactureEtablissement.value);">Générer les relances</button>
        </td>
        <td class="button">
          <button type="button" class="search" onclick="ListeFacture.view('CFactureEtablissement', this.form.typerelance_CFactureEtablissement.value, this.form.typereglement_CFactureEtablissement.value);">Voir les relances émises</button>
        </td>
      </tr>
    {{/if}}
  </table>
</form>
<form name="add-relances" action="" method="post" target="_blank">
  <input type="hidden" name="m" value="facturation" />
  <input type="hidden" name="dosql" value="do_relance_aed" />
  <input type="hidden" name="_date_min" value="" />
  <input type="hidden" name="_date_max" value="" />
  <input type="hidden" name="facture_class" value="" />
  <input type="hidden" name="type_relance" value="" />
  <input type="hidden" name="chir" value="" />
</form>