{{assign var="sejour" value=$object}}

<table class="form">
  <tr>
    <th class="title" colspan="2">
      {{$object->_view}}
    </th>
  </tr>

  {{if $sejour->annule == 1}}
  <tr>
    <th class="category" colspan="4" style="background: #f00;">
    SEJOUR ANNULE
    </th>
  </tr>
  {{/if}}
  
  <tr>
    <td>
      <strong>Etablissement :</strong>
      {{$object->_ref_group->_view}}
    </td>
    <td>
      <strong>Praticien :</strong>
      <i>{{$object->_ref_praticien->_view}}</i>
    </td>
  </tr>

  {{if $object->rques}}
  <tr>
    <td class="text" colspan="2">
      <strong>Remarques :</strong>
      {{$object->rques|nl2br}}
    </td>
  </tr>
  {{/if}}

  <tr>
    <td>
      <strong>Entrée prévue :</strong>
      {{mb_value object=$sejour field="entree_prevue"}}
    </td>
    <td>
      <strong>Entrée reelle :</strong>
      {{mb_value object=$sejour field="entree_reelle"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>Sortie prévue :</strong>
      {{mb_value object=$sejour field="sortie_prevue"}}
    </td>
    <td>
      <strong>Sortie reelle :</strong>
      {{mb_value object=$sejour field="sortie_reelle"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>Durée prévue :</strong>
      {{$sejour->_duree_prevue}} jour(s)
    </td>
    <td>
      <strong>Sortie reelle :</strong>
      {{$sejour->_duree_reelle}} jour(s)
    </td>
  </tr>
  
  <tr>
    <th class="category" colspan="2">Hospitalisation</th>
  </tr>
  
  <tr>
    <td>
      <strong>Type d'admission</strong>
      {{mb_value object=$sejour field="type"}}
    </td>
    <td>
      <strong>Modalité</strong>:
      {{mb_value object=$sejour field="modalite"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>ATNC :</strong>
      {{mb_value object=$sejour field="ATNC"}}
    </td>
    <td>
      <strong>Traitement hormonal :</strong>
      {{mb_value object=$sejour field="hormone_croissance"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>Chambre particulière :</strong>
      {{mb_value object=$sejour field="chambre_seule"}}
    </td>
    <td>
      <strong>Lit accompagnant :</strong>
      {{mb_value object=$sejour field="lit_accompagnant"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>Repas sans sel :</strong>
      {{mb_value object=$sejour field="repas_sans_sel"}}
    </td>
    <td>
      <strong>Isolement :</strong>
      {{mb_value object=$sejour field="isolement"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>Repas diabétique :</strong>
      {{mb_value object=$sejour field="repas_diabete"}}
    </td>
    <td>
      <strong>Télévision :</strong>
      {{mb_value object=$sejour field="television"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>Repas sans résidu :</strong>
      {{mb_value object=$sejour field="repas_sans_residu"}}
    </td>
  </tr>

</table>

{{include file="../../dPplanningOp/templates/inc_infos_operation.tpl"}}
{{include file="../../dPplanningOp/templates/inc_infos_hospitalisation.tpl"}}
