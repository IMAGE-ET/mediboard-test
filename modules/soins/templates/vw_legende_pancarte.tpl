{{assign var=specs_chapitre value=$prescription->_specs._chapitres}}
{{assign var=images value="CPrescription"|static:"images"}}

<table class="tbl">
  <tr>
    <th colspan="2">Légende</th>
  </tr>
  {{foreach from=$specs_chapitre->_list item=_chapitre}}
  <tr>
    <td style="width: 1%;"><img src="{{$images.$_chapitre}}" /></td>
    <td>{{tr}}CPrescription._chapitres.{{$_chapitre}}{{/tr}}</td>
  </tr>
  {{/foreach}}
  <tr>
    <th colspan="2">
      Couleurs possibles
    </th>
  </tr>
  <tr>
    <td  style="background-color: #B2FF9B; height: 100%;">
    
    </td>
    <td>
      Entièrement effectué
    </td>
  </tr>
  <tr>
    <td style="background-color: #FB4; height: 100%;">
      
    </td>
    <td>
      Partiellement ou pas effectué
    </td>
  </tr>
</table>