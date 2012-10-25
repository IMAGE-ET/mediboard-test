{{if $dialog}}
  {{assign var=destform value="m=$m&dialog=1&a=$action"}}
{{else}}
  {{assign var=destform value="m=$m&tab=$tab"}}
{{/if}}

<script type="text/javascript">
Main.add(function() {
  new Control.Tabs.create('tabs-usermessage');
});
</script>

<ul id="tabs-usermessage" class="control_tabs">
  <li><a href="#tab_mail">{{if $usermessage->_id}}
        {{tr}}CUserMessage-title-modify{{/tr}} '{{$usermessage}}'
      {{else}}
        {{tr}}CUserMessage-title-create{{/tr}}
      {{/if}}
      </a>
  </li>
  <li>
    <a href="#historique" {{if !$historique|@count}}class="empty"{{/if}}>Historique ({{$historique|@count}})</a>
  </li>
</ul>
<hr class="control_tabs" />

<div id="tab_mail">
  <form name="EditUserMessage" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="dosql" value="do_usermessage_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="usermessage_id" value="{{$usermessage->_id}}" />
    <input type="hidden" name="postRedirect" value="{{$destform}}" />
    {{if !$usermessage->date_sent}}
      {{mb_field object=$usermessage field=date_sent hidden=true}}
    {{else}}
      {{mb_field object=$usermessage field=archived hidden=true}}
      {{mb_field object=$usermessage field=starred hidden=true}}
    {{/if}}
    <table class="form">
      
      <tr>
        <th class="narrow">{{mb_label object=$usermessage field=from}}</th>
        <td>
          {{mb_field object=$usermessage field=from hidden=1}}
          <div class="mediuser" style="border-color: #{{$usermessage->_ref_user_from->_ref_function->color}};">
            {{$usermessage->_ref_user_from}}
          </div>
        </td>
      </tr>
    
      {{if $usermessage->date_sent}}
      <tr>
        <th>{{mb_label object=$usermessage field=date_sent}}</th>
        <td>{{mb_value object=$usermessage field=date_sent}} ({{mb_value object=$usermessage field=date_sent format=relative}})</td>
      </tr>
      {{/if}}
    
      <tr>
        <th>{{mb_label object=$usermessage field=to}}</th>
        <td>
          {{if $usermessage->date_sent}}
          <div class="mediuser" style="border-color: #{{$usermessage->_ref_user_to->_ref_function->color}};">
            {{$usermessage->_ref_user_to}}
          </div>
          {{else}}
            {{mb_field object=$usermessage field=to hidden=true}}
            <input type="text" name="_to_autocomplete_view" style="width: 16em;" class="autocomplete" value="{{$usermessage->_ref_user_to}}"
              onchange='if(!this.value){this.form.user_id.value=""}' />
            
            <script type="text/javascript">
              Main.add(function(){
                var form = getForm("EditUserMessage");
                var element = form.elements._to_autocomplete_view;
                var url = new Url("system", "ajax_seek_autocomplete");
                url.addParam("object_class", "CMediusers");
                url.addParam("input_field", element.name);
                url.autoComplete(element, null, {
                  minChars: 3,
                  method: "get",
                  select: "view",
                  dropdown: true,
                  afterUpdateElement: function(field,selected){
                    var id = selected.getAttribute("id").split("-")[2];
                    $V(form.to, id);
                    if ($V(element) == "") {
                      $V(element, selected.down('.view').innerHTML);
                    }
                  }
                });
              });
            </script>
          {{/if}}
        </td>
      </tr>
    
      {{if $usermessage->date_read}}
      <tr>
        <th>{{mb_label object=$usermessage field=date_read}}</th>
        <td>{{mb_value object=$usermessage field=date_read}} ({{mb_value object=$usermessage field=date_read format=relative}})</td>
      </tr>
      {{/if}}
    
      {{if $usermessage->archived}}
      <tr>
        <th>{{mb_label object=$usermessage field=archived}}</th>
        <td><strong>{{mb_value object=$usermessage field=archived}}</strong></td>
      </tr>
      {{/if}}
    
      {{if $usermessage->starred}}
      <tr>
        <th>{{mb_label object=$usermessage field=starred}}</th>
        <td><strong>{{mb_value object=$usermessage field=starred}}</strong></td>
      </tr>
      {{/if}}
    
      <tr>
        <th>{{mb_label object=$usermessage field=subject}}</th>
        <td>
          {{if $usermessage->date_sent}}
            {{mb_value object=$usermessage field=subject}}
          {{else}}
            {{mb_field object=$usermessage field=subject size=80}}
          {{/if}}
        </td>
      </tr>
    
      <tr>
        <td colspan="2" style="height: 300px">{{mb_field object=$usermessage field=source id="htmlarea"}}</td>
      </tr>
    
      {{if !$usermessage->date_sent}}
      <tr>
        <td colspan="2" style="text-align: center;">
          <button type="submit" class="send" onclick="$V(this.form.date_sent, 'now');">{{tr}}Send{{/tr}}</button>
          <button type="submit" class="submit">{{tr}}Save{{/tr}} {{tr}}Draft{{/tr}}</button>
        </td>
      </tr>
      {{elseif $usermessage->to == $app->user_id}}
      <tr>
        <td colspan="2" style="text-align: center;">
          <button type="button" onclick="window.parent.Control.Modal.close(); window.parent.UserMessage.create({{$usermessage->_ref_user_from->_id}}, 'Reponse'); ">
            <img src="images/icons/usermessage.png" alt="message"/>
            {{tr}}CUserMessage.answer{{/tr}}
          </button>
          {{if !$usermessage->starred}}
            {{if $usermessage->archived}}
            <button type="submit" class="cancel" onclick="$V(this.form.archived, '0');">{{tr}}Unarchive{{/tr}}</button>
            {{else}}
            <button type="submit" class="change" onclick="$V(this.form.archived, '1');">{{tr}}Archive{{/tr}}</button>
            {{/if}}
          {{/if}}
                
          {{if !$usermessage->archived}}
            {{if $usermessage->starred}}
            <button type="submit" class="cancel" onclick="$V(this.form.starred, '0');">{{tr}}Unstar{{/tr}}</button>
            {{else}}
            <button type="submit" class="new" onclick="$V(this.form.starred, '1');">{{tr}}Star{{/tr}}</button>
            {{/if}}
          {{/if}}
        </td>
      </tr>
      {{/if}}
    </table>
  </form>
</div>

<div id="historique">
  <table class="tbl">
    <tr>
      <th style="width: 20%">{{mb_label object=$usermessage field=from}}</th>
      <th style="width: 20%;">{{mb_label object=$usermessage field=to}}</th>
      <th>{{mb_label object=$usermessage field=subject}}</th>
    </tr>
    {{foreach from=$historique item=_usermessage}}
      <tr>
        <td>
          {{$_usermessage->_ref_user_from}}
        </td>
        <td>
          {{$_usermessage->_ref_user_to}}
        </td>
        <td>
          <a href="#1" onmouseover="ObjectTooltip.createDOM(this, 'usermessage_{{$_usermessage->_id}}')">{{$_usermessage->subject}}</a>
          <div style="display: none" id="usermessage_{{$_usermessage->_id}}">
            <table class="tbl">
              <tr>
                <th class="category">Contenu du message</th>
              </tr>
              <tr>
                <td>
                  {{$_usermessage->source|smarty:nodefaults}}
                </td>
              </tr>
            </table>
          </div>
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="3">{{tr}}CUserMessage.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</div>

