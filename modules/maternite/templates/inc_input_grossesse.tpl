{{mb_script module=maternite script=grossesse}}

{{assign var=grossesse value=$object->_ref_grossesse}}
{{mb_default var=submit value=0}}

<script type="text/javascript">
  Main.add(function() {
    Grossesse.formTo = $('grossesse_id').form;
    Grossesse.duree_sejour = '{{$conf.maternite.duree_sejour}}';
    
    {{if $submit}}
      Grossesse.submit = {{$submit}};
    {{/if}}
  });
</script>

<input type="hidden" name="grossesse_id" value="{{$object->grossesse_id}}" id="grossesse_id"/>
<input type="hidden" name="_patient_sexe" value="" onchange="Grossesse.toggleGrossesse(this.value, this.form)"/>

<span id="view_grossesse">
  {{if $grossesse->_id}}
    <span onmouseover="ObjectTooltip.createEx(this, '{{$grossesse->_guid}}')">{{$grossesse}}</span>
  {{else}}
    {{tr}}CGrossesse.none_linked{{/tr}}
  {{/if}}
</span>

<button type="button" class="edit notext button_grossesse" {{if !$patient->_id || ($patient->sexe != "f" && $patient->_age >= 12)}}disabled="disabled"{{/if}}
  onclick="Grossesse.viewGrossesses('{{$patient->_id}}', '{{$object->_guid}}', $V(this.form.grossesse_id), this.form)"></button>
  