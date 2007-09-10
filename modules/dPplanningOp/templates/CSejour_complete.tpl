{{assign var="sejour" value=$object}}

<table class="form">
  <tr>
    <th class="title" colspan="2">

	  <div class="idsante400" id="{{$object->_class_name}}-{{$object->_id}}"></div>

      <a style="float:right;" href="#nothing" onclick="view_log('{{$object->_class_name}}', {{$object->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="{{tr}}History.desc{{/tr}}" />
      </a>

      <div style="float:left;" class="noteDiv {{$object->_class_name}}-{{$object->_id}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>
      {{$object->_view}} {{if $sejour->_num_dossier}}[{{$sejour->_num_dossier}}]{{/if}}
    </th>
  </tr>

  {{if $sejour->annule == 1}}
  <tr>
    <th class="category cancelled" colspan="4">
    {{tr}}CSejour-annule{{/tr}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
    <td>
      <strong>{{tr}}CSejour-group_id{{/tr}} :</strong>
      {{$object->_ref_group->_view}}
    </td>
    <td>
      <strong>{{tr}}CSejour-praticien_id{{/tr}} :</strong>
      <i>{{$object->_ref_praticien->_view}}</i>
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}CSejour-_date_entree_prevue{{/tr}} :</strong>
      {{mb_value object=$sejour field="entree_prevue"}}
    </td>
    <td>
      <strong>{{tr}}CSejour-entree_reelle{{/tr}} :</strong>
      {{mb_value object=$sejour field="entree_reelle"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}CSejour-_date_sortie_prevue{{/tr}} :</strong>
      {{mb_value object=$sejour field="sortie_prevue"}}
    </td>
    <td>
      <strong>{{tr}}CSejour-sortie_reelle{{/tr}} :</strong>
      {{mb_value object=$sejour field="sortie_reelle"}}
    </td>
  </tr>
  <tr>
    <td>
      <strong>{{tr}}CSejour-_duree_prevue{{/tr}} :</strong>
      {{$sejour->_duree_prevue}} jour(s)
    </td>
    <td>
      <strong>{{tr}}CSejour-_duree_reelle{{/tr}} :</strong>
      {{$sejour->_duree_reelle}} jour(s)
    </td>
  </tr>
  <tr>
    <td>
    {{if $sejour->mode_sortie != null}}
      <strong>{{tr}}CSejour-mode_sortie{{/tr}}:</strong>
      <i>{{tr}}CAffectation._mode_sortie.{{$sejour->mode_sortie}}{{/tr}}</i>
      <br />
    {{/if}}
    <td>
  </tr>
  
  {{if $object->rques}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{tr}}CSejour-rques-court{{/tr}} :</strong>
      {{$object->rques|nl2br}}
    </td>
  </tr>
  {{/if}}

  {{if $object->convalescence}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{tr}}CSejour-convalescence{{/tr}} :</strong>
      {{$object->convalescence|nl2br}}
    </td>
  </tr>
  {{/if}}

  <tr>
    <th class="category" colspan="2">{{tr}}msg-CSejour-hospi{{/tr}}</th>
  </tr>
  
  <tr>
    <td>
      <strong>{{tr}}CSejour-type{{/tr}}</strong>
      {{mb_value object=$sejour field="type"}}
    </td>
    <td>
      <strong>{{tr}}CSejour-modalite-court{{/tr}}</strong>:
      {{mb_value object=$sejour field="modalite"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}CSejour-{{/tr}} :</strong>
      {{mb_value object=$sejour field="ATNC"}}
    </td>
    <td>
      <strong>{{tr}}CSejour-hormone_croissance{{/tr}} :</strong>
      {{mb_value object=$sejour field="hormone_croissance"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}CSejour-chambre_seule{{/tr}} :</strong>
      {{mb_value object=$sejour field="chambre_seule"}}
    </td>
    <td>
      <strong>{{tr}}CSejour-lit_accompagnant{{/tr}} :</strong>
      {{mb_value object=$sejour field="lit_accompagnant"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}CSejour-repas_sans_sel{{/tr}} :</strong>
      {{mb_value object=$sejour field="repas_sans_sel"}}
    </td>
    <td>
      <strong>{{tr}}CSejour-isolement{{/tr}} :</strong>
      {{mb_value object=$sejour field="isolement"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}CSejour-repas_diabete{{/tr}} :</strong>
      {{mb_value object=$sejour field="repas_diabete"}}
    </td>
    <td>
      <strong>{{tr}}CSejour-television{{/tr}} :</strong>
      {{mb_value object=$sejour field="television"}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}CSejour-repas_sans_residu{{/tr}} :</strong>
      {{mb_value object=$sejour field="repas_sans_residu"}}
    </td>
  </tr>
  
  
  <table class="form">
  {{assign var="vue" value="complete"}}
  {{assign var="subject" value=$sejour}}
  {{include file="../../dPcabinet/templates/inc_list_actes.tpl"}}
  </table>


</table>

{{include file="../../dPplanningOp/templates/inc_infos_operation.tpl"}}
{{include file="../../dPplanningOp/templates/inc_infos_hospitalisation.tpl"}}
