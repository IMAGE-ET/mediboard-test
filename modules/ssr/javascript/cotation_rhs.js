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
  
  refreshRHS: function(rhs_id, recalculate) {
    new Url('ssr', 'ajax_edit_rhs') .
      addParam('rhs_id', rhs_id) .
      addParam('recalculate', recalculate) .
      requestUpdate('cotation-' + rhs_id, CotationRHS.launchDrawDependancesGraph.curry(rhs_id));
  },
  
  refreshTotaux: function(rhs_id, recalculate) {
    new Url('ssr', 'ajax_totaux_rhs') .
      addParam('rhs_id', rhs_id) .
      addParam('recalculate', recalculate) .
      requestUpdate('totaux-' + rhs_id);
  },

  printRHS: function(form) {
    var url = new Url("ssr", "print_sejour_rhs_no_charge");
    url.addParam("sejour_ids", form.select('input.rhs:checked').pluck('value').join("-"));
    url.addParam("all_rhs", $V(form.all_rhs) ? "1" : "0");
    url.addElement(form.date_monday);
    url.popup(700, 500, "Impression RHS à facturer");
  },

  chargeRHS: function(form) {
    form.onsubmit();
  },

  restoreRHS: function(form) {
    $V(form.facture, '0');
    form.onsubmit();
  },
  
  onSubmitRHS: function(form) {
    return onSubmitFormAjax(form, CotationRHS.refresh.curry($V(form.sejour_id)));
  },
  
  onSubmitLine: function(form) {
     return onSubmitFormAjax(form, CotationRHS.refreshRHS.curry($V(form.rhs_id)));
  },

  confirmDeletionLine: function(form) {
    var options = {
      typeName:'l\'activité'
    };

    var ajax = {
      onComplete: CotationRHS.refreshRHS.curry($V(form.rhs_id))
    };

    confirmDeletion(form, options, ajax);
  },

  onSubmitQuantity: function(form, sField) {
    if($V(form[sField]) == '0' || $V(form[sField]) == '') {
      form.parentNode.removeClassName("ok");
    } else {
      form.parentNode.addClassName("ok");
    }
    
    return onSubmitFormAjax(form, CotationRHS.refreshTotaux.curry($V(form.rhs_id)));
  },
  
  updateTab: function(count) {
    var tab = $("tab-equipements");
    tab.down("a").setClassName("empty", !count);
    tab.down("a small").update("("+count+")");
  },
  
  autocompleteExecutant: function (form) {
    var url = new Url('ssr', 'httpreq_do_intervenant_autocomplete');
    url.autoComplete(form._executant, form.down('.autocomplete.executant'), {
      dropdown: true,
      minChars: 2,
      updateElement: function(element) { CotationRHS.updateExecutant(element, form); }
    } );
  },
  
  updateExecutant: function(selected, form) {
    var values = selected.down('.values'); 
  
    // On vide les valeurs
    if (!values) {
      $V(form._executant, '');
      $V(form.executant_id, '');
      $V(form.code_intervenant_cdarr, '');
    } 
    // Sinon, on rempli les valeurs
    else {
      $V(form.executant_id,           values.down('.executant_id'          ).textContent);
      $V(form.code_intervenant_cdarr, values.down('.code_intervenant_cdarr').textContent);
      $V(form._executant,             values.down('._executant'            ).textContent);
    }
  },

  autocompleteCsARR: function(form) {
    var url = new Url('ssr', 'httpreq_do_csarr_autocomplete');
    url.autoComplete(form.code_activite_csarr, form.down('.autocomplete.activite'), {
      dropdown: true,
      minChars: 2,
      updateElement: function(element) { CotationRHS.updateCsARR(element, form); }
    } );
  },

  updateCsARR: function(selected, form) {
    var value = selected.down('.value');
    $V(form.code_activite_csarr, value ? value.textContent : '');
  },

  editDependancesRHS: function(rhs_id) {
    var url = new Url('ssr', 'ajax_edit_dependances_rhs');
    url.addParam('rhs_id', rhs_id);
    url.requestModal(300, 200);
    url.modalObject.observe("afterClose", CotationRHS.refreshRHS.curry(rhs_id));
  },
  
  drawDependancesGraph: function(container, rhs_id, data) {
    CotationRHS.dependancesGraphs[rhs_id] = (function(container, data){
      Flotr.draw(
        container, 
        data,
        {
          radar: {show: true},
          grid: {circular: true, minorHorizontalLines: true},
          xaxis: {ticks:[
            [0, $T("CDependancesRHS-habillage-court")],
            [1, $T("CDependancesRHS-deplacement-court")],
            [2, $T("CDependancesRHS-alimentation-court")],
            [3, $T("CDependancesRHS-continence-court")],
            [4, $T("CDependancesRHS-comportement-court")],
            [5, $T("CDependancesRHS-relation-court")]
          ]},
          yaxis: {min: 0, max: 4},
          colors: [
            "#c1f1ff",
            "#8cdcff",
            "#00A8F0",
            "#86e8aa",
            "#91f798"
          ],
          legend: {
            labelBoxMargin: 4,
            labelBoxHeight: 5,
            labelBoxWidth: 4,
            margin: 4
          },
          HtmlText: false
        }
      );
    }).curry(container, data);
    
    CotationRHS.launchDrawDependancesGraph(rhs_id);
  },
  
  launchDrawDependancesGraph: function(rhs_id) {
	// Sometimes the container is invisible, flotr doesn't support it
	try { 
      (CotationRHS.dependancesGraphs[rhs_id] || Prototype.emptyFunction)();
      CotationRHS.dependancesGraphs[rhs_id] = function(){};
    } catch(e) {}
  }
};

CotationRHS.dependancesGraphs = CotationRHS.dependancesGraphs || {};

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