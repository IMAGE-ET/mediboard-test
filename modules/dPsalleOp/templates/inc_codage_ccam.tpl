<script>
  signerActes = function(object_id, object_class){
    var url = new Url("dPsalleOp", "vw_signature_actes");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("dialog", 1);
    url.requestModal(700, 400, 'Signature des actes');
  };
  
  clotureActivite = function(object_id, object_class) {
    var url = new Url("dPsalleOp", "ajax_cloture_activite");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.requestModal(500, 300);
  };

  {{if $subject->_class == "CConsultation"}}
    {{assign var=sejour value=$subject->_ref_sejour}}
    Main.add(function() {
      if (window.tabsConsult || window.tabsConsultAnesth) {
        var count_items = {{$subject->_count_actes}};
        {{if $sejour->DP}}
        count_items++;
        {{/if}}
        {{if $sejour->DR}}
        count_items++;
        {{/if}}
        count_items += {{$sejour->_diagnostics_associes|@count}};
        Control.Tabs.setTabCount("Actes", count_items);
      }
    });
  {{/if}}
</script>

<table class="form">
  {{if $subject->_coded}}
    {{if $subject->_class == "CConsultation"}}
      <tr>
        <td colspan="10">
          <div class="small-info">
           La cotation des actes est terminée.<br />
           Pour pouvoir coder des actes, veuillez dévalider la cotation.
           </div>
         </td>
      </tr>
    {{else}}
      <tr>
        <td {{if $conf.dPsalleOp.CActeCCAM.envoi_actes_salle && $conf.dPsalleOp.COperation.modif_actes == 'facturation'}}
              colspan="5" class="halfPane text"
            {{else}}
              colspan="10" class="text"
            {{/if}}>
          <div class="small-info">
            Les actes ne peuvent plus être modifiés pour la raison suivante : {{tr}}config-dPsalleOp-COperation-modif_actes-{{$conf.dPsalleOp.COperation.modif_actes}}{{/tr}}
            <br />
            Veuillez contacter le PMSI pour toute modification.
          </div>
        </td>
        {{if $conf.dPsalleOp.CActeCCAM.envoi_actes_salle && $conf.dPsalleOp.COperation.modif_actes == 'facturation'}}
          <script>
            Main.add(function () {
              PMSI.loadExportActes('{{$subject->_id}}', '{{$subject->_class}}', 1, 'dPsalleOp');
            });
          </script>

          <td class="halfPane">
            <fieldset>
              <legend>Validation du codage</legend>
              <div id="export_{{$subject->_class}}_{{$subject->_id}}">

              </div>
            </fieldset>
          </td>
        {{/if}}
      </tr>
    {{/if}}
  {{/if}}
  {{if !$subject->_canRead}}
    <tr>
      <td colspan="10" class="text">
        <div class="small-info">Vous n'avez pas les droits nécessaires pour coder les actes</div>
      </td>
    </tr>
  {{elseif !$subject->_coded}}
    <!-- Gestion des codes -->
    {{if $can->admin || $m == "dPpmsi" || (!$subject->_coded && $subject->_canRead)}}
    <tr>
      <td class="text">
        {{mb_include module=salleOp template=inc_manage_codes}}
      </td>
    </tr>
    {{/if}}

    <tr>
      <td class="text">
        <!-- Codage des actes -->
        {{if $subject->_coded && !$can->admin && $m != "dPpmsi"}}
          {{mb_include module=salleOp template=inc_possible_actes_ccam}}
        {{else}}
          {{mb_include module=salleOp template=inc_edit_actes_ccam}}
        {{/if}}
      </td>
    </tr>
  {{else}}
    {{mb_script module=pmsi script=PMSI ajax=true}}
    {{mb_include module=pmsi template=inc_codage_actes show_ngap=false read_only=true}}
  {{/if}}
</table>