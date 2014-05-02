{{assign var="view" value="true"}}
{{if $conf.dPfacturation.CRelance.use_relances || $conf.dPfacturation.CReglement.use_echeancier || $conf.ref_pays == 2}}
  <script>
    Main.add(Control.Tabs.create.curry('tabs-configure-{{$facture->_guid}}', true));
  </script>

  {{assign var="view" value="false"}}
  <ul id="tabs-configure-{{$facture->_guid}}" class="control_tabs">
    <li><a href="#gestion_facture-{{$facture->_guid}}">Gestion facture</a></li>
    <li><a href="#reglements_facture-{{$facture->_guid}}">Règlements</a></li>
    {{if $conf.dPfacturation.CRelance.use_relances}}
      <li><a href="#relances-{{$facture->_guid}}">Relances</a></li>
    {{/if}}
    {{if $conf.dPfacturation.CReglement.use_echeancier}}
      <li><a href="#echeances-{{$facture->_guid}}">Echéancier</a></li>
    {{/if}}
    {{* <li><a href="#debiteur">Compte Débiteur</a></li> *}}
  </ul>
  <hr class="control_tabs" />
{{/if}}

<div id="gestion_facture-{{$facture->_guid}}" {{if !$view}}style="display: none;"{{/if}}>
  <div style="display: none;">
    <form name="delete_facture" method="post" action="">
      {{mb_key object=$facture}}
      {{mb_class object=$facture}}
      <input type="hidden" name="del" value="1"/>
      <button class="cancel notext" type="submit"></button>
    </form>
  </div>
  <!-- Facture -->
  <fieldset class="hatching">
    {{if $facture && $facture->_id}}
      <legend>{{tr}}{{$facture->_class}}{{/tr}}: {{$facture}}</legend>
      <table class="main tbl">
        <tr>
          <td style="text-align:center;">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_patient->_guid}}')">
              Patient : {{$facture->_ref_patient}}
            </span>
          </td>
          <td style="text-align:center;">
            {{mb_include module=system template=inc_object_history object=$facture}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_praticien->_guid}}')">
              Praticien: {{$facture->_ref_praticien}}
            </span>
          </td>
        </tr>
      </table>
      <table class="main tbl">
        {{if $facture->annule}}
          <tr>
            <td colspan="10">
              <div class="small-warning">
                <strong>La facture est extournée.</strong>
              </div>
            </td>
          </tr>
        {{elseif $facture->cloture}}
          <tr>
            <td colspan="10">
              <div class="small-info">
                <strong>La facture est cloturée.</strong>
                Pour pouvoir ajouter des éléments, veuillez la rouvrir.
              </div>
            </td>
          </tr>
        {{/if}}

        {{mb_include module=dPfacturation template=inc_vw_facturation_tarmed}}
        {{mb_include module=dPfacturation template=inc_vw_facturation_t2a   }}
      </table>

    {{else}}
      <legend class="empty">{{tr}}CFactureCabinet.none{{/tr}}</legend>
    {{/if}}
  </fieldset>
</div>

<!-- Relances -->
{{if $conf.dPfacturation.CRelance.use_relances}}
  <div id="relances-{{$facture->_guid}}" {{if !$view}}style="display: none;"{{/if}}>
    {{if $facture->_ref_relances|@count}}
      {{mb_include module=dPfacturation template="inc_vw_relances"}}
    {{else}}
      <div class="small-info">Aucune relance n'existe pour la facture</div>
    {{/if}}
  </div>
{{/if}}

<!-- Reglements -->
<div id="reglements_facture-{{$facture->_guid}}" {{if !$view}}style="display: none;"{{/if}}>
  {{if $facture->_id && !$facture->annule && ($facture->cloture || $conf.dPfacturation.CReglement.add_pay_not_close) && (!isset($show_button|smarty:nodefaults) || $show_button)}}
    {{mb_include module=dPfacturation template="inc_vw_reglements"}}
  {{elseif $facture->_id}}
    <div class="small-info">Veuiller fermer la facture pour pouvoir renseigner des règlements.</div>
  {{/if}}
</div>

{{if $conf.dPfacturation.CReglement.use_echeancier}}
  <!-- Echelonnements -->
  <div id="echeances-{{$facture->_guid}}" {{if !$view}}style="display: none;"{{/if}}>
    {{mb_script module=facturation script=echeance}}
    <script>
      Main.add(function() {
        Echeance.loadList('{{$facture->_id}}', '{{$facture->_class}}');
      });
    </script>
  </div>
{{/if}}