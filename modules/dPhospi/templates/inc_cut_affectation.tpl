<table class="form">
  <tr>
    <th class="category" colspan="4">Autres actions</th>
  </tr>
  <tr>
    <td colspan="4">
      <form name="cutAffectation" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: Control.Modal.close})">
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="dosql" value="do_cut_affectation_aed" />
        <input type="hidden" name="lit_id" value="{{$lit_id}}" />
        {{mb_key object=$affectation}}
        <input type="text" name="_date_cut_da" value="{{$affectation->entree|date_format:$conf.datetime}}" readonly="readonly"/>
        <input type="hidden" name="_date_cut" class="dateTime" value="{{$affectation->entree}}" />
        
        <script type="text/javascript">
          var dates = {
            limit: {
              start: "{{$affectation->entree}}",
              stop: "{{$affectation->sortie}}"
            }
          }
          
          Main.add( function(){
            Calendar.regField(getForm("cutAffectation")._date_cut, dates, {timePicker: true});
          } );
        </script>
        <button type="button" class="cut" onclick="this.form.onsubmit();">Scinder</button>
      </form>
    </td>
  </tr>
</table>