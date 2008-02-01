<script type="text/javascript">


function pageMain(){
  // Initialisation des onglets du menu
  new Control.Tabs('main_tab_group');
}
  
</script>

<table class="main">
  <tr>
    <th class="title">{{$mbProduit->libelle}}</th>
  </tr>
  <!-- Menu de la monographie du medicament -->
  <tr>
    <td colspan="2">
      <ul id="main_tab_group" class="control_tabs">
        <li><a href="#one" style="height: 35px;">Composition et aspect</a></li>
        <li><a href="#two" style="height: 35px;">Données cliniques</a></li>
        <li><a href="#three" style="height: 35px; width:120px">Propriétés pharmacologiques</a></li>
        <li><a href="#four" style="height: 35px; width:120px">Données pharmaceutiques</a></li>
        <li><a href="#five" style="height: 35px; width:150px">Données technico-réglementaires</a></li>
      </ul>
      <hr class="control_tabs" />    
    </td>
    
  </tr>
  
  <!-- Commposition et aspect -->
  <tbody id="one">
    <tr>
      <td>
        <div class="accordionMain" id="accordionComposition">
          <div id="Identite">
            <div id="IdentiteHeader" class="accordionTabTitleBar">
              Composition
            </div>
            <div id="IdentiteContent"  class="accordionTabContentBox">
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
              </table>
              <table class="tbl">
                <tr>
                  <th class="title">Commentaires sur le composition</th>
                </tr>
                <td class="text">
                 {{$mbProduit->_ref_composition->exprime_par|smarty:nodefaults}}
                </td>
              </table>
              <table class="tbl">
                <tr>
                  <th colspan="2" class="title">Excipients</th>
                </tr>
                <tr>
                  <th>Libelle</th>
                  <th>Commentaire</th>
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
                  <td>{{$excipient->Commentaire}}</td>
                </tr>
                {{/foreach}} 
              </table>
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Aspect
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->aspect_forme|smarty:nodefaults}}
            </div>
          </div>
        </div>
      </td>
    </tr>
  </tbody>

  <!-- Données cliniques -->
  <tbody id="two">  
    <tr>
      <td>
        <div class="accordionMain" id="accordionClinique">
          <div id="Identite">
            <div id="IdentiteHeader" class="accordionTabTitleBar">
              Indications thérapeutiques
            </div>
            <div id="IdentiteContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->indications|smarty:nodefaults}}
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Posologie et mode d'administration
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
               {{$mbProduit->_ref_monographie->posologie|smarty:nodefaults}}
            </div>
            </div>
          <div id="Corresp">
            <div id="CorrespHeader" class="accordionTabTitleBar">
              Contre-indications
            </div>
            <div id="CorrespContent"  class="accordionTabContentBox">
             {{$mbProduit->_ref_monographie->contre_indications|smarty:nodefaults}}
            </div>
          </div>
          <div id="Assure">
            <div id="AssureHeader" class="accordionTabTitleBar">
              Mises en garde et précautions d'emploi
            </div>
            <div id="AssureContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->precautions_emploi|smarty:nodefaults}}
            </div>
          </div>
          <div id="Corresp">
            <div id="CorrespHeader" class="accordionTabTitleBar">
              Interactions avec d'autres médicaments et autres formes d'intéractions
            </div>
            <div id="CorrespContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->interactions|smarty:nodefaults}}
            </div>
          </div>
          <div id="Corresp">
            <div id="CorrespHeader" class="accordionTabTitleBar">
              Grossesse et allaitement
            </div>
            <div id="CorrespContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->grossesse_allaitement|smarty:nodefaults}}
            </div>
          </div>
          <div id="Corresp">
            <div id="CorrespHeader" class="accordionTabTitleBar">
              Effet sur l'aptitude à conduire des véhicules et à utiliser des machines
            </div>
            <div id="CorrespContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->effets_aptitude|smarty:nodefaults}}
            </div>
          </div>
          <div id="Corresp">
            <div id="CorrespHeader" class="accordionTabTitleBar">
               Effets indésirables
            </div>
            <div id="CorrespContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->effets_indesirables|smarty:nodefaults}}
            </div>
          </div>
          <div id="Corresp">
            <div id="CorrespHeader" class="accordionTabTitleBar">
              Surdosage
            </div>
            <div id="CorrespContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->surdosage|smarty:nodefaults}}
            </div>
          </div>
        </div>
      </td>
    </tr>
  </tbody>

  <!-- Propriétés pharmacologiques -->
  <tbody id="three">
    <tr>
      <td>
        <div class="accordionMain" id="accordionPharmaco">
          <div id="Identite">
            <div id="IdentiteHeader" class="accordionTabTitleBar">
              Classification thérapeutique  
            </div>
            <div id="IdentiteContent"  class="accordionTabContentBox">
              <table class="tbl">
                <tr>
                  <th>Classification ATC</th>
                </tr>
                <!-- Parcours des classes ATC du produit -->
                {{foreach from=$mbProduit->_ref_classes_ATC item=classeATC name=classesATC}}
                
                  <!-- Initialisation du compteur -->
                  {{counter start=0 skip=2 assign="compteur"}}
                  {{foreach from=$classeATC->classes item=classe}}
                    {{if $classe.libelle}}
                    <tr>
                      <td>
                        {{$tabEspace.$compteur|smarty:nodefaults}}
                        <img src="./images/icons/dotgrey.gif" alt="" title="" />
                        {{$classe.libelle}} ({{$classe.code}})
                      </td>
                    </tr>
                    {{/if}}
                    {{counter}}
                  {{/foreach}}
                  
									{{if !$smarty.foreach.classesATC.last}}
									<tr>
									<td>
									&nbsp;
									</td>
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
                  {{foreach from=$classeThera->classes item=classe}}
                    {{if $classe.libelle}}
                    <tr>
                      <td>
                        {{$tabEspace.$compteur|smarty:nodefaults}} 
                        <img src="./images/icons/dotgrey.gif" alt="" title="" />
                        {{$classe.libelle}} ({{$classe.code}})
                      </td>
                    </tr>
                    {{/if}}
                    {{counter}}
                  {{/foreach}}
                  
									{{if !$smarty.foreach.classesThera.last}}
									<tr>
									<td>
									&nbsp;
									</td>
									</tr>
									{{/if}}
                {{/foreach}}
                
              </table>
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Propriétés pharmacodynamiques
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->pharmacodynamie|smarty:nodefaults}}
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Propriétés pharmacocinétiques
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->pharmacocinetique|smarty:nodefaults}}
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Données de sécurité précliniques
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->securite_preclinique|smarty:nodefaults}}
            </div>
          </div>
        </div>
      </td>
    </tr>
  </tbody>

  <!-- Données pharmaceutiques -->
  <tbody id="four">
    <tr>
      <td>
        <div class="accordionMain" id="accordionPharma">
          <div id="Identite">
            <div id="IdentiteHeader" class="accordionTabTitleBar">
              Incompatibilités 
            </div>
            <div id="IdentiteContent"  class="accordionTabContentBox">
               {{$mbProduit->_ref_monographie->incompatibilite|smarty:nodefaults}}
              <!-- Affichage de la composition du medicaments -->
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Durée et précautions particulières de conservation
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->conservation|smarty:nodefaults}}
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Nature et contenu de l'emballage extérieur
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->emballage_ext|smarty:nodefaults}}
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Instructions pour l'utilisation, la manipulation et l'élimination
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->instruction_manipulation|smarty:nodefaults}}
            </div>
          </div>
        </div>
      </td>
    </tr>
  </tbody>
  
  <tbody id="five">
    <tr>
      <td>
        <table class="tbl">
          <tr>
            <th colspan="2">Données technico-réglementaires</th>
          </tr>
          <tr>
            <td>Titulaire de l'AMM</th>
            <td>{{$mbProduit->_ref_economique->laboratoire}}</td>
          </tr>
          <tr>
            <td>Laboratoire exploitant</th>
            <td>{{$mbProduit->_ref_economique->labo_exploitant}}</td>
          </tr>
          <tr>
            <td>Prix de vente TTC</th>
            <td>
            {{if $mbProduit->_ref_economique->prix_vente != "0000000"}}
              {{$mbProduit->_ref_economique->prix_vente}} &euro;
            {{/if}}
            </td>
          </tr>
          <tr>
            <td>Taux de TVA</th>
            <td>{{$mbProduit->_ref_economique->taux_tva}} %</td>
          </tr>
          <tr>
            <td>Taux de remboursement SS</td>
            <td>{{$mbProduit->_ref_economique->taux_ss}} %</td>
          </tr>
          <tr>
            <td>Code AMM</th>
            <td>{{$mbProduit->numero_AMM}}</td>
          </tr>
          <tr>
            <td>Code UCD</th>
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
            <td colspan="2">{{$mbProduit->_ref_monographie->condition_delivrance|smarty:nodefaults}}</td>
          </tr>
        </table>
      </td>
    </tr>
  </tbody>
     
</table>

<script language="Javascript" type="text/javascript">
var oAccordComposition = new Rico.Accordion( $('accordionComposition'), { 
  panelHeight: 380,
  showDelay: 50, 
  showSteps: 3 
} );

var oAccordClinique = new Rico.Accordion( $('accordionClinique'), { 
  panelHeight: 380,
  showDelay: 50, 
  showSteps: 3 
} );

var oAccordPharmaco = new Rico.Accordion( $('accordionPharmaco'), { 
  panelHeight: 380,
  showDelay: 50, 
  showSteps: 3 
} );

var oAccordPharma = new Rico.Accordion( $('accordionPharma'), { 
  panelHeight: 380,
  showDelay: 50, 
  showSteps: 3 
} );
</script>

