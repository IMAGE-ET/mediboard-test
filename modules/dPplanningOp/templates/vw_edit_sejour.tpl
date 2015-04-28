{{mb_script module="dPplanningOp" script="protocole_selector"}}

<script>
  ProtocoleSelector.init = function() {
    this.sForSejour      = true;
    this.sForm           = "editSejour";
    this.sChir_id        = "praticien_id";
    this.sChir_view      = "praticien_id_view";
    this.sServiceId      = "service_id";
    this.sDP             = "DP";
    this.sDepassement    = "depassement";

    this.sLibelle_sejour = "libelle";
    this.sType           = "type";
    this.sCharge_id       = "charge_id";
    this.sDuree_prevu    = "_duree_prevue";
    this.sDuree_prevu_heure = "_duree_prevue_heure";
    this.sConvalescence  = "convalescence";
    this.sRques_sej      = "rques";

    {{if $conf.dPplanningOp.CSejour.show_type_pec}}
      this.sTypePec      = "type_pec";
    {{/if}}
    {{if $conf.dPplanningOp.CSejour.show_facturable}}
      this.sFacturable   = "facturable";
    {{/if}}

    this.sProtoPrescAnesth = "_protocole_prescription_anesth_id";
    this.sProtoPrescChir   = "_protocole_prescription_chir_id";

    this.pop();
  };

  function toggleMode() {
    var trigger = $("modeExpert-trigger"),
        hiddenElements = $$(".modeExpert"),
        expert = !hiddenElements[0].visible();

    trigger.update(expert ? '{{tr}}button-COperation-modeExpert{{/tr}}' : '{{tr}}button-COperation-modeEasy{{/tr}}');
    hiddenElements.invoke("setVisible", expert);
  }

  window.refreshingSejours = false;

  function reloadSejours(checkCollision) {
    var oForm = getForm("editSejour");
    var patient_id = $V(oForm.patient_id);

    if (!patient_id) {
      return;
    }

    // Changer l'entrée prévue d'un séjour change également la sortie prévue,
    // il faut donc éviter de lancer deux fois cette fonction.
    if (window.refreshingSejours) {
      return;
    }
    window.refreshingSejours = true;
    var url = new Url("dPplanningOp", "ajax_list_sejours");
    url.addParam("check_collision", checkCollision);
    url.addParam("patient_id", patient_id);

    // L'entrée prévue est envoyée pour chercher les séjours datant de moins de 48h
    url.addParam("date_entree_prevue", $V(oForm._date_entree_prevue));
    url.addParam("hour_entree_prevue", $V(oForm._hour_entree_prevue));
    url.addParam("min_entree_prevue" , $V(oForm._min_entree_prevue));

    // Dans le cas où on va checker la collision,
    // on envoie également la sortie prévue
    if (checkCollision) {
      url.addParam("date_sortie_prevue", $V(oForm._date_sortie_prevue));
      url.addParam("hour_sortie_prevue", $V(oForm._hour_sortie_prevue));
      url.addParam("min_sortie_prevue" , $V(oForm._min_sortie_prevue));
      url.addParam("sejour_id"         , $V(oForm.sejour_id));
    }
    url.requestUpdate("list_sejours", {onComplete: function() { window.refreshingSejours = false; }});
  }

  {{if $sejour->_id && $dialog == 1}}
    // Il faut sauvegarder le sejour_id pour la création de l'affectation
    // après la fermeture de la modale.
    window.parent.sejour_id_for_affectation = '{{$sejour->_id}}';
  {{/if}}

  {{if $app->user_prefs.mode_dhe == 0}}
    Main.add(toggleMode);
  {{/if}}
</script>

<table class="main">

  {{if $sejour->_id && !$dialog}}
    <tr>
      <td colspan="2">
        <a id="didac_program_new_sejour" class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;sejour_id=0">
          {{tr}}CSejour.create{{/tr}}
        </a>
      </td>
    </tr>
  {{/if}}
  <tr>
    {{if $sejour->_id}}
      <th colspan="2" class="title modify">
        {{mb_include module=system template=inc_object_idsante400 object=$sejour}}
        {{mb_include module=system template=inc_object_history    object=$sejour}}
        {{mb_include module=system template=inc_object_notes      object=$sejour}}

        <button type="button" class="search" style="float: left;" onclick="ProtocoleSelector.init()">
          {{tr}}button-COperation-choixProtocole{{/tr}}
        </button>

        <button type="button" class="hslip" style="float: right;" onclick="toggleMode(this)" id="modeExpert-trigger">
          {{tr}}button-COperation-modeExpert{{/tr}}
        </button>

        Modification du séjour {{$sejour->_view}}
        {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
      </th>
      {{else}}
      <th colspan="2" class="title">
        <button type="button" class="search" style="float: left;" onclick="ProtocoleSelector.init()">
          {{tr}}button-COperation-choixProtocole{{/tr}}
        </button>

        <button type="button" class="hslip" style="float: right;" onclick="toggleMode(this)" id="modeExpert-trigger">
          {{tr}}button-COperation-modeExpert{{/tr}}
        </button>
        Création d'un nouveau séjour
      </th>
    {{/if}}
  </tr>
  
  <tr>
    <td style="width: 60%">
      {{mb_include module=planningOp template=js_form_sejour}}
      {{mb_include module=planningOp template=inc_form_sejour mode_operation=false}}
    </td>
    <td>
      {{if $m != "reservation" && $sejour->_id}}
        <a class="button new" href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id=0&amp;sejour_id={{$sejour->_id}}" id="link_operation" target="_parent">
          Programmer une nouvelle intervention dans ce séjour
        </a>
        <label>
          <input type="checkbox" onclick="
            if (this.checked) {
            $('link_operation').href = '?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id=0&amp;sejour_id={{$sejour->_id}}';
            } else {
            $('link_operation').href = '?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id=0&amp;sejour_id={{$sejour->_id}}';
            }" /> Hors plage
        </label>
      {{/if}}
      {{mb_include module=planningOp template=inc_infos_operation}}
      {{mb_include module=planningOp template=inc_infos_hospitalisation}}
      <table class="form" style="width: 100%;">
        <tr>
          <th class="title">{{tr}}CSejour-existants{{/tr}}</th>
        </tr>
        <tr>
          <td id="list_sejours">
            {{mb_include module=planningOp template=inc_list_sejours selected_guid=$sejour->_guid}}
          </td>
        </tr>

        {{if $sejour->_id}} 
        <tr>
          <th class="title">
            {{tr}}CMbObject-back-documents{{/tr}}
          </th>
        </tr>
        <tr>
          <td id="documents">
            {{mb_script module=compteRendu script=document}}
            {{mb_script module=compteRendu script=modele_selector}}
            <script type="text/javascript">
            Document.register('{{$sejour->_id}}','{{$sejour->_class}}','{{$sejour->praticien_id}}', 'documents');
            </script>
          </td>
        </tr>

        <tr>
          <th class="title">
            {{tr}}CMbObject-back-files{{/tr}}
          </th>
        </tr>
        <tr>
          <td id="files">
            {{mb_script module=files script=file}}
            <script type="text/javascript">
            File.register('{{$sejour->_id}}','{{$sejour->_class}}', 'files');
            </script>
            {{mb_include module=files template=yoplet_uploader object=$sejour}}
          </td>
        </tr>
        {{/if}}

        {{if $sejour->_id && "forms"|module_active}}
          <tr>
            <th class="title">
              {{tr}}CMbObject-back-ex_links_meta{{/tr}}
            </th>
          </tr>
          <tr>
            <td style="vertical-align: top;">
              {{unique_id var=unique_id_sejour_forms}}

              <script type="text/javascript">
                Main.add(function(){
                  ExObject.loadExObjects("{{$sejour->_class}}", "{{$sejour->_id}}", "{{$unique_id_sejour_forms}}", 0.5);
                });
              </script>

              <div id="{{$unique_id_sejour_forms}}"></div>
            </td>
          </tr>
        {{/if}}
      </table>
    </td>
  </tr>

</table>