<form name="passageRadio{{$rpu->_id}}" action="" method="post">
  <input type="hidden" name="dosql" value="do_rpu_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPurgences" />
  <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
  <input type="hidden" name="radio_debut" value="{{$rpu->radio_debut}}" />
  <input type="hidden" name="radio_fin" value="{{$rpu->radio_fin}}" />
  {{if $rpu->radio_debut}}
  	<strong>{{mb_label object=$rpu field=radio_debut}}</strong>
    {{$rpu->radio_debut|date_format:$dPconfig.time}}
    <button class="cancel notext" type="button" onclick="this.form.radio_debut.value=''; submitRadio(this.form)">{{tr}}Cancel{{/tr}}</button>
  {{else}}
    <button class="submit" type="button" onclick="this.form.radio_debut.value='current'; submitRadio(this.form)">{{tr}}CRPU-radio_debut{{/tr}}</button>
  {{/if}}
  
  {{if $rpu->radio_fin}}
  	<strong>{{mb_label object=$rpu field=radio_fin}}</strong>
    {{$rpu->radio_fin|date_format:$dPconfig.time}}
    <button class="cancel notext" type="button" onclick="this.form.radio_fin.value=''; submitRadio(this.form)">{{tr}}Cancel{{/tr}}</button>
  {{else}}
    <button class="submit" type="button" onclick="this.form.radio_fin.value='current'; submitRadio(this.form)">{{tr}}CRPU-radio_fin{{/tr}}</button>
  {{/if}}
</form>