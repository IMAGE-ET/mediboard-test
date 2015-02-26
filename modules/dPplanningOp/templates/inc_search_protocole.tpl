{{mb_default var=formOp value="editOp"}}
{{mb_default var=formSecondOp value="editOpEasy"}}
{{mb_default var=id_protocole value="get_protocole"}}

<input type="text" name="search_protocole" style="width: 15em;" placeholder="{{tr}}fast-search{{/tr}}" onblur="$V(this, '')"/>
<input type="checkbox" name="search_all_chir" title="Étendre la recherche à tous les praticiens" />
<div style="display:none;" id="{{$id_protocole}}"></div>

<script>
  ProtocoleSelector.inite = function(){
    this.sForSejour     = false;
    this.sChir_id       = "chir_id";
    this.sChir_view     = "chir_id_view";
    this.sCodes_ccam    = "codes_ccam";
    this.sCote          = "cote";
    this.sLibelle       = "libelle";
    this.sTime_op       = "_time_op";
    this.sMateriel      = "materiel";
    this.sExamenPerop   = "exam_per_op";
    this.sExamen        = "examen";
    this.sDepassement   = "depassement";
    this.sForfait       = "forfait";
    this.sFournitures   = "fournitures";
    this.sRques_op      = "rques";
    this.sServiceId     = "service_id";
    this.sPresencePreop = "presence_preop";
    this.sPresencePostop = "presence_postop";
    this.sType          = "type";
    this.sCharge_id     = "charge_id";
    this.sTypeAnesth    = "type_anesth";
    this.sUf_hebergement_id = "uf_hebergement_id";
    this.sUf_medicale_id = "uf_medicale_id";
    this.sUf_soins_id = "uf_soins_id";
    this.sTypesRessourcesIds = "_types_ressources_ids";
    {{if $conf.dPplanningOp.CSejour.show_type_pec}}
      this.sTypePec     = "type_pec";
    {{/if}}
    {{if $conf.dPplanningOp.CSejour.show_facturable}}
      this.sFacturable   = "facturable";
    {{/if}}
    this.sDuree_uscpo   = "duree_uscpo";
    this.sDuree_preop   = "duree_preop";
    this.sDuree_prevu   = "_duree_prevue";
    this.sDuree_prevu_heure   = "_duree_prevue_heure";
    this.sConvalescence = "convalescence";
    this.sDP            = "DP";
    this.sRques_sej     = "rques";
    this.sExamExtempo   = "exam_extempo";

    this.sServiceId_easy  = "service_id";
    this.sLibelle_easy    = "libelle";
    this.sCodes_ccam_easy = "codes_ccam";
    this.sLibelle_sejour  = "libelle";

    this.sProtoPrescAnesth = "_protocole_prescription_anesth_id";
    this.sProtoPrescChir   = "_protocole_prescription_chir_id";
  }

  ajoutProtocole = function(protocole_id) {
    if (aProtocoles['interv'][protocole_id]) {
      ProtocoleSelector.set(aProtocoles['interv'][protocole_id]);
    }
  }

  Main.add(function () {
    aProtocoles = {
      sejour: {},
      interv: {}
    };

    ProtocoleSelector.inite();
    var oForm = getForm('{{$formOp}}');
    var url = new Url('planningOp', 'ajax_protocoles_autocomplete');
    url.addParam('field'          , 'protocole_id');
    url.addParam('input_field'    , 'search_protocole');
    url.addParam('for_sejour', '0');
    url.autoComplete(oForm.elements.search_protocole, null, {
      minChars: 3,
      method: 'get',
      select: 'view',
      dropdown: true,
      width: '400px',
      afterUpdateElement: function(field, selected) {
        ajoutProtocole(selected.get('id'));
        $V(field.form.elements.search_protocole, "")
      },
      callback: function(input, queryString) {
        return queryString +
          (input.form.search_all_chir.checked ? "" : "&chir_id=" + $V(input.form.chir_id));
      }
    });
  });
</script>