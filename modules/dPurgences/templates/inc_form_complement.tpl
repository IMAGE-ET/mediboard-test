<fieldset>
  <legend>Compléments</legend>
  <form name="editBox" action="?m={{$m}}{{if !$can->edit}}&amp;tab=vw_idx_rpu{{/if}}" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="m" value="dPurgences" />
    <input type="hidden" name="dosql" value="do_rpu_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
    <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
    <input type="hidden" name="_annule" value="{{$rpu->_annule|default:"0"}}" />
    <input type="hidden" name="code_diag" value="{{$rpu->code_diag}}" />
  
    <table class="form">
      <tr>
        <th>{{tr}}CChapitreMotif-nom{{/tr}}</th>
        <td>
          <select name="_chapitre_id" onchange="refreshComplement('chapitre');"  style="width: 15em;">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$chapitres item=chapitre}}
              <option value="{{$chapitre->_id}}" {{if $chapitre_id == $chapitre->_id || $rpu->_ref_motif->chapitre_id == $chapitre->_id}}selected="selected"{{/if}}>
                {{$chapitre->_view}}
              </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <th>{{tr}}CMotif-nom{{/tr}}</th>
        <td>
          <select name="_motif_id" onchange="refreshComplement('motif');"  style="width: 15em;">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$motifs item=motif}}
              <option value="{{$motif->_id}}" {{if $motif->_id == $motif_id || $rpu->_ref_motif->motif_id == $motif->_id}}selected="selected"{{/if}}>
                {{$motif->_view}}
              </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$rpu field="code_diag"}}</th>
        <td>{{mb_value object=$rpu field="code_diag"}}</td>
      </tr>
      {{if $can->edit}}
        <tr>
          <th>{{mb_label object=$rpu field="ccmu"}}</th>
          <td>
            {{mb_field object=$rpu field="ccmu" emptyLabel="Choose" style="width: 15em;"}}
            <script>
              Main.add(function () {
                {{if $rpu->code_diag}}
                  var min = '{{$rpu->_ref_motif->degre_min}}';
                  var max = '{{$rpu->_ref_motif->degre_max}}';
                  var form = getForm("editBox");
                  var ccmu = form.ccmu;
                  ccmu.options[2].disabled=true;
                  ccmu.options[6].disabled=true;
                  ccmu.options[7].disabled=true;
                  ccmu.options[2].hide();
                  ccmu.options[6].hide();
                  ccmu.options[7].hide();
                  for (var i=0; i<ccmu.options.length; i++){
                    if (i == 2) { i++; min++; max++;}
                    if (i<min || i>max) {
                      ccmu.options[i].disabled = true;
                    }
                  }
                {{/if}}
              });
            </script>
          </td>
        </tr>
      {{/if}}
      <tr>
        <th>{{mb_label object=$rpu field="box_id"}}</td>
        <td>
          {{mb_include module=dPhospi template="inc_select_lit" field=box_id selected_id=$rpu->box_id ajaxSubmit=0 listService=$services}}
          <button type="button" class="cancel opacity-60 notext" onclick="this.form.elements['box_id'].selectedIndex=0"></button>
          &mdash; {{tr}}CRPU-_service_id{{/tr}} :
          {{if $services|@count == 1}}
            {{assign var=first_service value=$services|@reset}}
            {{$first_service->_view}}
          {{else}}
          <select name="_service_id" class="{{$sejour->_props.service_id}}">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$services item=_service}}
            <option value="{{$_service->_id}}" {{if "Urgences" == $_service->nom}} selected="selected" {{/if}}>
              {{$_service->_view}}
            </option>
            {{/foreach}}
          </select>
          {{/if}}
          <br/>
          <script type="text/javascript">
            Main.add(function(){
              var form = getForm("editBox");
              
              if (form.elements._service_id) {
                var box = form.elements.box_id;
                box.observe("change", function(event){
                  var service_id = box.options[box.selectedIndex].up("optgroup").get("service_id");
                  $V(form.elements._service_id, service_id);
                });
              }
            });
          </script>
        </td>
      </tr>
      <tr>
        <td class="button" colspan="2">
          <button class="submit" onclick="this.form.submit();">{{tr}}Validate{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</fieldset>