<table class="main">
  <tr>

    <td colspan="2">
      <form name="selectPrat" action="?" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
        
      <table class="form">
        <tr>
          <th class="category" colspan="10">Filtrer les modèles</th>
        </tr>
       
        <tr>
          <th>
            <label for="prat_id" title="Sélectionner l'utilisateur pour afficher ses modèles">Utilisateur: </label>
          </th>
          <td>
            <select name="prat_id" onchange="submit()">
              <option value="">&mdash; Choisir un utilisateur &mdash;</option>
              {{foreach from=$listPrat item=curr_prat}}
                <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $userSel->user_id}} selected="selected" {{/if}}>
                  {{$curr_prat->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        
        </table>
      </form>
    </td>
  </tr>
  
  <tr>

    <td>
      {{include file=inc_modeles.tpl modeles=$modeles.prat object=$userSel}}
    </td>

    <td>
      {{include file=inc_modeles.tpl modeles=$modeles.func object=$userSel->_ref_function}}
    </td>
  </tr>
</table>