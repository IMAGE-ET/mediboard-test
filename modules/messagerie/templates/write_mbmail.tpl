{{if $dialog}}
  {{assign var=destform value="m=$m&dialog=1&a=$action"}}
{{else}}
  {{assign var=destform value="m=$m&tab=$tab"}}
{{/if}}

<script type="text/javascript">
Main.add(function() {
  new Control.Tabs.create('tabs-mbmail');
});
</script>

<ul id="tabs-mbmail" class="control_tabs">
  <li><a href="#tab_mail">{{if $mbmail->_id}}
        {{tr}}CMbMail-title-modify{{/tr}} '{{$mbmail}}'
      {{else}}
        {{tr}}CMbMail-title-create{{/tr}}
      {{/if}}
      </a>
  </li>
  <li>
    <a href="#historique" {{if !$historique|@count}}class="empty"{{/if}}>Historique ({{$historique|@count}})</a>
  </li>
</ul>
<hr class="control_tabs" />

<div id="tab_mail">
  <form name="EditMbMail" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="dosql" value="do_mbmail_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="mbmail_id" value="{{$mbmail->_id}}" />
    <input type="hidden" name="postRedirect" value="{{$destform}}" />
    {{if !$mbmail->date_sent}}
      {{mb_field object=$mbmail field=date_sent hidden=true}}
    {{else}}
      {{mb_field object=$mbmail field=archived hidden=true}}
      {{mb_field object=$mbmail field=starred hidden=true}}
    {{/if}}
    <table class="form">
      
      <tr>
        <th class="narrow">{{mb_label object=$mbmail field=from}}</th>
        <td>
          {{mb_field object=$mbmail field=from hidden=1}}
          <div class="mediuser" style="border-color: #{{$mbmail->_ref_user_from->_ref_function->color}};">
            {{$mbmail->_ref_user_from}}
          </div>
        </td>
      </tr>
    
      {{if $mbmail->date_sent}}
      <tr>
        <th>{{mb_label object=$mbmail field=date_sent}}</th>
        <td>{{mb_value object=$mbmail field=date_sent}} ({{mb_value object=$mbmail field=date_sent format=relative}})</td>
      </tr>
      {{/if}}
    
      <tr>
        <th>{{mb_label object=$mbmail field=to}}</th>
        <td>
          {{if $mbmail->date_sent}}
          <div class="mediuser" style="border-color: #{{$mbmail->_ref_user_to->_ref_function->color}};">
            {{$mbmail->_ref_user_to}}
          </div>
          {{else}}
            {{mb_field object=$mbmail field=to hidden=true}}
            <input type="text" name="_to_autocomplete_view" style="width: 16em;" class="autocomplete" value="{{$mbmail->_ref_user_to}}"
              onchange='if(!this.value){this.form.user_id.value=""}' />
            
            <script type="text/javascript">
              Main.add(function(){
                var form = getForm("EditMbMail");
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
    
      {{if $mbmail->date_read}}
      <tr>
        <th>{{mb_label object=$mbmail field=date_read}}</th>
        <td>{{mb_value object=$mbmail field=date_read}} ({{mb_value object=$mbmail field=date_read format=relative}})</td>
      </tr>
      {{/if}}
    
      {{if $mbmail->archived}}
      <tr>
        <th>{{mb_label object=$mbmail field=archived}}</th>
        <td><strong>{{mb_value object=$mbmail field=archived}}</strong></td>
      </tr>
      {{/if}}
    
      {{if $mbmail->starred}}
      <tr>
        <th>{{mb_label object=$mbmail field=starred}}</th>
        <td><strong>{{mb_value object=$mbmail field=starred}}</strong></td>
      </tr>
      {{/if}}
    
      <tr>
        <th>{{mb_label object=$mbmail field=subject}}</th>
        <td>
          {{if $mbmail->date_sent}}
            {{mb_value object=$mbmail field=subject}}
          {{else}}
            {{mb_field object=$mbmail field=subject size=80}}
          {{/if}}
        </td>
      </tr>
    
      <tr>
        <td colspan="2" style="height: 300px">{{mb_field object=$mbmail field=source id="htmlarea"}}</td>
      </tr>
    
      {{if !$mbmail->date_sent}}
      <tr>
        <td colspan="2" style="text-align: center;">
          <button type="submit" class="send" onclick="$V(this.form.date_sent, 'now');">{{tr}}Send{{/tr}}</button>
          <button type="submit" class="submit">{{tr}}Save{{/tr}} {{tr}}Draft{{/tr}}</button>
        </td>
      </tr>
      {{elseif $mbmail->to == $app->user_id}}
      <tr>
        <td colspan="2" style="text-align: center;">
          <button type="button" onclick="window.parent.Control.Modal.close(); window.parent.MbMail.create({{$mbmail->_ref_user_from->_id}}, 'Reponse'); ">
            <img src="images/icons/mbmail.png" alt="message"/>
            {{tr}}CMbMail.answer{{/tr}}
          </button>
          {{if !$mbmail->starred}}
            {{if $mbmail->archived}}
            <button type="submit" class="cancel" onclick="$V(this.form.archived, '0');">{{tr}}Unarchive{{/tr}}</button>
            {{else}}
            <button type="submit" class="change" onclick="$V(this.form.archived, '1');">{{tr}}Archive{{/tr}}</button>
            {{/if}}
          {{/if}}
                
          {{if !$mbmail->archived}}
            {{if $mbmail->starred}}
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
      <th style="width: 20%">{{mb_label object=$mbmail field=from}}</th>
      <th style="width: 20%;">{{mb_label object=$mbmail field=to}}</th>
      <th>{{mb_label object=$mbmail field=subject}}</th>
    </tr>
    {{foreach from=$historique item=_mbmail}}
      <tr>
        <td>
          {{$_mbmail->_ref_user_from}}
        </td>
        <td>
          {{$_mbmail->_ref_user_to}}
        </td>
        <td>
          <a href="#1" onmouseover="ObjectTooltip.createDOM(this, 'mbmail_{{$_mbmail->_id}}')">{{$_mbmail->subject}}</a>
          <div style="display: none" id="mbmail_{{$_mbmail->_id}}">
            <table class="tbl">
              <tr>
                <th class="category">Contenu du message</th>
              </tr>
              <tr>
                <td>
                  {{$_mbmail->source|smarty:nodefaults}}
                </td>
              </tr>
            </table>
          </div>
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="3">{{tr}}CMbMail.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</div>

