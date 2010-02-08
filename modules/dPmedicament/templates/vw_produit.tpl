{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $mbProduit->code_cip}}
<script type="text/javascript">
Main.add(function () {
  // Initialisation des onglets du menu
  var tabsProduit = Control.Tabs.create('tab-produit', false);
  var tabsClinique = Control.Tabs.create('tab-clinique', false);
  var tabsPharmaco = Control.Tabs.create('tab-pharmaco', false);
});
</script>
{{/if}}

{{if $produits|@count}}
    <form name="viewMonographie" method="get">
      <input type="hidden" name="m" value="dPmedicament" />
      <input type="hidden" name="a" value="vw_produit" />
      <input type="hidden" name="dialog" value="1" />
      <input type="hidden" name="code_cip" value="" />
      <input type="hidden" name="code_ucd" value="{{$code_ucd}}" />
      <input type="hidden" name="code_cis" value="{{$code_cis}}" />
    </form>
    <table class="main tbl">
    <tr>
      <th class="title" colspan="{{$produits|@count}}">
	      Produits disponibles pour le code
	      {{if $code_cis}}CIS {{$code_cis}}{{/if}}
	      {{if $code_ucd && !$code_cis}}UCD {{$code_ucd}}{{/if}}
      </th> 
    </tr>
    <tr>
	    <!-- Affichage des produits disponibles pour le code CIS/UCD selectionné -->
	    {{foreach from=$produits item=_produit}}
	      <td class="text" {{if $_produit->code_cip == $mbProduit->code_cip}}style="font-weight: bold;"{{/if}}><a href="#{{$_produit->code_cip}}" onclick="document.viewMonographie.code_cip.value = '{{$_produit->code_cip}}'; document.viewMonographie.submit();">{{$_produit->libelle}}</a></td>
	    {{/foreach}}
    </tr>
    </table>    
{{/if}}

{{if $mbProduit->code_cip}}
<table class="main">
  <tr>
    <th class="title" {{if $mbProduit->_ref_monographie->date_suppression}}style="background-color: red"{{/if}}>
    {{$mbProduit->libelle}}
    {{if $mbProduit->_ref_monographie->date_suppression}}
    <br />
    Produit supprimé depuis le {{$mbProduit->_ref_monographie->date_suppression}}
    {{/if}}
    </th>
  </tr>
  <!-- Menu de la monographie du medicament -->
  <tr>
    <td colspan="2">
      <ul id="tab-produit" class="control_tabs">
        <li><a href="#one">Composition et aspect</a></li>
        <li><a href="#two">Données cliniques</a></li>
        <li><a href="#three">Propriétés pharmacologiques</a></li>
        <li><a href="#four">Données pharmaceutiques</a></li>
        <li><a href="#five">Données technico-réglementaires</a></li>
      </ul>
      <hr class="control_tabs" />    
  
      <!-- Commposition et aspect -->
      <div id="one" style="display: none;">
        <!-- Affichage de la composition du medicaments -->
        <table class="tbl">
          <tr>
            <th colspan="3" class="title">Principes actifs</th>
          </tr>
          <tr>
            <th>Libelle</th>
            <th>Commentaire</th>
            <th>Quantite</th>
          </tr>
          {{foreach from=$mbProduit->_ref_composition->principes_actifs item=PA}}
          <tr>
            <td>{{$PA->Libelle}}</td>
            <td>{{$PA->Commentaire}}</td>
            <td>{{$PA->Quantite}}{{$PA->Unite}}</td>
          </tr>
          {{/foreach}} 
          <tr>
            <th class="title" colspan="3">Commentaires sur la composition</th>
          </tr>
          <tr>
            <td class="text" colspan="3">
             {{$mbProduit->_ref_composition->exprime_par|smarty:nodefaults}}
            </td>
          </tr>
          <tr>
            <th colspan="3" class="title">Excipients</th>
          </tr>
          <tr>
            <th>Libelle</th>
            <th colspan="2">Commentaire</th>
          </tr>
          {{foreach from=$mbProduit->_ref_composition->excipients item=excipient}}
          <tr>
            <td {{if $excipient->Notoire == -1}}style="color: red;"{{/if}}>
              {{if $excipient->Is_Info}}<strong>{{/if}}
              {{$excipient->Libelle}} 
              {{if $excipient->Is_Info}}</strong>{{/if}}
              {{if $excipient->Notoire == -1}}
                (Excipient à effet notoire)
              {{/if}}
            </td>
            <td colspan="2">{{$excipient->Commentaire}}</td>
          </tr>
          {{/foreach}} 
          <tr>
            <th class="title" colspan="3">Aspect</th>
          </tr>
          <tr>
            <td colspan="3">
              {{$mbProduit->_ref_monographie->aspect_forme|smarty:nodefaults}}
           </td>
          </tr>
        </table>
      </div>

      <!-- Données cliniques -->
      <div id="two" style="display: none;">
        <ul id="tab-clinique" class="control_tabs_vertical" style="width: 20em;">
          <li><a href="#indications">Indications thérapeutiques</a></li>
          <li><a href="#posologie">Posologie et mode d'administration</a></li>
          <li><a href="#contre_indications">Contre-indications</a></li>
          <li><a href="#precautions_emploi">Mises en garde et précautions d'emploi</a></li>
          <li><a href="#interactions">Interactions avec d'autres médicaments et autres formes d'intéractions</a></li>
          <li><a href="#grossesse_allaitement">Grossesse et allaitement</a></li>
          <li><a href="#effets_aptitude">Effet sur l'aptitude à conduire des véhicules et à utiliser des machines</a></li>
          <li><a href="#effets_indesirables">Effets indésirables</a></li>
          <li><a href="#surdosage">Surdosage</a></li>
        </ul>

        <div style="margin-left: 20.5em;">
          <div id="indications" style="display: none;">{{$mbProduit->_ref_monographie->indications|smarty:nodefaults}}</div>
          <div id="posologie" style="display: none;">{{$mbProduit->_ref_monographie->posologie|smarty:nodefaults}}</div>
          <div id="contre_indications" style="display: none;">{{$mbProduit->_ref_monographie->contre_indications|smarty:nodefaults}}</div>
          <div id="precautions_emploi" style="display: none;">{{$mbProduit->_ref_monographie->precautions_emploi|smarty:nodefaults}}</div>
          <div id="interactions" style="display: none;">{{$mbProduit->_ref_monographie->interactions|smarty:nodefaults}}</div>
          <div id="grossesse_allaitement" style="display: none;">{{$mbProduit->_ref_monographie->grossesse_allaitement|smarty:nodefaults}}</div>
          <div id="effets_aptitude" style="display: none;">{{$mbProduit->_ref_monographie->effets_aptitude|smarty:nodefaults}}</div>
          <div id="effets_indesirables" style="display: none;">{{$mbProduit->_ref_monographie->effets_indesirables|smarty:nodefaults}}</div>
          <div id="surdosage" style="display: none;">{{$mbProduit->_ref_monographie->surdosage|smarty:nodefaults}}</div>
        </div>
      </div>

      <!-- Propriétés pharmacologiques -->
      <div id="three" style="display: none;">
        <ul id="tab-pharmaco" class="control_tabs_vertical" style="width: 20em;">
          <li><a href="#classification">Classification thérapeutique</a></li>
          <li><a href="#pharmacodynamie">Propriétés pharmacodynamiques</a></li>
          <li><a href="#pharmacocinetique">Propriétés pharmacocinétiques</a></li>
          <li><a href="#securite_preclinique">Données de sécurité précliniques</a></li>
        </ul>

        <div style="margin-left: 20.5em;">
          <div id="classification" style="display: none;">
            <table class="tbl">
              <tr>
                <th>Classification ATC</th>
              </tr>
              <!-- Parcours des classes ATC du produit -->
              {{foreach from=$mbProduit->_ref_classes_ATC item=classeATC name=classesATC}}
              
                <!-- Initialisation du compteur -->
                {{counter start=0 skip=2 assign="compteur"}}
                {{foreach from=$classeATC->classes item=classe name="classes"}}
                  {{if $classe.libelle}}
                    <tr>
                      <td style="padding-left: {{$compteur}}em;">
                        <img src="./images/icons/dotgrey.gif" alt="" title="" />
                        {{$classe.libelle}} ({{$classe.code}})
                      </td>
                    </tr>
                    {{counter}}
                  {{/if}}
                {{/foreach}}
                
  							{{if !$smarty.foreach.classesATC.last}}
  							<tr>
  							  <td>&nbsp;</td>
  							</tr>
  							{{/if}}
              {{/foreach}}
              
              <tr>
                <th>Classification BCB</th>
              </tr>
              <!-- Parcours des classes ATC du produit -->
              {{foreach from=$mbProduit->_ref_classes_thera item=classeThera name=classesThera}}
              
                <!-- Initialisation du compteur -->
                {{counter start=0 skip=2 assign="compteur"}}
                {{foreach from=$classeThera->classes item=classe name=classes}}
                  {{if $classe.libelle}}
                    <tr>
                      <td style="padding-left: {{$compteur}}em;">
                        <img src="./images/icons/dotgrey.gif" alt="" title="" />
                        {{$classe.libelle}} ({{$classe.code}})
                      </td>
                    </tr>
                    {{counter}}
                  {{/if}}
                {{/foreach}}
                
  							{{if !$smarty.foreach.classesThera.last}}
  							<tr>
  							<td>&nbsp;</td>
  							</tr>
  							{{/if}}
              {{/foreach}}
              
            </table>
          </div>
            
          <div id="pharmacodynamie" style="display: none;">{{$mbProduit->_ref_monographie->pharmacodynamie|smarty:nodefaults}}</div>
          <div id="pharmacocinetique" style="display: none;">{{$mbProduit->_ref_monographie->pharmacocinetique|smarty:nodefaults}}</div>
          <div id="securite_preclinique" style="display: none;">{{$mbProduit->_ref_monographie->securite_preclinique|smarty:nodefaults}}</div>
        </div>
      </div>

      <!-- Données pharmaceutiques -->
      <div id="four" style="display: none;">
        <table class="tbl">
          <tr>
            <th>Incompatibilités</th>
          </tr>
          <tr>
            <td class="text">{{$mbProduit->_ref_monographie->incompatibilite|smarty:nodefaults}}</td>
          </tr>
          <tr>
            <th>Durée et précautions particulières de conservation</th>
          </tr>
          <tr>
            <td class="text">
              {{$mbProduit->_ref_monographie->conservation|smarty:nodefaults}}
            </td>
          </tr>
          <tr>
            <th>Nature et contenu de l'emballage extérieur</th>
          </tr>
          <tr>
            <td class="text">
              {{$mbProduit->_ref_monographie->emballage_ext|smarty:nodefaults}}
            </td>
          </tr>
          <tr>
            <th>Instructions pour l'utilisation, la manipulation et l'élimination</th>
          </tr>
          <tr>
            <td class="text">
              {{$mbProduit->_ref_monographie->instruction_manipulation|smarty:nodefaults}}
            </td>
          </tr>
        </table>
      </div>
      
      <div id="five" style="display: none;">
        <table class="tbl">
          <tr>
            <th colspan="2">Données technico-réglementaires</th>
          </tr>
          <tr>
            <th style="width: 50px;">Titulaire de l'AMM</th>
            <td>{{$mbProduit->_ref_economique->laboratoire}}</td>
          </tr>
          <tr>
            <th>Laboratoire exploitant</th>
            <td>{{$mbProduit->_ref_economique->labo_exploitant}}</td>
          </tr>
          <tr>
            <th>Prix de vente TTC</th>
            <td>
            {{if $mbProduit->_ref_economique->prix_vente != "0000000"}}
              {{$mbProduit->_ref_economique->prix_vente}} &euro;
            {{/if}}
            </td>
          </tr>
          <tr>
            <th>Taux de TVA</th>
            <td>{{$mbProduit->_ref_economique->taux_tva}} %</td>
          </tr>
          <tr>
            <th>Taux de remboursement SS</th>
            <td>{{$mbProduit->_ref_economique->taux_ss}} %</td>
          </tr>
          <tr>
            <th>Code AMM</th>
            <td>{{$mbProduit->numero_AMM}}</td>
          </tr>
          <tr>
            <th>Code UCD</th>
            <td>{{$mbProduit->_ref_economique->code_ucd}}</td>
          </tr>
          <tr>
            <th colspan="2">Statut</th>
          </tr>
          <tr>
            <td colspan="2">
              {{if $mbProduit->libelle_statut}}
                {{$mbProduit->libelle_statut}} du {{$mbProduit->date_AMM}}  
              {{/if}}
            </td>
          </tr>
          <tr>  
            <td colspan="2">
              <strong>Agrément collectivités : </strong>
              {{if $mbProduit->agrement}}
                Oui
              {{else}}
                Non
              {{/if}}        
            </td>
          </tr>
          <tr>
            <th colspan="2">Conditions de prescription et de délivrance</th>
          </tr>
          <tr>
            <td colspan="2" class="text">{{$mbProduit->_ref_monographie->condition_delivrance|smarty:nodefaults}}</td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>
{{elseif $produits|@count}}
  <div class="small-info">
    Veuillez sélectionner un médicament ci-dessus pour voir sa monographie.
  </div>
{{else}}
  <div class="small-info">
	  Monographie non disponible pour ce produit
	</div>
{{/if}}