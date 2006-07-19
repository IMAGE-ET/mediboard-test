<table class="main">
  <tr>
    <td>
      <form name="bloc" action="index.php" method="get">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="5" class="category">
            <select name="typeVue" onChange="this.form.submit();">
              <option value="0">Moyenne des temps opératoires</option>
              <option value="1"{{if $typeVue}} selected="selected"{{/if}}>Moyenne des temps de préparation</option>
            </select>
          </th>
        </tr>
        
        <tr>
          {{if !$typeVue}}
          <th><label for="codeCCAM" title="Acte CCAM">Acte CCAM</label></th>
          <td>
            <input type="text" name="codeCCAM" value="{{$codeCCAM}}" />
            (% pour grouper toutes les interventions)
          </td> 
          {{/if}}    
          <th{{$_aff_particulier}}>
            <label for="intervalle_0" title="Intervalle">Intervalle</label>
          </th>
          <td{{$_aff_particulier}}>
            <input type="radio" name="intervalle" value="0" {{if $intervalle == 0}}checked="checked"{{/if}} />
            <label for="intervalle_0" title="Dernier mois">Dernier mois</label>
            <br />
            <input type="radio" name="intervalle" value="1" {{if $intervalle == 1}}checked="checked"{{/if}} />
            <label for="intervalle_1" title="6 dernier mois">6 dernier mois</label>
          </td>
          <td{{$_aff_particulier}}>
            <input type="radio" name="intervalle" value="2" {{if $intervalle == 2}}checked="checked"{{/if}} />
            <label for="intervalle_2" title="Dernière année">Dernière année</label>
            <br />
            <input type="radio" name="intervalle" value="3" {{if $intervalle == 3}}checked="checked"{{/if}} />
            <label for="intervalle_3" title="Pas d'ntervalle">Pas d'intervalle</label>
          </td>
        </tr>
        {{if !$typeVue}}
        <tr>
          <th><label for="prat_id" title="Praticien">Praticien</label></th>
          <td>
            <select name="prat_id">
              <option value="0">&mdash; Tous les praticiens</option>
              {{foreach from=$listPrats item=curr_prat}}
              <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $prat_id}}selected="selected"{{/if}}>
                {{$curr_prat->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{/if}}
        <tr>
          <td colspan="5" class="button"><button type="submit" class="search">Afficher</button></td>
        </tr>
      </table>
      </form>
      {{if $typeVue}}
        {{include file="inc_vw_timeop_prepa.tpl"}}
      {{else}}
        {{include file="inc_vw_timeop_op.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>