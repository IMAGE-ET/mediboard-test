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
  
  refreshTotaux: function(rhs_id) {
    new Url('ssr', 'ajax_totaux_rhs') .
      addParam('rhs_id', rhs_id) .
      requestUpdate('totaux-' + rhs_id);
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
    if($V(oForm[sField]) == '0') {
      oForm.parentNode.removeClassName("highlight");
    } else {
      oForm.parentNode.addClassName("highlight");
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
  }
};