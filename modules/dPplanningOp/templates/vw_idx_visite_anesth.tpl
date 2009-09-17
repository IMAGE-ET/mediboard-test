<script type="text/javascript">
  Main.add(function(){
    Calendar.regField(getForm("selection").date, null, {noView: true});
	  if ($('type_sejour')){
      Control.Tabs.create('type_sejour', true);
    }
  });
</script>

<table class="main">
  <tr>
    <th>
      <form action="?" name="selection" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <label for="selPrat">Praticien</label>
      <select name="selPrat" onchange="this.form.submit()" style="max-width: 150px;">
        <option value="-1">&mdash; Choisir un praticien</option>
        {{foreach from=$listPrat item=curr_prat}}
        <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $selPrat}} selected="selected" {{/if}}>
          {{$curr_prat->_view}}
        </option>
        {{/foreach}}
      </select>
      - Interventions du {{$date|date_format:$dPconfig.longdate}}
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="vw_idx_planning" />
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
  <tr>
    <td>
      <ul id="type_sejour" class="control_tabs">
      {{foreach from=$listInterv key=_key_type item=_services}}
      <li><a href="#{{$_key_type}}_tab">{{$_key_type}}</a></li>
      {{/foreach}}
      </ul>
      <hr class="control_tabs" />
      {{foreach from=$listInterv key=_key_type item=_services}}
      <div id="{{$_key_type}}_tab" style="display:none">
      <table class="tbl">
        {{foreach from=$_services key=_key_service item=_list_intervs}}
        {{if $_list_intervs|@count}}
        <tr>
          {{if $_key_service == "non_place"}}
          <th colspan="5">Non placés</th>
          {{else}}
          <th colspan="5">Service {{$services.$_key_service->_view}}</th>
          {{/if}}
        </tr>
        {{foreach from=$_list_intervs item=_operation}}
        <tr>
          <td>{{$_operation->_ref_chir->_view}}</td>
          <td>
            {{if $_operation->libelle}}
              {{$_operation->libelle}}
            {{else}}
              {{foreach from=$_operation->_ext_codes_ccam item=curr_code}}
                {{$curr_code->code}}
              {{/foreach}}
            {{/if}}
          </td>
          <td>{{$_operation->_ref_affectation->_ref_lit->_view}}</td>
          <td>{{$_operation->time_operation|date_format:$dPconfig.time}}</td>
          <td>
            {{if $_operation->date_visite_anesth}}
            Le {{$_operation->date_visite_anesth|date_format:$dPconfig.datetime}} par le Dr {{$_operation->_ref_anesth_visite->_view}}
            {{else}}
            Visite non effectuée
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
        {{/if}}
        {{/foreach}}
      </table>
      </div>
      {{/foreach}}
    </td>
  </tr>
</table>