<script type="text/javascript">
// Notification de l'arrivée du patient
if (!window.Consultations) {
  Consultations = {
    start: function() {
    window.location.reload();
    }
  };
}

putArrivee = function(oForm) {
  var today = new Date();
  oForm.arrivee.value = today.toDATETIME(true);
  onSubmitFormAjax(oForm, { onComplete: Consultations.start } );
}
</script>


{{if !$board}}
{{if $canCabinet->read}}
<script type="text/javascript">
Main.add( function () {
  Calendar.regField(getForm("changeView").date, null, {noView: true});
} );
</script>
{{/if}}
<form name="changeView" action="?" method="get">
  <input type="hidden" name="m" value="{{$current_m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <table class="form">
    <tr>
      <td colspan="6" style="text-align: left; width: 100%; font-weight: bold; height: 20px;">
        <div style="float: right;">{{$hour|date_format:$conf.time}}</div>
        {{$date|date_format:$conf.longdate}}
        {{if $canCabinet->read}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
        {{/if}}
      </td>
    </tr>
    <tr>
      {{if $canCabinet->read}}
      <th><label for="vue2" title="Type de vue du planning">Type de vue</label></th>
      <td colspan="5">
        <select name="vue2" onchange="this.form.submit()">
          <option value="0" {{if $vue == "0"}}selected="selected"{{/if}}>Tout afficher</option>
          <option value="1" {{if $vue == "1"}}selected="selected"{{/if}}>Cacher les terminées</option>
        </select>
      </td>
      {{/if}}
    </tr>
  </table>
</form>
{{/if}}

<table class="tbl" style="{{if !@$offline}}font-size: 9px;{{/if}} {{if @$fixed_width|default:0}}width: 250px{{/if}}">
  <tr>
    <th class="title" colspan="3">Consultations</th>
  </tr>

  <tr>
    <th style="width: 50px; ">Heure</th>
    <th colspan="2">Patient / Motif</th>
  </tr>
  
  {{foreach from=$listPlage item=_plage}}
  <tr>
    <th colspan="3">
      {{if $current_m == "dPurgences"}}
        <span style="float: right;">
          <button class="print notext" onclick="printPlage({{$_plage->_id}})">{{tr}}Print{{/tr}}</button>
        </span>
      {{/if}}
      {{mb_include module=system template=inc_object_notes object=$_plage}}
      {{$_plage->debut|date_format:$conf.time}} 
      - {{$_plage->fin|date_format:$conf.time}}
      {{if $_plage->libelle}}: {{$_plage->libelle}}{{/if}}
    </th>
  </tr>
  {{foreach from=$_plage->_ref_consultations item=_consult}}
    {{mb_include module=cabinet template=inc_detail_consult}}
  {{foreachelse}}
    <tr>
      <td colspan="3"class="empty">{{tr}}CConsultation.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  
  {{foreachelse}}
  <tr>
    <th colspan="3" style="font-weight: bold;">{{tr}}CPlageconsult.none{{/tr}}</th>
  </tr>
  {{/foreach}}
</table>