{{if $moment->code_moment_id}}
      <form name="addMomentUnitaire" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_association_moment_aed" />
	    <input type="hidden" name="code_moment_id" value="{{$moment->code_moment_id}}" />
	    <input type="hidden" name="association_moment_id" value="" />
      <input type="hidden" name="del" value="0" />
      <table class="form" style="position: fixed; width: 150px;">
        <tr>
          <th class="category" colspan="2">Gestion des moments unitaires de: "{{$moment->libelle_moment}}"</th>
        </tr>
        
        {{if !$moment->_ref_associations}}
        <tr>
          <td colspan="2">
            <div class="big-info">
              Aucun moment unitaire n'est associé à ce moment
            </div>
          </td>
        </tr>
        {{else}}
        <tr>
          <th class="category">Moment Unitaire</th>
          <th class="category">OR</th>
        </tr>
        {{foreach from=$moment->_ref_associations item=_association}}
        <tr>
          <td>
            <button type="button" class="trash notext" onclick="delMomentUnitaire(this.form,'{{$_association->_id}}');">
              Supprimer moment unitaire
            </button>
            {{$_association->_ref_moment_unitaire->_view}}</td>
          <td>
            {{if $_association->OR}}
              Oui
            {{else}}
              Non
            {{/if}}  
          </td>
        </tr>
        {{/foreach}}
        {{/if}}
        <tr>
          <th colspan="2" class="category">Ajout d'un moment unitaire</th>
        </tr>
        <tr>
          <td>
            <select name="moment_unitaire_id" style="width: 150px">      
						  <option value="">&mdash; Sélection du moment</option>
						  {{foreach from=$moments_unitaires key=type_moment item=_moments}}
						     <optgroup label="{{$type_moment}}">
						     {{foreach from=$_moments item=moment}}
						     <option value="{{$moment->_id}}">{{$moment->_view}}</option>
						     {{/foreach}}
						     </optgroup>
						  {{/foreach}}
						 </select>
					</td>
					<td>
					  {{mb_field object=$association field="OR" typeEnum="checkbox"}}
					  {{mb_label object=$association field="OR" typeEnum="checkbox"}}
					</td>
			  </tr>
			  <tr>
			    <td colspan="2" style="text-align: center">
			      <button type="button" class="submit" onclick="submitAssociation(this.form);">Ajouter ce moment unitaire</button>
			    </td>
			  </tr>			  
       </table>
     </form>
    {{else}}
      <div class="big-info">
        Veuillez sélectionner un moment afin d'éditer ces moments unitaires
      </div>
    {{/if}}