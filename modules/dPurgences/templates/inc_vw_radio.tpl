<form name="passageRadio{{$rpu->_id}}" action="" method="post">
  <input type="hidden" name="dosql" value="do_rpu_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPurgences" />
  <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
  <input type="hidden" name="radio_debut" value="{{$rpu->radio_debut}}" />
  <input type="hidden" name="radio_fin" value="{{$rpu->radio_fin}}" />
  {{if $rpu->radio_debut}}
    {{$rpu->radio_debut|date_format:"%Hh%M"}}
    <button class="cancel notext" type="button" onclick="this.form.radio_debut.value=''; submitRadio(this.form)">Supprimer début</button>
  {{else}}
    <button class="submit" type="button" onclick="this.form.radio_debut.value='current'; submitRadio(this.form)">Début</button>
  {{/if}}
  
  {{if $rpu->radio_fin}}
    {{$rpu->radio_fin|date_format:"%Hh%M"}}
    <button class="cancel notext" type="button" onclick="this.form.radio_fin.value=''; submitRadio(this.form)">Supprimer fin</button>
  {{else}}
    <button class="submit" type="button" onclick="this.form.radio_fin.value='current'; submitRadio(this.form)">Fin</button>
  {{/if}}
</form>