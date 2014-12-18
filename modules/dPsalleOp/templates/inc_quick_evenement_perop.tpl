<h2>{{$operation}}</h2>

{{unique_id var=uid}}
<form name="addAnesthPerop-{{$uid}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, function(){Control.Modal.close()})">
  <input type="hidden" name="m" value="dPsalleOp" />
  <input type="hidden" name="dosql" value="do_anesth_perop_aed" />
  <input type="hidden" name="operation_id" value="{{$operation->_id}}" />
  <input type="hidden" name="datetime" value="now" />
  <input type="hidden" name="incident" value="0" />
  <input type="hidden" name="libelle" value="0" />
  {{mb_key object=$evenement}}

  {{foreach from=$evenement->_aides item=_by_label}}
    {{foreach from=$_by_label.no_enum item=_aides key=_owner}}
      <div style="text-align: right;">
        <em>{{$_owner}} &ndash; </em>
      </div>

      <table class="main tbl">
        <tr>
          <td class="text">
            {{foreach from=$_aides item=_aide}}
              <div style="display: inline-block; width: 20em; margin-bottom: 3px;">
                <button type="submit" class="tick notext compact"
                        onclick="$V(this.form.incident,0); $V(this.form.libelle,this.getText());">{{$_aide}}</button>

                <button type="submit" class="warning notext compact" title="{{tr}}CAnesthPerop-incident{{/tr}}"
                        onclick="$V(this.form.incident,1); $V(this.form.libelle,this.getText());">{{$_aide}}</button>

                {{$_aide}}
              </div>
            {{/foreach}}
          </td>
        </tr>
      </table>
    {{/foreach}}
  {{/foreach}}
</form>