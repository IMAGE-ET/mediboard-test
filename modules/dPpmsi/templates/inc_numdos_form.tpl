<script type="text/javascript">
Main.add(function () {
  prepareForm(document.forms.editNumdos{{$_sejour->_id}});
});
</script>

<form name="editNumdos{{$_sejour->_id}}" action="?m={{$m}}" method="post" onsubmit="return ExtRefManager.submitNumdosForm({{$_sejour->_id}})">

<input type="hidden" name="dosql" value="do_idsante400_aed" />
<input type="hidden" name="m" value="dPsante400" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="ajax" value="1" />
<input type="hidden" name="id_sante400_id" value="{{$_sejour->_ref_numdos->_id}}" />

<table class="form">
  <tr>
    <th class="category" colspan="4">
      Numéro de dossier
      <script type="text/javascript">
        SejourHprimSelector.init{{$_sejour->_id}} = function(){
          this.sForm      = "editNumdos{{$_sejour->_id}}";
          this.sId        = "id400";
          this.sIPP       = document.forms.editIPP.id400.value;
          this.sPatNom    = "{{$patient->nom}}";
          this.sPatPrenom = "{{$patient->prenom}}";
          if(this.sIPP != "") {
            this.pop();
          } else {
            alert("Vous devez indiquer l'IPP");
          }
        };
      </script>
    </th>
  </tr>
  <tr>
    <th>
      <label for="id400" title="Saisir le numéro de dossier">Numéro de dossier</label>
    </th>
    <td>
      <input type="text" class="notNull" name="id400" value="{{if $_sejour->_ref_numdos}}{{$_sejour->_ref_numdos->id400}}{{/if}}" size="8" />
      <input type="hidden" class="notNull" name="tag" value="{{if $_sejour->_ref_numdos}}{{$_sejour->_ref_numdos->tag}}{{/if}}" />
      <input type="hidden" class="notNull" name="object_id" value="{{$_sejour->_id}}" />
      <input type="hidden" class="notNull" name="object_class" value="CSejour" />
      <input type="hidden" name="last_update" value="{{if $_sejour->_ref_numdos}}{{$_sejour->_ref_numdos->last_update}}{{/if}}" />
    </td>
    <td class="button" rowspan="2">
      <button class="submit" type="submit">Valider</button>
    </td>
    <td class="button" rowspan="2">
      {{if $hprim21installed}}
      <button class="search" type="button" onclick="SejourHprimSelector.init{{$_sejour->_id}}()">Rechercher</button>
      {{/if}}
    </td>
  </tr>
  <tr>
    <th>
      Suggestion
    </th>
    <td>
      {{$_sejour->_guess_num_dossier}}
    </td>
  </tr>
</table>

</form>