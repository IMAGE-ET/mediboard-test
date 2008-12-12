<table>
  <tr>
    <th colspan="2" rowspan="2"/>
    <th colspan="{{$fiche->_enums.vraissemblance|@count}}">{{mb_label object=$fiche field=vraissemblance}}</th>
  </tr>
  <tr>
    {{foreach from=$fiche->_enums.vraissemblance item=vraissemblance}}
    <th>{{$vraissemblance}}</th>
    {{/foreach}}
  </tr>
  {{assign var=matrice value="CFicheEi"|static:"criticite_matrice"}}
  {{assign var=colors value=","|explode:"none,optimum,min,critical"}}
  {{foreach from=$fiche->_enums.gravite item=gravite name=gravite}}
    <tr>
      {{if $smarty.foreach.gravite.first}}
      <th rowspan="{{$fiche->_enums.gravite|@count}}" style="width: 0.1%; vertical-align: middle;">{{mb_label object=$fiche field=gravite}}</th>
      {{/if}}
      <th>{{$gravite}}</th>
      {{foreach from=$fiche->_enums.vraissemblance item=vraissemblance}}
        {{assign var=criticite value=$matrice.$gravite.$vraissemblance}}
        <td style="text-align: center;" class="{{$colors.$criticite}}">{{$criticite}}</td>
      {{/foreach}}
    </tr>
  {{/foreach}}
</table>