<table class="main">
  <tr>

    <td colspan="2">
      <form name="selectPrat" action="index.php" method="get">
      <input type="hidden" name="m" value="{$m}" />
        
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
              <option value="0">&mdash; Choisir un utilisateur &mdash;</options>
              {foreach from=$listPrat item=curr_prat}
                <option value="{$curr_prat->user_id}" {if $curr_prat->user_id == $userSel->user_id} selected="selected" {/if}>
                  {$curr_prat->_view}
                </option>
              {/foreach}
            </select>
          </td>
        </tr>
        
        </table>
      </form>
    </td>


  </tr>
  <tr>

    <td>
      {if $listModelePrat|@count}
      <table class="tbl">
		<tr>
		  <th class="title" colspan="3">Modèles de {$userSel->_view}</th>
		</tr>
        <tr>
          <th>Nom</th>
          <th>Type</th>
          <th>Supprimer</th>
        </tr>
        {foreach from=$listModelePrat item=curr_modele}
        <tr>
          <td>
            <a href="index.php?m={$m}&tab=addedit_modeles&compte_rendu_id={$curr_modele->compte_rendu_id}">{$curr_modele->nom}</a>
          </td>
          <td>
            <a href="index.php?m={$m}&tab=addedit_modeles&compte_rendu_id={$curr_modele->compte_rendu_id}">{$curr_modele->type}</a>
          </td>
          <td>
            <form name="editFrm" action="?m={$m}" method="post">
            <input type="hidden" name="m" value="{$m}" />
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="dosql" value="do_modele_aed" />
            <input type="hidden" name="compte_rendu_id" value="{$curr_modele->compte_rendu_id}" />
            <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{ldelim}typeName:'le modèle',objName:'{$curr_modele->nom|escape:javascript}'{rdelim})" />
            </form>
          </td>
        </tr>
        {/foreach}
      </table>
      {/if}
    </td>


    <td>
      {if $listModeleFunc|@count}
      <table class="tbl">
        <tr>
		  <th class="title" colspan="3">Modèles de {$userSel->_ref_function->_view}</th>
		</tr>
		<tr>
          <th>Nom</th><th>Type</th><th>Supprimer</th>
        </tr>
        {foreach from=$listModeleFunc item=curr_modele}
        <tr>
          <td>
            <a href="index.php?m={$m}&tab=addedit_modeles&compte_rendu_id={$curr_modele->compte_rendu_id}">{$curr_modele->nom}</a>
          </td>
          <td>
            <a href="index.php?m={$m}&tab=addedit_modeles&compte_rendu_id={$curr_modele->compte_rendu_id}">{$curr_modele->type}</a>
          </td>
          <td>
            <form name="editFrm" action="?m={$m}" method="post">
            <input type="hidden" name="m" value="{$m}" />
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="dosql" value="do_modele_aed" />
            <input type="hidden" name="compte_rendu_id" value="{$curr_modele->compte_rendu_id}" />
            <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{ldelim}typeName:'le modèle',objName:'{$curr_modele->nom|escape:javascript}'{rdelim})" />
            </form>
          </td>
        </tr>
        {/foreach}
      </table>
      {/if}
    </td>

  </tr>
</table>