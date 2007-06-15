{{* $Id: tree_catalogues.tpl 1806 2007-04-11 16:28:47Z MyttO $
  $_catalogue : catalog which results to display hierarchically
*}}

{{assign var=level value=$_catalogue->_level+1}}
<tr class="catalogue-{{$level}}">
  <th colspan="10">
    {{$_catalogue->libelle}}
  </th>
</tr>

{{foreach from=$_catalogue->_ref_prescription_items item=_item}}
{{assign var=analyse value=$_item->_ref_examen_labo}}
<tr>
  <td>
    <img 
      src="images/icons/anteriorite.png" onclick="Anteriorite.viewItem({{$_item->_id}})" />
  </td>
  <td>{{$analyse->libelle}}</td>
  
  <td>
    {{$_item->resultat}}
  </td>

  {{if $analyse->type == "num"}}
  <td>{{$analyse->unite}}</td>
  <td>{{$analyse->min}} &ndash; {{$analyse->max}}</td>
  {{else}}
  <td colspan="2">{{mb_value object=$analyse field="type"}}</td>
  {{/if}}
  <td>{{$_item->date}}</td>
  <td class="text">
    {{$_item->commentaire|nl2br}}
  </td>
</tr>
{{/foreach}}

{{foreach from=$_catalogue->_ref_catalogues_labo item=_catalogue}}
{{include file="tree_resultats.tpl"}}
{{/foreach}}
