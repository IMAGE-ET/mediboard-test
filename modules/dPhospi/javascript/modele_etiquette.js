ModeleEtiquette = {
  nb_printers: 0,

  print: function(object_class, object_id, modele_etiquette_id, uniq_id) {
    if (ModeleEtiquette.nb_printers > 0) {
      var url = new Url('compteRendu', 'ajax_choose_printer');

      if (modele_etiquette_id) {
        Control.Modal.close();
        url.addParam('modele_etiquette_id', modele_etiquette_id);
      }

      url.addParam('mode_etiquette', 1);
      url.addParam('object_class', object_class);
      url.addParam('object_id', object_id);
      url.requestModal(400);
    }
    else {
      var form = getForm('download_etiq_'+object_class+'_'+object_id+'_'+uniq_id);
      if (modele_etiquette_id) {
        $V(form.modele_etiquette_id, modele_etiquette_id);
      }
      form.submit();
      if (modele_etiquette_id) {
        Control.Modal.close();
      }
    }
  },

  chooseModele: function(object_class, object_id, unique_id, afterClose) {
    var url = new Url('hospi', 'ajax_choose_modele_etiquette');
    url.addParam('object_class', object_class);
    url.addParam('object_id', object_id);
    url.addParam("unique_id", unique_id);
    url.requestModal(400);
    url.modalObject.observe("afterClose", Object.isFunction(afterClose) ? afterClose : Prototype.emptyFunction);
  },

  refreshList: function() {
    var form = getForm('Filter');
    var url = new Url('hospi', 'ajax_list_modele_etiquette');
    url.addNotNullElement(form.filter_class);
    url.requestUpdate("list_etiq");
    return false;
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, ModeleEtiquette.refreshList);
  },

  onSubmitComplete: function (guid) {
    Control.Modal.close();
    var id = guid.split('-')[1];
    ModeleEtiquette.edit(id);
  },

  edit: function(modele_etiquette_id) {
    Form.onSubmitComplete = modele_etiquette_id == '' ? 
      ModeleEtiquette.onSubmitComplete : 
      Prototype.emptyFunction;

    var selected = $('modele_etiq-'+modele_etiquette_id);
    if (selected) {
      selected.addUniqueClassName('selected');
    }

    var url = new Url('hospi', 'ajax_edit_modele_etiquette');
    url.addParam('modele_etiquette_id', modele_etiquette_id);
    url.requestModal(800);
    url.modalObject.observe("afterClose", ModeleEtiquette.refreshList);
  },

  confirmDeletion: function(form) {
    var options = {
      typeName: 'Le modèle ', 
      objName: $V(form.nom)
    };

    var ajax = Control.Modal.close;

    confirmDeletion(form, options, ajax);    
  },

  preview: function() {
    var form_edit     = getForm("edit_etiq"    );
    var form_download = getForm("download_prev");
    $V(form_download.largeur_page , $V(form_edit.largeur_page ));
    $V(form_download.hauteur_page , $V(form_edit.hauteur_page ));
    $V(form_download.nb_lignes    , $V(form_edit.nb_lignes    ));
    $V(form_download.nb_colonnes  , $V(form_edit.nb_colonnes  ));
    $V(form_download.marge_horiz  , $V(form_edit.marge_horiz  ));
    $V(form_download.marge_vert   , $V(form_edit.marge_vert   ));
    $V(form_download.hauteur_ligne, $V(form_edit.hauteur_ligne));
    $V(form_download.nom          , $V(form_edit.nom          ));
    $V(form_download.texte        , $V(form_edit.texte        ));
    $V(form_download.texte_2      , $V(form_edit.texte_2      ));
    $V(form_download.texte_3      , $V(form_edit.texte_3      ));
    $V(form_download.texte_4      , $V(form_edit.texte_4      ));
    $V(form_download.font         , $V(form_edit.font         ));
    $V(form_download.show_border  , $V(form_edit.show_border  ));
    $V(form_download.text_align   , $V(form_edit.text_align   ));
    form_download.submit();
  },

  insertField: function(elem) {
    var texte_etiq = window.text_focused;
    if (!texte_etiq) {
      texte_etiq = $("edit_etiq_texte");
    }
    var caret = texte_etiq.caret();
    var form = elem.form;
    var bold  = $V(form._write_bold);
    var upper = $V(form._write_upper);
    var content = elem.value;
    if (bold == "1") {
      if (upper == "1") {
        content = "#" + content + "#";
      }
      else {
        content = "*" + content + "*";
      }
    }
    else if (upper == "1") {
      content = "+" + content + "+";
    }
    else {
      content = "[" + content + "]";
    }

    texte_etiq.caret(caret.begin, caret.end, content + " ");
    texte_etiq.caret(texte_etiq.value.length);
    texte_etiq.fire('ui:change');
    $V(getForm('edit_etiq').fields, '');
  }
}
