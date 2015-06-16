{{mb_default var=include_form value=1}}
{{mb_default var=see_alertes value=0}}
{{mb_default var=name_form value=""}}

{{if $line->_recent_modification}}
  {{if @$conf.object_handlers.CPrescriptionAlerteHandler && $line->_ref_alerte->_id}}
    {{if !$see_alertes}}
      <div id="alert_manuelle{{$name_form}}_{{$line->_ref_alerte->_id}}">
      {{assign var=img_src value="ampoule"}}
      {{if $line->_urgence}}
        {{assign var=img_src value="ampoule_urgence"}}
      {{/if}}
      <img style="float: left" src="images/icons/{{$img_src}}.png" onclick="alerte_prescription = ObjectTooltip.createDOM(this, 'editAlerte{{$name_form}}-{{$line->_ref_alerte->_id}}', { duration: 0}); "/>
    {{/if}}
      {{if $include_form || $see_alertes}}
        <div id="editAlerte{{$name_form}}-{{$line->_ref_alerte->_id}}" style="display: none;">
          <table class="form">
            <tr>
              <th class="category">Alerte</th>
            </tr>
            <tr>
              <td class="text" style="width: 300px;">
                {{mb_value object=$line->_ref_alerte field=comments}}
              </td>
            </tr>
            <tr>
              <td class="button">
                <form name="modifyAlert{{$name_form}}-{{$line->_ref_alerte->_id}}" action="?" method="post" class="form-alerte{{if $line->_urgence}}-urgence{{/if}}-_{{$line->_guid}}"
                      onsubmit="return onSubmitFormAjax(this, {
                        onComplete: function() { $('alert_manuelle{{$name_form}}_{{$line->_ref_alerte->_id}}').hide(); if(alerte_prescription ) { alerte_prescription.hide(); }} });">
                  <input type="hidden" name="m" value="system" />
                  <input type="hidden" name="dosql" value="do_alert_aed" />
                  <input type="hidden" name="del" value="" />
                  <input type="hidden" name="alert_id" value="{{$line->_ref_alerte->_id}}" />
                  <input type="hidden" name="handled" value="1" />
                  <button type="button" class="tick" onclick="this.form.onsubmit();">Traiter</button>
                </form>
              </td>
            </tr>
          </table>
        </div>
      {{/if}}
    {{if !$see_alertes}}
      </div>
    {{/if}}
  {{elseif !$see_alertes}}
    <img style="float: left" src="images/icons/ampoule.png" title="Ligne récemment modifiée"/>
    {{if is_array($line->_dates_urgences) && array_key_exists($date, $line->_dates_urgences)}}
      <img style="float: left" src="images/icons/ampoule_urgence.png" title="Urgence"/>
    {{/if}}
  {{/if}}
{{/if}}