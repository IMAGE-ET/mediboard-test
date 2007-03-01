<table class="tbl">
  <tr>
    <th>Objet</th>
    <th>Nombre d'enregistrement à '0'</th>
    <th>Nombre total d'enregistrement</th>
  </tr>
  {{assign var="styleColorConflit" value="style=\"background-color:#fc0;\""}}
        
  {{foreach from=$aChamps key=keyClass item=currClass}}
  {{if $currClass|@count}}
  
  <tr>
    <th colspan="3" class="title">
      {{$keyClass}}
    </th>
  </tr>
  
  {{foreach from=$currClass key=keyChamp item=currChamp}}
    <tr>
      <td {{$styleColorConflit|smarty:nodefaults}}>
        {{$currChamp.class_field}}
      </td>
      <td {{$styleColorConflit|smarty:nodefaults}}>
        {{$currChamp.count_0_bdd}}
      </td>
      <td {{$styleColorConflit|smarty:nodefaults}}>
        {{$currChamp.count_bdd}}
      </td>
    </tr>
  {{/foreach}}
  
  {{/if}}
  {{/foreach}}
</table>