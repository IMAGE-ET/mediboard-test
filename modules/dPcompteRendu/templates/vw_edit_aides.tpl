<script type="text/javascript">
function closeWindow(){
  window.close();
}
function submitFormAides(oForm){
  if(checkForm(oForm)){
    submitFormAjax(oForm, 'systemMsg', { onComplete : closeWindow });
  }
  return false;
}
</script>

<form name="editAides" action="?m=dPcompteRendu" method="post">
<input type="hidden" name="dosql" value="do_aide_aed" />
<input type="hidden" name="m" value="dPcompteRendu" />
<input type="hidden" name="aide_id" value="" />
<input type="hidden" name="class" value="{{$aide->class}}" />
<input type="hidden" name="field" value="{{$aide->field}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      Création d'une aide
    </th>
  </tr>
  <tr>
    <th><label for="function_id" title="Fonction concerné">Fonction</label></th>
    <td>
      <select name="function_id" title="{{$aide->_props.function_id}}" onchange="this.form.user_id.value = ''">
        <option value="">&mdash; Associer à une fonction &mdash;</option>
        {{foreach from=$listFunc item=curr_func}}
          <option class="mediuser" style="border-color: #{{$curr_func->color}};" value="{{$curr_func->function_id}}" {{if $curr_func->function_id == $aide->function_id}} selected="selected" {{/if}}>
            {{$curr_func->_view}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th><label for="user_id" title="Utilisateur concerné">Praticien</label></th>
    <td>
      <select name="user_id" title="{{$aide->_props.user_id}}" onchange="this.form.function_id.value = ''">
        <option value="">&mdash; Associer à un praticien &mdash;</option>
        {{foreach from=$listPrat item=curr_prat}}
          <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $aide->user_id}} selected="selected" {{/if}}>
            {{$curr_prat->_view}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th><label for="name" title="intitulé de l'aide, obligatoire.">Intitulé</label></th>
    <td><input type="text" name="name" title="{{$aide->_props.name}}" value="{{$aide->name}}" /></td>
  </tr>
  <tr>
    <th><label for="text" title="Texte de remplacement.">Texte</label></th>
    <td>
      <textarea rows="4" name="text" title="{{$aide->_props.text}}">{{$aide->text}}</textarea>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button class="submit" type="button" onclick="submitFormAides(this.form)">
        Créer
      </button>
    </td>
  </tr>
</table>

</form>