/**
 * Created by flavien on 19/09/14.
 */

Modele = {
  showUtilisation: function(compte_rendu_id) {
    var url = new Url('compteRendu', 'ajax_show_utilisation');
    url.addParam('compte_rendu_id', compte_rendu_id);
    url.requestModal(640, 480);
  },

  refresh: function() {
    var url = new Url("compteRendu", "ajax_list_modeles");
    url.addFormData(getForm("filterModeles"));
    url.requestUpdate("modeles_area");
  },

  edit: function(compte_rendu_id) {
    var url = new Url("compteRendu", "addedit_modeles");
    url.addParam("compte_rendu_id", compte_rendu_id);
    url.modal({width: "95%", height: "90%", onClose: Modele.refresh, closeOnEscape: false, waitingText: true});
  },

  remove: function(compte_rendu_id, nom) {
    var form = getForm("deleteModele");
    $V(form.compte_rendu_id, compte_rendu_id);
    confirmDeletion(form, {
      typeName: 'le modèle',
      objName:  nom,
      ajax:     1
    });
  },

  preview: function(id) {
    var url = new Url("compteRendu", "print_cr");
    url.addParam("compte_rendu_id", id);
    url.popup(800, 800);
  },

  preview_layout: function() {
    var header_size = parseInt($V(getForm("editFrm").elements.height));
    if (!isNaN(header_size)) {
      $("header_footer_content").style["height"] = ((header_size / 728.5)*80).round() + "px";
    }
    $("body_content").style["height"] =  "80px";
  },

  generate_auto_height: function() {
    var content = window.CKEDITOR.instances.htmlarea ? CKEDITOR.instances.htmlarea.getData() : $V(form.source);
    var container = new Element("div", {style: "width: 17cm; padding: 0; margin: 0; position: absolute; left: -1500px; bottom: 200px;"}).insert(content);
    $$('body')[0].insert(container);
    // Calcul approximatif de la hauteur
    $V(getForm("editFrm").height, (container.getHeight()).round());
  },

  showUtilisation: function(compte_rendu_id) {
    var url = new Url("compteRendu", "ajax_show_utilisation");
    url.addParam("compte_rendu_id", compte_rendu_id);
    url.requestModal(640, 480);
  },

  copy: function(form, user_id, droit) {
    form = form || getForm("editFrm");

    $V(form.compte_rendu_id, "");
    $V(form.nom, "Copie de " + $V(form.nom));
    $V(form.user_id, user_id);
    if (droit && !confirm($T('CCompteRendu-already-access'))) {
      return;
    }
    form.onsubmit();
  },

  filter: function(input) {
    var table = input.up('table');

    var term = $V(input);

    if (!term) {
      table.select("tr.line").invoke("show");
      return;
    }

    table.select("tr.line").invoke("hide");
    table.select(".CCompteRendu-view").each(function(e) {
      if (e.innerHTML.like(term)) {
        e.up('tr').show();
      }
    });
  },

  exportXML: function(owner, object_class, modeles_ids) {
    var url = new Url("compteRendu", "ajax_export_modeles", "raw");
    url.addParam("owner", owner);
    url.addParam("object_class", object_class);
    url.pop(400, 300, "export_csv", null, null, {
      modeles_ids:  modeles_ids.join("-"),
      owner:        owner,
      object_class: object_class
    })
  },

  importXML: function(owner_guid) {
    var url = new Url("compteRendu", "ajax_vw_import_modele");
    url.addParam("owner_guid", owner_guid);
    url.pop(500, 400, "Import de modèles");
  }
};