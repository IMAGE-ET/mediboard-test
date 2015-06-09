{{mb_script module=dPccam script=CCodageCCAM ajax=true}}
<div id="affichage_ccam_informations_generales" style="display: none">
  <table class="tbl">
      <th style="width: 20%;">Dernière mise<br />à jour</th>
      <td class="text">{{$date_versions[0]}}</td>
    </tr>
    <tr>
      <th>Place dans la CCAM</th>
      <td class="text">{{$code_complet->place}}</td>
    </tr>
    <tr>
      <th>Code de regroupement</th>
      {{foreach name=first from=$code_complet->_ref_code_ccam->_ref_activites[1]->_ref_classif item=_classif}}
        {{if $smarty.foreach.first.first}}
          <td>{{$_classif->_regroupement}} ({{$_classif->code_regroupement}})</td>
        {{/if}}
      {{/foreach}}
    </tr>
    <tr>
      <th>Remarques</th>
      {{foreach from=$code_complet->remarques item=_remarque}}
          <td>{{$_remarque}}</td>
      {{/foreach}}
    </tr>
  </table>
  <table class="tbl">
    {{foreach from=$code_complet->activites item=_activite key=_key}}
      <tr>
        <th class="category" style="width: 50%;">Activité {{$_key}}</th>
      </tr>
      {{foreach from=$_activite->phases item=_phase}}
        <tr>
          <td style="text-align: center;">Phase {{$_phase->phase}} ({{$_phase->libelle}})</td>
        </tr>
      {{/foreach}}
      <tr>
        <th class="section">Modificateurs</th>
      </tr>
      {{foreach from=$_activite->modificateurs item=_modificateur}}
        <tr>
          <td class="text">
            <ul>
              <li>
                {{$_modificateur->code}} : {{$_modificateur->libelle}}
              </li>
            </ul>
          </td>
        </tr>
      {{/foreach}}
      <tr>
        <th class="section">Prix de l'acte</th>
      </tr>
      <tr>
        <td><p style="text-align: center;">En euros : {{$_activite->phases[0]->tarif}}</p></td>
      </tr>
    {{/foreach}}

  </table>
</div>

<div id="affichage_ccam_prise_en_charge" style="display: none">
  <table class="tbl">
    <tr>
      <th>Note :</th>
      {{foreach from=$code_complet->_ref_code_ccam->_ref_notes item=_note}}
        <td>{{$_note->texte}}</td>
      {{/foreach}}
    </tr>
    <tr>
      <th>Remboursement :</th>
      {{if $code_complet->remboursement == 1}}
        <td>Remboursable</td>
      {{else}}
        <td>Non remboursable</td>
      {{/if}}
    </tr>
    <tr>
      <th>Exonération du ticket modérateur :</th>
      {{foreach name=infotarif from=$code_complet->_ref_code_ccam->_ref_infotarif item=_ref_infotarif}}
        {{if $smarty.foreach.infotarif.first}}
          {{foreach name=first from=$_ref_infotarif->code_exo item=_code_exo}}
            {{if $smarty.foreach.first.first}}
              <td>{{$_code_exo.libelle}}</td>
            {{/if}}
          {{/foreach}}
        {{/if}}
      {{/foreach}}
    </tr>
  </table>
</div>

<div id="affichage_ccam_associations" style="display: none">
  <table class="tbl">
    <tr>
      <th class="category" colspan="2">Type d'acte : {{$code_complet->_ref_code_ccam->_type_acte}}</th>
    </tr>
    {{foreach name=first from=$code_complet->activites item=_activite key=_key}}
      {{if $_activite->assos|@count > 0}}
        {{assign var=nbAssociations value=2}}
        <tr>
          <th class="section" colspan="2">Activité {{$_key}}</th>
        </tr>
        {{foreach from=$_activite->assos item=_asso key=_key_asso}}
          {{if $nbAssociations is div by 2}}
            <tr>
          {{/if}}
          <td class="text" class="section" style="width: 50%;">
            <strong>
              <a onclick="CCodageCCAM.refreshModal('{{$_key_asso}}');" href="#">
                {{$_key_asso}}
              </a>
            </strong>
            {{$_asso.texte}}
          </td>
          {{assign var=nbAssociations value=$nbAssociations+1}}
          {{if $nbAssociations is div by 2}}
            </tr>
          {{/if}}
        {{/foreach}}
      {{else}}
        <tr>
          <td><p style="text-align:center;">Pas d'associations pour l'activité {{$_key}}</p></td>
        </tr>
      {{/if}}
    {{/foreach}}
  </table>
</div>

<div id="affichage_ccam_actes_voisins" style="display: none">
  <table class="tbl">
    {{foreach from=$acte_voisins item=_acte key=_key}}
      {{if $_key is div by 2}}
        <tr>
      {{/if}}
      <td class="text" style="width:50%;">
        <strong>
          <a onclick="CCodageCCAM.refreshModal('{{$_acte->code}}');" href="#">
            {{$_acte->code}}
          </a>
        </strong>
        {{$_acte->libelleLong}}
      </td>
      {{if ($_key+1) is div by 2 or ($_key+1) == $acte_voisins|@count}}
        </tr>
      {{/if}}
    {{/foreach}}
  </table>
</div>

<div id="affichage_ccam_incompatibilites" style="display: none">
  <table class="tbl">
    {{assign var=number_incompatibilite value=2}}
    {{foreach from=$code_complet->incomps item=_incomp key=_key}}
      {{if $number_incompatibilite is div by 2}}
        <tr>
      {{/if}}
      <td class="text" style="width:50%;">
        <strong>
          <a onclick="CCodageCCAM.refreshModal('{{$_incomp.code}}');" href="#">
            {{$_incomp.code}}
          </a>
        </strong>
        {{$_incomp.texte}}
      </td>
      {{assign var=number_incompatibilite value=$number_incompatibilite+1}}
      {{if $number_incompatibilite is div by 2}}
        </tr>
      {{/if}}
    {{/foreach}}
  </table>
</div>
