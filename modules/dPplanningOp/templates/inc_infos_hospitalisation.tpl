<!-- $Id: $ -->

<table class="tbl">
  <tr>
    <th class="title" colspan="3">
      Informations sur les affectations d'hospitalisation
    </th>
  </tr>
  
  <tr>
    <th>{{tr}}CLit-nom{{/tr}}</th>
    <th>{{tr}}CAffectation-entree-court{{/tr}}</th>
    <th>{{tr}}CAffectation-sortie-court{{/tr}}</th>
  </tr>

  {{foreach from=$sejour->_ref_affectations item=curr_affectation}}
  <tr>
    <td>{{$curr_affectation->_ref_lit->_view}}</td>
    <td>{{$curr_affectation->entree|date_format:"%a %d %b %Y à %Hh%M"}}</td>
    <td>{{$curr_affectation->sortie|date_format:"%a %d %b %Y à %Hh%M"}}</td>
  </tr>
  {{/foreach}}

</table> 

