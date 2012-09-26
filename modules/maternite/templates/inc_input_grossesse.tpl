{{mb_script module=maternite script=grossesse}}

{{assign var=grossesse value=$object->_ref_grossesse}}
{{mb_default var=submit value=0}}
{{mb_default var=large_icon value=0}}

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
<input type="hidden" name="_large_icon" value="{{$large_icon}}" />

<span id="view_grossesse">
  {{if $grossesse->_id}}
    <img onmouseover="ObjectTooltip.createEx(this, '{{$grossesse->_guid}}')" {{if !$grossesse->active}}class="opacity-50"{{/if}}
        src="style/mediboard/images/icons/grossesse.png" {{if $large_icon}}style="width: 30px;"{{/if}}/>
  {{elseif !$patient->_id || $patient->sexe == "f"}}
    <div class="empty" style="display:inline">{{tr}}CGrossesse.none_linked{{/tr}}</div>
  {{/if}}
</span>

{{if !$patient->_id || $patient->sexe == "f"}}
  <button type="button" class="edit notext button_grossesse" {{if !$patient->_id || $patient->_annees < 12}}disabled="disabled"{{/if}}
    onclick="Grossesse.viewGrossesses('{{$patient->_id}}', '{{$object->_guid}}', this.form)"></button>
{{/if}}
  