<script>
  viewBill = function(facture_id, facture_class) {
    var url = new Url('facturation', 'ajax_view_facture');
    url.addParam('facture_id'    , facture_id);
    url.addParam("object_class", facture_class);
    url.addParam("show_button", 0);
    url.requestModal();
    url.modalObject.observe('afterClose', function() { Control.Modal.close();gestionFacture();});
  }
  editFacture = function() {
    var url = new Url('facturation', 'vw_edit_facture');
    url.addParam('facture_class', 'CFactureEtablissement');
    url.addParam('patient_id'   , '{{$sejour->patient_id}}');
    url.addParam('object_id'    , '{{$sejour->_id}}');
    url.addParam('numero'       , '{{$sejour->_ref_factures|@count}}');
    url.requestModal();
    url.modalObject.observe('afterClose', function() { Control.Modal.close();});
  }
</script>
<table class="tbl">
  <tr>
    <th class="title" colspan="6">Gestion des factures {{$sejour->_view}}</th>
  </tr>
  <tr>
    {{foreach from=$sejour->_ref_factures item=facture}}
      <td colspan="2">
        <table style="height: 100%;">
          <tr>
            <th class="category" colspan="2">
              Facture: {{$facture->_view}}
            </th>
          </tr>
          <tr>
            <td>{{mb_title object=$facture field=type_facture}}</td>
            <td>{{mb_value object=$facture field=type_facture}}</td>
          </tr>
          <tr>
            <td>{{mb_title object=$facture field=cession_creance}}</td>
            <td>{{mb_value object=$facture field=cession_creance}}</td>
          </tr>
          <tr>
            <td>{{mb_title object=$facture field=dialyse}}</td>
            <td>{{mb_value object=$facture field=dialyse}}</td>
          </tr>
          <tr>
            <td>{{mb_title object=$facture field=statut_pro}}</td>
            <td>{{mb_value object=$facture field=statut_pro}}</td>
          </tr>
          <tr>
            <td>Assurance</td>
            <td>
              {{if $facture->assurance_maladie && $facture->type_facture != "esthetique"}}
                {{mb_value object=$facture->_ref_assurance_maladie field=nom}}</td>
              {{elseif $facture->type_facture != "esthetique"}}
                {{mb_value object=$facture->_ref_assurance_accident field=nom}}</td>
              {{/if}}
          </tr>
          <tr>
            <td colspan="2" class="button">
              <button type="button" class="search" onclick="viewBill('{{$facture->_id}}', '{{$facture->_class}}');">Voir</button>
            </td>
          </tr>
        </table>
      </td>
    {{/foreach}}
    {{if $sejour->_ref_factures|@count <=2}}
      <td colspan="2">
        <button type="button" class="add" onclick="editFacture();">Ajouter une facture</button>
      </td>
    {{/if}}
  </tr>
</table>