<!-- $Id$ -->

<table class="tbl">
  <tr>
    <th class="title" colspan="10">
      {{tr}}CSejour-back-affectations{{/tr}}
    </th>
  </tr>
  
  <tr>
    <th>{{mb_label class=CAffectation field=lit_id}}</th>
    <th>{{mb_label class=CAffectation field=entree}}</th>
    <th>{{mb_label class=CAffectation field=sortie}}</th>
    <th>{{mb_label class=CAffectation field=effectue}}</th>
  </tr>

  {{foreach from=$sejour->_ref_affectations item=_affectation}}
  <tr>
    <td>{{$_affectation->_ref_lit->_view}}</td>
    <td>{{mb_value object=$_affectation field=entree}}</td>
    <td>{{mb_value object=$_affectation field=sortie}}</td>
    <td>{{mb_value object=$_affectation field=effectue}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CAffectation.none{{/tr}}</td>
  </tr>
  {{/foreach}}
  
</table> 
