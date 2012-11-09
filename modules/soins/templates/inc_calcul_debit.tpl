{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script type="text/javascript">
  Main.add(function() {
    var form = getForm("calculPerf");
    form.debit.addSpinner({min: 0});
    poids = $$(".poids_patient");
    volume = {{$line->_quantite_totale}} - $V(form.rapport) * $V(form.quantite);
    
    // On ne garde que les unités notées en mg
    $A(form.unite.options).each(function(elt) {
      if (!/(^mg)/.test(elt.innerHTML)) {
        elt.remove();
      }
    });
    
    if (poids.length) {
      poids = parseInt(poids[0].innerHTML);
      $("view_poids").update("<strong>"+poids+" kg</strong>");
    }
    else {
      $("alert_poids").show();
      $("calcul_perf_poids").hide();
      $A(form.unite.options).each(function(elt) {
        if (/\/kg/.test(elt.innerHTML)) {
          elt.writeAttribute("disabled");
        }
      });
    }
  });
  
  updateData = function() {
    var form = getForm("calculPerf");
    
    // Débit massique nécessaire
    var debit = $V(form.debit);
    var unite = form.unite.selectedOptions[0].innerHTML.split(" ")[0];
    
    var debit_necessaire = $("debit_necessaire");
    var result_necessaire = debit;
    
    if (/\/kg/.test(unite)) {
      result_necessaire *= poids;
    }
    
    debit_necessaire.update(result_necessaire + " " + unite.replace("/kg", ""));
    
    var rapport_conditionnement = $V(form.rapport);
    var rapport_debit = parseFloat($V(form.unite));
    
    {{if $line->type == "classique"}}
      var duree = parseFloat($("result_duree").innerHTML);
      
      var qte = debit * duree * rapport_debit / rapport_conditionnement;
      if (/\/kg/.test(unite)) {
        qte *= poids;
      }
      
      qte = Math.round(qte * 100) / 100;      
      $("conditionnement").update("<strong>"+qte+"</strong>");
      
      // Mise à jour du volume
      var volume_produit = qte * rapport_conditionnement;
      var volume_ref = Math.round((volume + volume_produit)*100) / 100;
      $("volume_ref").update(volume_ref);
      var debit_volumique = Math.round(($("volume_ref").innerHTML / duree) * 100) / 100;
      $("debit_volumique").update("<strong>"+debit_volumique+"</strong>");
    {{else}}
      var qte = $V(form.quantite);
      
      if (/\/kg/.test(unite)) {
        qte *= poids;
      }
      
      var duree = Math.round((qte / rapport_debit/ debit * rapport_conditionnement) * 100) / 100;
      
      $("result_duree").update("<strong>"+(duree != "Infinity" ? duree : "-")+"</strong>");
      var debit_volumique = Math.round(($("volume_ref").innerHTML / duree) * 100 * rapport_debit) / 100;
      $("debit_volumique").update("<strong>"+debit_volumique+"</strong>");
    {{/if}}
    
    
  }
  
  setValuesClassique = function() {
    var form = getForm("calculPerf");
    var formLineItem = getForm("editLinePerf-{{$line_item->_id}}")
    var quantite = parseFloat($("conditionnement").down("strong").innerHTML);
    $V(formLineItem.quantite, quantite, false);
    //$V(formLineItem.unite, unite, false);
    
    onSubmitFormAjax(formLineItem, {onComplete: function() {
      Prescription.updateVolumeTotal('{{$line->_id}}', 1, null, null, null, function() {
        formLineItem.quantite.onchange();
        Prescription.updateDebit('{{$line->_id}}');
        Control.Modal.close();
      });
    }});
  }
  
  setValuesOther = function() {
    var form = getForm("calculPerf");
    var debit = $V(form.debit);
    var unite = $V(form.unite);
    var qte   = $V(form.quantite);
    var duree = $("result_duree").down("strong").innerHTML;
    
    var formPerf = getForm("editPerf-{{$line->_id}}");
    $V(formPerf.duree_debit, duree, false);
    
    var formLineItem = getForm("editLinePerf-{{$line_item->_id}}");
    $V(formLineItem.quantite, qte, false);
    $V(formLineItem.unite, unite, false);
    
    onSubmitFormAjax(formPerf, function() {
      Prescription.updateVolumeTotal('{{$line->_id}}', 1, null, null, null, function() {
        onSubmitFormAjax(formLineItem, function() {
          formLineItem.quantite.onchange();
          Prescription.updateDebit('{{$line->_id}}');
          Control.Modal.close();
        });
      });
    });
  }
</script>

<div class="small-info">
  {{if $line->type == "classique"}}
    Perfusion classique : <strong>Calcul de la quantité de produit</strong>
  {{else}}
    Perfusion électique ({{tr}}CPrescriptionLineMix.type.{{$line->type}}{{/tr}}) :
      <strong>Calcul du débit volumique</strong>
  {{/if}}
</div>

<div class="small-warning" style="display: none;" id="alert_poids">
  Le poids du patient n'est pas renseigné. Veuillez le renseigner pour accéder aux unités relatives au poids. 
</div>

<form name="calculPerf" method="get">
  {{mb_field object=$line_item field=quantite hidden=true}}
  {{assign var=unite value=$line_item->unite}}
  <input type="hidden" name="rapport"
    value="{{if preg_match("/^mg\/kg/", $unite)}}1{{else}}{{$line_item->_ref_produit->rapport_unite_prise.$unite.ml}}{{/if}}" />
  <table class="form">
    <tr>
      <th class="title" colspan="2">
        {{$line_item->_ucd_view}}
      </th>
    </tr>
    
    <tr>
      <th style="width: 30%">
        Débit choisi
      </th>
      <td>
        <input type="text" name="debit" size="5" onchange="updateData()" />
          <select name="unite" onchange="updateData()">
            {{if $line_item->_ref_produit_prescription->_id}}
               <option value="{{$line_item->_ref_produit_prescription->unite_prise}}">{{$line_item->_ref_produit_prescription->unite_prise}}</option>
            {{else}}
              {{foreach from=$line_item->_unites_prise item=_unite}}
                {{if preg_match("/^mg\/kg/", $_unite)}}
                  {{assign var=__unite value=$_unite|regex_replace:"/\/kg/":""}}
                {{else}}
                  {{assign var=__unite value=$_unite}}
                {{/if}}
                <option value="{{$line_item->_ref_produit->rapport_unite_prise.$__unite.ml}}"
                  {{if $line_item->unite == $_unite}}selected="selected"{{/if}}>{{$_unite}}</option>
              {{/foreach}}
            {{/if}}
          </select>
        / h
      </td>
    </tr>
    <tr id="calcul_perf_poids">
      <th>Poids du patient</th>
      <td id="view_poids"></td>
    </tr>
    <tr>
      <th>
        Débit nécessaire
      </th>
      <td >
        <span id="debit_necessaire">-</span>/h
      </td>
    </tr>
    
    <tr>
      <td colspan="2">
        <hr />
      </td>
    </tr>
    
    <tr>
      <th>
        Conditionnement choisi
      </th>
      <td>
        {{$line_item->unite}}
      </td>
    </tr>
    
    <tr>
      <th>
        {{if $line->type == "classique"}}
          Quantité nécessaire pour la durée de référence
        {{else}}
          Quantité choisie
        {{/if}}
      </th>
      <td>
        <span id="conditionnement">
          {{$line_item->quantite}}
        </span>
        {{$line_item->_view_unite_prise}}
      </td>
    </tr>
     
    <tr>
      <td colspan="2">
        <hr />
      </td>
    </tr>
    
    <tr>
      <th>Durée de référence</th>
      <td>
        <span id="result_duree">
          {{if $line->type == "classique"}}
            {{$line->duree_debit}}
          {{/if}}
        </span> h
      </td>
    </tr>
    <tr>
      <th>
        Volume de référence
      </th>
      <td>
        <span id="volume_ref">{{$line->_quantite_totale}}</span> ml
      </td>
    </tr>
    <tr>
      <th>
        Débit volumique
      </th>
      <td>
        <span id="debit_volumique">-</span> ml/h
      </td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {{if $line->type =="classique"}}
          <button type="button" class="tick oneclick" onclick="setValuesClassique();">{{tr}}Validate{{/tr}}</button>
        {{else}}
          <button type="button" class="tick oneclick" onclick="setValuesOther();">{{tr}}Validate{{/tr}}</button>
        {{/if}}
        
        <button type="button" class="cancel" onclick="Control.Modal.close()">{{tr}}Close{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>