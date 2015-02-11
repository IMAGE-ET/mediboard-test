{{if $is_last}}
  <style>
    .textarea-container {
      border:none;
    }

    .list_tabs_doc {
      padding:0;
    }

    .list_tabs_doc li {
      border:solid 1px #cacaca;
      margin-top:5px;
      list-style: none;
    }

    .list_tabs_doc li span.tag_tab {
      border:solid 1px grey;
      cursor:pointer;
      display: inline-block;
      border-radius: 2px;
      margin:0 2px;
      padding:2px;
      background-color: #4e4e4e;
      color:white;
    }
  </style>

  <script>
    createLi = function(elt, target) {
      var _id = ($$('li.line_config').length)+1;

      var _button = DOM.button({'type': 'button', 'className': 'trash', 'onclick': 'this.up().remove();'});

      var _span_atc = DOM.span({'class': 'atc'});
      if (elt) {
        _span_atc.insert(DOM.span({'className' : 'tag_tab'}, elt));
      }
      else {
        var _auto_cp = DOM.input({"id" : 'seek_'+_id, "type" : "text", "name" : 'keywords_atc', "placeholder": "Rercherche"});
      }
      var _div_ac = DOM.div({"style" : "float:right"}, _auto_cp);
      var _line = DOM.li({'className' : 'line_config'}, _button, _span_atc, _div_ac);

      $(target).insert(_line);

      // ac
      var urlATC = new Url("medicament", "ajax_atc_autocomplete");
      urlATC.autoComplete("seek_"+_id, null, {
        minChars: 1,
        dropdown: true,
        updateElement: function(selected) {
          var div = selected.up('li').down("span.atc");
          var name = selected.select(".view")[0].innerHTML.replace(/<em>|<\/em>/g, '');
          div.insert(DOM.span({'class' : "tag_tab", 'onclick': '$(this).remove();'}, name));
          $V("seek"+_id, '');
          $('seek_'+_id).up('div').up('div').style.display = "none";
        }
      });
    };

    // final save for the textarea
    saveText = function() {
      var textarea = getForm("edit-configuration")["c[{{$_feature}}]"][1];
      var textarea_lines = [];

      $$('ul.list_tabs_doc li').each(function(elt) {
        var current_line = "";
        // tags
        elt.select("span.tag_tab").each(function(tag) {
          textarea_lines.push(tag.innerHTML);
        });
      });

      var final_text = textarea_lines.join("|");
      $V(textarea, final_text);

      return true;
    };

    Main.add(function() {
      var form = getForm("edit-configuration");
      var list = $$('ul.list_tabs_doc')[0];
      var field = form["c[{{$_feature}}]"][1];
      var lines = $V(field).split("|");
      if (lines.length) {
        $(lines).each(function (elt) {
          createLi(elt, list);
        });
      }
      {{if $is_inherited}}
        toggleCustomValue($('div_atc_tracabilite'), false);
      {{/if}}
    });
  </script>

  <textarea style="display:none;" name="c[{{$_feature}}]" {{if $is_inherited}} disabled="disabled" {{/if}} class="editable" value="{{$value}}">{{$value}}</textarea>
  <ul class="list_tabs_doc">
  </ul>
  <div style="clear:both;"></div>
  <button type="button" onclick="createLi(null, $$('ul.list_tabs_doc')[0]);" class="add" id="div_atc_tracabilite">Nouvelle classe ATC</button>
  <p style="text-align:center;"><button type="submit" onclick="return saveText()" class="save">Enregistrer</button></p>
{{else}}
  {{if $value}}
    {{assign var=lines value="|"|explode:$value}}
    <ul class="parent_onglets opacity-30">
      {{foreach from=$lines item=_line}}
        <li>{{$_line}}</li>
      {{/foreach}}
    </ul>
  {{/if}}
{{/if}}