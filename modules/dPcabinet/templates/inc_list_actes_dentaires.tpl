{{assign var=patient_id value=$devenir_dentaire->patient_id}}
{{assign var=actes_dentaires value=$devenir_dentaire->_ref_actes_dentaires}}

<script type="text/javascript">
  Main.add(function() {
    $('list_actes_dentaires').select('hr').each(function(hr) {
      Droppables.add($(hr), {
      onDrop: function(div, hr) {
        orderActeDentaire(div.get('id'), hr.get('rank'));
      },
      hoverclass: "rank-selected"
      });
    });
  });
</script>

<table class="tbl">
  <tr>
    <th class="category">{{tr}}CCodable-actes{{/tr}}</th>
  </tr>
  
  {{if $actes_dentaires|@count}}
    <tr>
      <td style="max-height: 1px !important;">
        <hr id='drop_1' class="droppable hr_rank" data-rank="1" />
      </td>
    </tr>
  {{/if}}
  {{foreach from=$actes_dentaires item=_acte_dentaire}}
    <tr>
      <td>
        <div id="acte_{{$_acte_dentaire->_id}}" style="width:100%;" data-id="{{$_acte_dentaire->_id}}" class="draggable">
          <span style="float: right;">
            ICR : {{$_acte_dentaire->ICR}}
          </span>
          
          <div style="float: left;">
          <form name="delCode-{{$_acte_dentaire->_id}}" method="post"
            onsubmit="return onSubmitFormAjax(this, { onComplete: function() {
              afterActeDentaire(null, {devenir_dentaire_id: '{{$devenir_dentaire->_id}}'});
              updateRank(-1); }});">
            <input type="hidden" name="m" value="dPpatients" />
            <input type="hidden" name="dosql" value="do_acte_dentaire_aed" />
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="_patient_id" value="{{$patient_id}}" />
            {{mb_key object=$_acte_dentaire}}
            {{mb_field object=$_acte_dentaire field=code hidden=true}}
            <span>
              <button type="button" class="notext trash" onclick="this.form.onsubmit();" title="Supprimer"></button>
              
              <big style="border: 2px solid #888; background: #fff; width: 2em; border-radius: 1em;">&nbsp;{{$_acte_dentaire->rank}}&nbsp;</big>
              <a style="display: inline;" onclick="CodeCCAM.show('{{$_acte_dentaire->code}}', 'CConsultation')" href="#1">
                {{$_acte_dentaire->code}}
              </a>
            </span>
          </form>

          {{if $_acte_dentaire->commentaire}}          
          <span>
          {{$_acte_dentaire->commentaire|nl2br}}
          </span>
          {{/if}} 

          </div>
          
          
          
          <script type="text/javascript">
            new Draggable($('acte_{{$_acte_dentaire->_id}}'), dragOptions);
          </script>
        </div>
      </td>
    </tr>
    <tr>
      <td>
        <hr data-rank="{{$_acte_dentaire->rank}}" class="droppable hr_rank"/>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="4">
        {{tr}}CActeDentaire.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
  <tr>
    <td>
      <span style="float: right">Total : {{$devenir_dentaire->_total_ICR}}</span>
    </td>
  </tr>
</table>