<div id="{{$class}}">
  <table class="form">
    <tr>
      {{if $modeles.prat|@count}}<th class="category">Modèles du praticien</th>{{/if}}
      {{if $modeles.func|@count}}<th class="category">Modèles de la fonction</th>{{/if}}
    </tr>
    <tr>
    {{if $modeles.prat|@count}}
      <td style="text-align:center">
        <select id="modele_{{$class}}_prat" name="modele_{{$class}}_prat" onchange="if (this.value) setClose(this.value,'{{$modelesId.$class}}');" size="20">
        {{foreach from=$modeles.prat item=modele}}
          <option value="{{$modele->_id}}">{{$modele->nom}}</option>
        {{/foreach}}
        </select>
      </td>
    {{/if}}
    
    {{if $modeles.func|@count}}
      <td style="text-align:center">
        <select id="modele_{{$class}}_func" name="modele_{{$class}}_func" onchange="if (this.value) setClose(this.value,'{{$modelesId.$class}}');" size="20">
        {{foreach from=$modeles.func item=modele}}
          <option value="{{$modele->_id}}">{{$modele->nom}}</option>
        {{/foreach}}
        </select>
      </td>
    {{/if}}
    </tr>
  </table>
</div>
