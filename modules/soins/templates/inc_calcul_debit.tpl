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


{{if $line->type != "classique" && !$line_item->quantite}}
  <div class="small-warning">
    La quantité est nécessaire pour calculer le débit volumique.
  </div>
  {{mb_return}}
{{/if}}

<script>
  Main.add(function() {
    var form = getForm("calculPerf");
    form.quantite_debit.addSpinner({min: 0});
    
    limitSelect(form.temps_debit);
    
    poids = $$(".poids_patient");
    taille = $$(".taille_patient");
    volume = {{$line->_quantite_totale}};
    
    if (poids.length) {
      poids = parseInt(poids[0].down("span").innerHTML);
      $("view_poids").update("<strong>"+poids+" kg</strong>");
      $V(form._poids, poids);
    }
    else {
      $("calcul_perf_poids").hide();
      poids = 0;
      
      /*$A(form.unite_debit.options).each(function(elt) {
        if (/\/kg/.test(elt.innerHTML)) {
          elt.writeAttribute("disabled");
        }
      });*/
    }

    if (taille.length) {
      taille = parseInt(taille[0].innerHTML);
    }
    else {
      $("calcul_perf_poids").hide();
      taille = 0;
    }

    updateData();
  });
  
  updateData = function() {
    var rapport_unite_prise = {{$line_item->_ref_produit->rapport_unite_prise|@json}};
    var unite_administration = "{{$unite_administration}}";
    
    var form = getForm("calculPerf");
    var unite_choisie = $V(form.unite_debit);
    
    // Si le poids n'est pas renseigné et que l'on choisit une unité/kg, alors on cache
    // les infos de calcul
    
    if ((!poids && /\/kg/.test(unite_choisie)) || (!taille && !poids && /\/m2/.test(unite_choisie))) {
      $("calcul_debit_area").hide();
      {{if !$mode_protocole}}
        $("alert_poids").show();
      {{/if}}
      return;
    }
    
    $("calcul_debit_area").show();
    $("alert_poids").hide();
    
    var unite_temps_debit_select = form.unite_temps_debit;
    var unite_temps_debit = parseInt(unite_temps_debit_select.options[unite_temps_debit_select.selectedIndex].get("facteur"));
    var temps_debit = $V(form.temps_debit);
    
    // rapport_debit_choisi_ua
    var rapport_debit_choisi_ua = rapport_unite_prise[unite_choisie.replace(/\/(kg|m2)/g, "")][unite_administration.replace(/\/(kg|m2)/g, "")];

    // rapport_conditionnement_ua
    var rapport_conditionnement_ua = rapport_unite_prise["{{$line_item->unite}}".replace(/\/(kg|m2)/g, "")][unite_administration.replace(/\/(kg|m2)/g, "")];

    // Rapport resultant des calculs précédents
    var rapport_quantite_necessaire = rapport_debit_choisi_ua / rapport_conditionnement_ua;

    var debit_choisi = parseFloat($V(form.quantite_debit).replace(/,/, "."));
    
    debit_choisi *= unite_temps_debit;
    
    debit_choisi /= temps_debit;
    
    // Calcul du débit nécessaire
    var debit_necessaire = debit_choisi;
    
    if (/\/kg/.test(unite_choisie)) {
      debit_necessaire *= poids;
    }
    
    $("debit_necessaire").update((Math.round(debit_necessaire * 100) / 100) + " " + unite_choisie.replace(/\/(kg|m2) \(.*\)/, ""));
    
    {{if $line->type == "classique"}}
      var duree = parseFloat($("result_duree").innerHTML);
      
      // Calcul de la quantité nécessaire
      var quantite_necessaire = debit_choisi * rapport_quantite_necessaire * duree;
      
      if (/\/kg/.test(unite_choisie)) {
        quantite_necessaire *= poids;
      }
            
      $("quantite_necessaire").update(Math.round(quantite_necessaire * 100) / 100);
      
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
      var duree = Math.round(quantite_choisie / rapport_quantite_necessaire / debit_choisi * 100) / 100;
      
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
  };
  
  setValuesClassique = function() {
    var form = getForm("calculPerf");
    var unite_choisie = $V(form.unite_debit);
    
    onSubmitFormAjax(form, function() {
      var formLineItem = getForm("editLinePerf-{{$line_item->_id}}");
      var volume_ref = $("volume_ref").innerHTML;

      // Si on a choisi une unité/kg et que le poids du patient n'est pas dispo,
      // on ne peut que vider la quantité de produit et tagger la perfusion en sans poids
      if ((/\/kg/.test(unite_choisie) && !poids) || (/\/m2/.test(unite_choisie) && (!taille || !poids))) {
        formLineItem.quantite.onchange();
        tagLineSansTaillePoids();
        return;
      }

      // Sinon, réinitialiser les flags sans_poids et sans_taille car ils ont pu être mis à 1
      // (choix d'une unité/kg ou /m2 puis retour à une unité sans)
      restoreFlags();

      var formPerf = getForm("editPerf-{{$line->_id}}");
      var formQteTotale = getForm("editQuantiteTotale-{{$line->_id}}");
      var quantite = parseFloat($("quantite_necessaire").innerHTML);

      $V(formLineItem.quantite, quantite, false);
      onSubmitFormAjax(formLineItem, function() {
        Prescription.updateVolumeTotal('{{$line->_id}}', 1, null, null, null, function() {
          $V(formPerf.volume_debit, volume_ref);
          if (formPerf.volume_debit.readOnly) {
            formPerf.volume_debit.onchange();
          }
        });
        Control.Modal.close();
      });
    });
  };
  
  setValuesOther = function() {
    var form = getForm("calculPerf");
    var unite_choisie = $V(form.unite_debit);

    onSubmitFormAjax(form, function() {
      if ((/\/kg/.test(unite_choisie) && !poids) || (/\/m2/.test(unite_choisie) && (!taille || !poids))) {
        getForm("editLinePerf-{{$line_item->_id}}").quantite.onchange();
        tagLineSansTaillePoids();
        return;
      }

      restoreFlags();

      var duree = $("result_duree").down("strong").innerHTML;
      var formQteTotale = getForm("editQuantiteTotale-{{$line->_id}}");
      var formPerf = getForm("editPerf-{{$line->_id}}");

      Prescription.updateVolumeTotal('{{$line->_id}}', 1, null, null, null, function() {
        $V(formPerf.volume_debit, $V(formQteTotale._quantite_totale), false);
        $V(formPerf.duree_debit, duree, false);
        formPerf.duree_debit.onchange();
        Control.Modal.close();
      });
    });
  };
  
  limitSelect = function(elt) {
    var form = elt.form;
    var temps = form.temps_debit;
    var show = elt.options[elt.selectedIndex].innerHTML == "min";
    
    $A(temps.options).each(function(option) {
      if (option.value > 24) {
        if (show) {
          option.show();
        }
        else {
          option.hide();
        }
      }
    });
    
    if (!show && temps.value > 24) {
      temps.selectedIndex = 0;
    }
  };

  tagLineSansTaillePoids = function() {
    var formTag = getForm("tagSansPoidsTaille");

    if (!poids) {
      $V(formTag.sans_poids, 1);
    }
    if (!taille) {
      $V(formTag.sans_taille, 1);
    }
    onSubmitFormAjax(formTag, function() {
      Control.Modal.close();
    });
  };

  restoreFlags = function() {
    var formTag = getForm("tagSansPoidsTaille");
    $V(formTag.sans_taille, 0);
    $V(formTag.sans_poids, 0);
    onSubmitFormAjax(formTag);
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
  Le poids et/ou la taille du patient ne sont pas renseignés. Veuillez les renseigner pour générer le plan de soins.
</div>

<form name="tagSansPoidsTaille" method="post">
  <input type="hidden" name="m" value="prescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
  {{mb_key object=$line}}
  <input type="hidden" name="sans_poids" />
  <input type="hidden" name="sans_taille" />
</form>

<form name="calculPerf" method="post">
  <input type="hidden" name="m" value="prescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_mix_item_aed" />
  <input type="hidden" name="_poids" />
  {{mb_key object=$line_item}}
  {{mb_field object=$line_item field=quantite hidden=true}}
  
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
        <input type="number" name="quantite_debit" size="5" onchange="updateData()" value="{{$line_item->quantite_debit}}"/>
        <select name="unite_debit" onchange="updateData()" style="width: 13em;">
          {{foreach from=$line_item->_unites_prise item=_unite}}
            <option value="{{$_unite}}" {{if $line_item->unite_debit == $_unite}}selected{{/if}}>{{$_unite}}</option>
          {{/foreach}}
          {{if $line_item->unite_debit && !in_array($line_item->unite_debit, $line_item->_unites_prise)}}
            <option value="{{$line_item->unite}}" style="background-color: red;" selected>
              {{$line_item->_ref_produit->getLibelleUnite($line_item->unite_debit)}}
            </option>
          {{/if}}
        </select>
        /
        <select name="temps_debit" onchange="updateData()">
          {{foreach from=1|range:60 item=i}}
            <option value="{{$i}}" {{if $line_item->temps_debit == $i}}selected{{/if}}>{{$i}}</option>
          {{/foreach}}
        </select>
        <select name="unite_temps_debit" onchange="updateData(); limitSelect(this)">
          <option value="hour" {{if $line_item->unite_temps_debit == "hour"}}selected{{/if}} data-facteur="1" >h</option>
          <option value="min"  {{if $line_item->unite_temps_debit == "min" }}selected{{/if}} data-facteur="60">min</option>
        </select>
        {{if $line_item->unite_debit && !in_array($line_item->unite_debit, $line_item->_unites_prise)}}
          <div class="small-error text">
            L'unité de prise sélectionnée ({{$line_item->_ref_produit->getLibelleUnite($line_item->unite_debit)}}) n'est plus disponible dans la banque de médicaments, veuillez la modifier !
          </div>
        {{/if}}
      </td>
    </tr>
    <tr id="calcul_perf_poids">
      <th>Poids du patient</th>
      <td id="view_poids"></td>
    </tr>
    
    <tbody id="calcul_debit_area">
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
    </tbody>
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