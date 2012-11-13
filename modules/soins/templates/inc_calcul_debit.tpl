{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{assign var=unite_administration value=$line_item->_unite_administration}}

{{if $line->type != "classique" && (!$line->volume_debit || !$line_item->quantite)}}
  <div class="small-warning">
    Le volume de référence et la quantité sont nécessaires pour calculer le débit volumique.
  </div>
  {{mb_return}}
{{/if}}
<script type="text/javascript">
  Main.add(function() {
    var form = getForm("calculPerf");
    form.debit.addSpinner({min: 0});
    
    poids = $$(".poids_patient");
    volume = {{$line->_quantite_totale}};
    
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
    var rapport_unite_prise = {{$line_item->_ref_produit->rapport_unite_prise|@json}};
    var unite_administration = "{{$unite_administration}}";
    
    var form = getForm("calculPerf");
    var unite_choisie = $V(form.unite);
    
    // rapport_debit_choisi_ua
    var rapport_debit_choisi_ua = rapport_unite_prise[$V(form.unite)][unite_administration];
 
    // rapport_conditionnement_ua
    var rapport_conditionnement_ua = rapport_unite_prise["{{$line_item->unite}}"][unite_administration];
    
    // Rapport resultant des calculs précédents
    var rapport_quantite_necessaire = rapport_debit_choisi_ua / rapport_conditionnement_ua;
    
    var debit_choisi = $V(form.debit);
    
    {{if $line->type == "classique"}}
      var duree = parseFloat($("result_duree").innerHTML);
      
      // Calcul du débit nécessaire
      var debit_necessaire = debit_choisi;
      
      if (/\/kg/.test(unite_choisie)) {
        debit_necessaire *= poids;
      }
      
      $("debit_necessaire").update(debit_necessaire + " " + unite_choisie.replace(/\/kg \(.*\)/, ""));
      
      // Calcul de la quantité nécessaire
      var quantite_necessaire = debit_choisi * rapport_quantite_necessaire * duree;
      
      if (/\/kg/.test(unite_choisie)) {
        quantite_necessaire *= poids;
      }
            
      $("quantite_necessaire").update(quantite_necessaire);
      
      // Calcul du volume de référence
      var volume_total = volume;
      
      if (unite_administration == "ml") {
        volume_total = Math.round((volume_total + quantite_necessaire * rapport_conditionnement_ua) * 100) / 100;
      }
      $("volume_ref").update(volume_total);
      
    {{else}}
      var quantite_choisie = parseFloat($("quantite_choisie").innerHTML);
      
      if (/\/kg/.test(unite_choisie)) {
        quantite_choisie /= poids;
      }
      
      // Calcul de la durée
      var duree = Math.round(quantite_choisie * rapport_quantite_necessaire / debit_choisi * 100) / 100;
      
      if (duree == "Infinity") {
        duree = "-";
      }
      
      $("result_duree").update("<strong>"+duree+"</strong>");
      
      var volume_total = volume;
    {{/if}}
    
    // Calcul du débit volumique
    var debit_volumique = Math.round(volume_total / duree * 100) / 100;
    
    if (isNaN(debit_volumique)) {
      debit_volumique = "-";
    }
    $("debit_volumique").update(debit_volumique);
  }
  
  setValuesClassique = function() {
    var form = getForm("calculPerf");
    var formLineItem = getForm("editLinePerf-{{$line_item->_id}}")
    var formPerf = getForm("editPerf-{{$line->_id}}");
    var formQteTotale = getForm("editQuantiteTotale-{{$line->_id}}");
    var quantite = parseFloat($("quantite_necessaire").innerHTML);
    
    $V(formLineItem.quantite, quantite, false);
    
    onSubmitFormAjax(formLineItem, function() {
      Prescription.updateVolumeTotal('{{$line->_id}}', 1, null, null, null, function() {
        $V(formPerf.volume_debit, $V(formQteTotale._quantite_totale));
      });
      Control.Modal.close();
    });
  }
  
  setValuesOther = function() {
    var form = getForm("calculPerf");
    var duree = $("result_duree").down("strong").innerHTML;
    var formQteTotale = getForm("editQuantiteTotale-{{$line->_id}}");
    var formPerf = getForm("editPerf-{{$line->_id}}");
    
    Prescription.updateVolumeTotal('{{$line->_id}}', 1, null, null, null, function() {
      $V(formPerf.volume_debit, $V(formQteTotale._quantite_totale), false);
      $V(formPerf.duree_debit, duree);
      Control.Modal.close();
    });
  }
</script>

<div class="small-info">
  {{if $line->type == "classique"}}
    Perfusion classique : <strong>Calcul de la quantité de produit et du volume de référence</strong>
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
            {{foreach from=$line_item->_unites_prise item=_unite}}
              <option value="{{$_unite}}" {{if $line_item->unite == $_unite}}selected{{/if}}>{{$_unite}}</option>
            {{/foreach}}
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
        <span id="quantite_{{if $line->type == "classique"}}necessaire{{else}}choisie{{/if}}">
          {{if $line_item->quantite}}
            {{$line_item->quantite}}
          {{else}}
            -
          {{/if}}
        </span>
        {{$line_item->unite|regex_replace:"/\(.*\)/":""}}
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
          {{else}}
            -
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