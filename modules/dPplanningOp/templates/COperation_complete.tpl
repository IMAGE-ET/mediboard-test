{{assign var="operation" value=$object}}

<table class="form">

  <tr>
    <th class="title" colspan="2">
      <a style="float:right;" href="#nothing" onclick="view_log('{{$object->_class_name}}', {{$object->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="{{tr}}History.desc{{/tr}}" />
      </a>

      {{if $canSante400->read}}
      <a style="float:right;" href="#" onclick="view_idsante400('{{$object->_class_name}}',{{$object->_id}})">
        <img src="images/icons/sante400.gif" alt="Sante400" title="Identifiant sante 400"/>
      </a>
      {{/if}}

      <div style="float:left;" class="noteDiv {{$object->_class_name}}-{{$object->_id}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>
      {{$operation->_view}}
    </th>
  </tr>
  
  {{if $operation->annulee == 1}}
  <tr>
    <th class="category cancelled" colspan="4">
    {{tr}}COperation-annulee{{/tr}}
    </th>
  </tr>
  {{/if}}

  <tr>
    <td>
      <strong>{{tr}}COperation-chir_id-court{{/tr}} :</strong>
      {{$operation->_ref_chir->_view}}
    </td>
    <td>
      <strong>{{tr}}COperation-anesth_id-court{{/tr}} :</strong>
      {{$operation->_ref_anesth->_view}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-date-court{{/tr}} :</strong>
      {{$operation->_datetime|date_format:"%d %B %Y"}}
    </td>
    <td>
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-libelle{{/tr}} :</strong>
      {{$operation->libelle}}
    </td>
    <td>
      <strong>{{tr}}COperation-cote{{/tr}} :</strong>
      {{tr}}{{$operation->cote}}{{/tr}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-_lu_type_anesth{{/tr}} :</strong>
      {{$operation->_lu_type_anesth}}
    </td>
  </tr>
  
  {{if $operation->materiel}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{tr}}COperation-materiel-court{{/tr}} :</strong>
      {{$operation->materiel|nl2br}}
    </td>
  </tr>
  {{/if}}

  {{if $operation->rques}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{tr}}COperation-rques-court{{/tr}} :</strong>
      {{$operation->rques|nl2br}}
    </td>
  </tr>
  {{/if}}

  {{if $operation->examen}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{tr}}COperation-examen{{/tr}} :</strong>
      {{$operation->examen|nl2br}}
    </td>
  </tr>
  {{/if}}

  <tr>
    <th class="category" colspan="2">{{tr}}COperation-_ext_codes_ccam{{/tr}}</th>
  </tr>
  {{foreach from=$operation->_ext_codes_ccam item=currCode}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{$currCode->code}}</strong> :
      {{$currCode->libelleLong}}
    </td>
  </tr>
  {{/foreach}}
  
  <tr>
    <th class="category" colspan="2">{{tr}}COperation-_ref_actes_ccam{{/tr}}</th>
  </tr>
  
  {{foreach from=$operation->_ref_actes_ccam item=curr_acte}}
  <tr>
    <td>
      <strong>{{$curr_acte->_view}}</strong>
      par {{$curr_acte->_ref_executant->_view}} 
    </td>
    <td>
      {{$curr_acte->commentaire}}
    </td>
  </tr>
  {{/foreach}}
  
  <tr>
    <th class="category" colspan="2">{{tr}}msg-COperation-horodatage{{/tr}}</th>
  </tr>
  
  <tr>
    <td>
      <strong>{{tr}}COperation-entree_salle{{/tr}} :</strong>
      {{$operation->entree_salle}} 
    </td>
    <td>
      <strong>{{tr}}COperation-sortie_salle{{/tr}} :</strong>
      {{$operation->sortie_salle}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-debut_op{{/tr}} :</strong>
      {{$operation->debut_op}} 
    </td>
    <td>
      <strong>{{tr}}COperation-fin_op{{/tr}} :</strong>
      {{$operation->fin_op}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-pose_garrot{{/tr}} :</strong>
      {{$operation->pose_garrot}} 
    </td>
    <td>
      <strong>{{tr}}COperation-retrait_garrot{{/tr}} :</strong>
      {{$operation->retrait_garrot}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-induction_debut{{/tr}} :</strong>
      {{$operation->induction_debut}} 
    </td>
    <td>
      <strong>{{tr}}COperation-induction_fin{{/tr}} :</strong>
      {{$operation->induction_fin}} 
    </td>
  </tr>

</table>