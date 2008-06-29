{{if $user->_is_secretaire}}
<tr>
  <td colspan="2">
  <form name="praticien" method="post">
   <select name="praticien_id" onchange="form.submit()">
   <option value="">&mdash; Choix d'un praticien</option>
   {{foreach from=$praticiens item=_praticien}}
     <option value="{{$_praticien->_id}}" class="mediuser" style="border-color: #{{$_praticien->_ref_function->color}};" 
     	{{if $prat->_id == $_praticien->_id}}selected = "selected"{{/if}}>
       {{$_praticien->_view}}
     </option>
   {{/foreach}}
   </select>
 </form>
 
 {{if !$prat->_id}}
 <div class="big-info">
   Les vues du tableau de bord sont spécifiques à chaque praticien.
   <br />Merci d'en <strong>sélectionner</strong> un dans la liste ci-dessus.
 </div>
 {{/if}}
 
 </td>
</tr>
{{/if}}
