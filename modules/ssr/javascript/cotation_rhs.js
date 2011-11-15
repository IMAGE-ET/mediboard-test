/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CotationRHS = {
  refresh: function(sejour_id) {
    new Url('ssr', 'ajax_cotation_rhs') .
      addParam('sejour_id', sejour_id) .
      requestUpdate('cotation-rhs');
    
  },
  
  refreshRHS: function(rhs_id) {
    new Url('ssr', 'ajax_edit_rhs') .
      addParam('rhs_id', rhs_id) .
      requestUpdate('cotation-' + rhs_id);
  },
  
  recalculatehRHS: function(rhs_id) {
    new Url('ssr', 'ajax_recalculate_rhs') .
      addParam('rhs_id', rhs_id) .
      requestUpdate('cotation-' + rhs_id);
  },
  
  refreshTotaux: function(rhs_id) {
    new Url('ssr', 'ajax_totaux_rhs') .
      addParam('rhs_id', rhs_id) .
      requestUpdate('totaux-' + rhs_id);
  },
  
  printRHS: function(rhs_date_monday) {
    var form = getForm('editRHS-'+rhs_date_monday);
    var url = new Url("ssr", "print_sejour_rhs_no_charge");
    url.addParam("sejour_ids", form.select('input.rhs:checked').pluck('value').join("-"));
    url.addParam("all_rhs", $V(form.all_rhs) ? "1" : "0");
    url.addElement(form.date_monday);
    url.popup(700, 500, "Impression RHS à facturer");
  },

  chargeRHS: function(rhs_date_monday) {
    getForm('editRHS-'+rhs_date_monday).onsubmit();
  },

  restoreRHS: function(rhs_date_monday) {
    var form = getForm('editRHS-'+rhs_date_monday);
    $V(form.facture, '0');
    form.onsubmit();
  },
  
  onSubmitRHS: function(oForm) {
    return onSubmitFormAjax(oForm, { 
      onComplete: CotationRHS.refresh.curry($V(oForm.sejour_id))
    } );
  },
  
  onSubmitLine: function(oForm) {
     return onSubmitFormAjax(oForm, {
       onComplete: CotationRHS.refreshRHS.curry($V(oForm.rhs_id)) 
     } );
  },
  
  onSubmitQuantity: function(oForm, sField) {
    if($V(oForm[sField]) == '0' || $V(oForm[sField]) == '') {
      oForm.parentNode.removeClassName("ok");
    } else {
      oForm.parentNode.addClassName("ok");
    }
    return onSubmitFormAjax(oForm, {
      onComplete : CotationRHS.refreshTotaux.curry($V(oForm.rhs_id))
    } );
  },
  
  updateTab: function(count) {
    var tab = $("tab-equipements");
    tab.down("a").setClassName("empty", !count);
    tab.down("a small").update("("+count+")");
  },
  
  updateExecutant: function(selected, oForm) {
    Element.cleanWhitespace(selected);
    var dn = selected.childNodes;
  
    if(dn[0].className == 'informal') {
      // On vide les valeurs
      $V(oForm._executant, '');
      $V(oForm.executant_id, '');
      $V(oForm.code_intervenant_cdarr, '');
      // Sinon, on rempli les valeurs
    } else {
      $V(oForm.executant_id,           dn[0].firstChild.nodeValue);
      $V(oForm.code_intervenant_cdarr, dn[1].firstChild.nodeValue);
      $V(oForm._executant,             dn[2].firstChild.nodeValue);
    }
  },
  
  updateActivite: function(selected, oForm) {
    Element.cleanWhitespace(selected);
    var dn = selected.childNodes;
  
    if(dn[0].className == 'informal') {
      // On vide les valeurs
      $V(oForm.code_activite_cdarr, '');
      // Sinon, on rempli les valeurs
    } else {
      $V(oForm.code_activite_cdarr, dn[0].firstChild.nodeValue);
    }
  },
  
  editDependancesRHS: function(rhs_id) {
    var url = new Url('ssr', 'ajax_edit_dependances_rhs');
    url.addParam('rhs_id', rhs_id);
    url.modal({
      width: 300,
      height: 200
    });
    
    url.modalObject.observe("afterClose", CotationRHS.refreshRHS.curry(rhs_id));
  }
};

Charged = {
  refresh: function(rhs_date_monday) {
    var form = getForm('editRHS-'+rhs_date_monday);
    var label = form.down("label.rhs-charged");
    var count = form.select('tr.charged').length;
    label.setVisibility(count != 0);
    label.down("span").update(count);
  },
  
  addSome: function(rhs_date_monday) {
    var form = getForm('editRHS-'+rhs_date_monday);
    var max = 10;
    form.select('input.rhs').each(function (checkbox) {
      if (!checkbox.checked && $(checkbox).up('tr').visible()) {
        if (max-- > 0) {
          checkbox.checked = true;
        }
      }
    });
  },
  toggle: function(checkbox) {
    $$('tr.charged').invoke('setVisible', !checkbox.checked);
  }
};