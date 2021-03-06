/**
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

Keeper = {
  showListKeeper : function() {
    var url = new Url("passwordKeeper", "ajax_list_keeper");
    url.requestUpdate("vw_list_keeper");
  },

  showKeeper : function(password_keeper_id, elmt) {
    if (password_keeper_id == '0') {
      $('keeperList').select('tr').invoke('removeClassName', 'selected');
    }
    var url = new Url("passwordKeeper", "ajax_edit_keeper");
    url.addParam("password_keeper_id", password_keeper_id);
    url.requestUpdate("vw_edit_keeper", {
      method: "post",
      getParameters: {
        m: "passwordKeeper",
        a: "ajax_edit_keeper"
      },
      onComplete: function() {
        if (password_keeper_id != 0) {
          Keeper.showListCategory(password_keeper_id);
        }
        var tr = elmt.up('tr');
        if (tr) {
          tr.addUniqueClassName('selected');
        }
      }
    });
  },

  submitKeeper : function(form, password_keeper_id) {
    return onSubmitFormAjax(form, {
      onComplete: function() {
        Keeper.showListKeeper();
      }
    });
  },

  showListCategory : function(password_keeper_id) {
    var url = new Url("passwordKeeper", "ajax_list_category");
    url.addParam("password_keeper_id", password_keeper_id);
    url.requestUpdate("vw_list_category");
  },
  
  showCategory : function(category_id, password_keeper_id, elmt) {
    if (category_id == '0') {
      $('categoryList').select('tr').invoke('removeClassName', 'selected');
    }
    var url = new Url("passwordKeeper", "ajax_edit_category");
    url.addParam("category_id", category_id);
    url.addParam("password_keeper_id", password_keeper_id);
    url.requestUpdate("vw_edit_category", {
      onComplete:function() {
        if (category_id != 0) {
          Keeper.showListPasswordEntry(category_id);
        }
        var tr = elmt.up('tr');
        if (tr) {
          tr.addUniqueClassName('selected');
        }
      }
    });
  },
  
  submitCategory : function(form, password_keeper_id) {
    onSubmitFormAjax(form, {
      onComplete: function() {
        Keeper.showListCategory(password_keeper_id);
      }
    });
    return false;
  },

  showListPasswordEntry : function(category_id) {
    var url = new Url("passwordKeeper", "ajax_list_password");
    url.addParam("category_id", category_id);
    url.requestUpdate("vw_list_password");
  },

  showPasswordEntry : function(password_id, category_id, elmt) {
    if (password_id == '0') {
      $('passwordList').select('tr').invoke('removeClassName', 'selected');
    }
    var url = new Url("passwordKeeper", "ajax_edit_password");
    url.addParam("password_id", password_id);
    url.addParam("category_id", category_id);
    url.requestUpdate("vw_edit_password", {
      onComplete: function() {
        var tr = elmt.up('tr');
        if (tr) {
          tr.addUniqueClassName('selected');
        }
      }
    });
  },

  submitPasswordEntry : function(form, category_id) {
    onSubmitFormAjax(form, {
      onComplete: function() {
        Keeper.showListPasswordEntry(category_id);
      }
    });
    return false;
  },

  promptPassphrase : function(password_keeper_id, deletion, exportKeeper) {
    if (deletion) {
      Modal.open("modalPassphrase");
      var form = getForm("passphrase");
      form.passphraseInput.focus();
      $V(form.password_keeper_id, password_keeper_id);
      $V(form.deletion, true);
      return;
    }
    if (exportKeeper) {
      Modal.open("modalPassphrase");
      var form = getForm("passphrase");
      form.passphraseInput.focus();
      $V(form.password_keeper_id, password_keeper_id);
      //form.passphraseInputExport.writeAttribute("type", "password");
      $('passphrase2').show();
      return;
    }
    Modal.open("modalPassphrase");
    var form = getForm("passphrase");
    form.passphraseInput.focus();
    $V(form.password_keeper_id, password_keeper_id);
  },

  getPassphrase : function(form, password_keeper_id, deletion, exportKeeper) {
    passphrase = $V(form.passphraseInput);
    if (passphrase != null) {
      var url = new Url("passwordKeeper", "ajax_edit_keeper");
      url.addParam("password_keeper_id", password_keeper_id);
      url.addParam("passphrase", passphrase);

      if (deletion) {
        url.addParam("deletion", 1);
        url.requestUpdate("vw_edit_keeper",
          { method: "post",
            getParameters: {
              m: "passwordKeeper",
              a: "ajax_edit_keeper"
            },
            onComplete: function() {
              $V(form.passphraseInput, '');
              Control.Modal.close();
            }
          }
        );

        return false;
      }

      if (exportKeeper) {
        var url = new Url("passwordKeeper", "vw_export_keeper", 'raw');
        url.pop(10, 10, null, null, null,
          { password_keeper_id: password_keeper_id, oldPassphrase: passphrase, newPassphrase: exportKeeper }
        );
        $V(form.passphraseInput, '');
        $V(form.passphraseInputExport, '');
        Control.Modal.close();

        return false;
      }

      url.requestUpdate("vw_edit_keeper", {
        method: "post",
        getParameters: {
          m: "passwordKeeper",
          a: "ajax_edit_keeper"
        },
        onComplete: function() {
          if (password_keeper_id != 0) {
            Keeper.showListCategory(password_keeper_id);
          }
          $V(form.passphraseInput, '');
          Control.Modal.close();
        }
      });
    }

    return false;
  },

  revealPasswordEntry : function(password_id) {
    var url = new Url("passwordKeeper", "ajax_revealed");
    url.addParam("password_id", password_id);
    url.requestUpdate("vw_reveal_password");
  },

  popupImport : function() {
    var url = new Url("passwordKeeper", "vw_import_keeper");
    url.pop(500, 400, "Importation d'un trousseau");
  }
};