{{mb_default var=formOp value="editOp"}}
{{mb_default var=formSecondOp value="editOpEasy"}}
{{mb_default var=id_protocole value="get_protocole"}}

<br />
<input type="text" name="search_protocole" style="width: 13em;" placeholder="{{tr}}fast-search{{/tr}}" onblur="$V(this, '')"/>
<div style="display:none;" id="{{$id_protocole}}"></div>
<script>
  ajoutProtocole = function(protocole_id) {
    if (aProtocoles[protocole_id]) {
      ProtocoleSelector.set(aProtocoles[protocole_id]);
      Control.Modal.close();
    }
    else {
      var url = new Url('planningOp', 'ajax_get_protocole');
      url.addParam('protocole_id', protocole_id);
      url.addParam('chir_id'     , $V(getForm('{{$formOp}}').chir_id));
      url.requestUpdate("{{$id_protocole}}");
    }
  }

  Main.add(function () {
    aProtocoles = {
      sejour: {},
      interv: {}
    };
    var oForm = getForm('{{$formOp}}');
    var url = new Url('planningOp', 'ajax_protocoles_autocomplete');
    url.addParam('field'          , 'protocole_id');
    url.addParam('input_field'    , 'search_protocole');
    url.addParam('for_sejour', '0');
    url.autoComplete(oForm.elements.search_protocole, null, {
      minChars: 3,
      method: 'get',
      select: 'view',
      dropdown: true,
      afterUpdateElement: function(field, selected){
        ajoutProtocole(selected.get('id'));
        $V(field.form.libelle, selected.down('strong').getText());
        $V(getForm('{{$formSecondOp}}').libelle, selected.down('strong').getText());
        $V(field.form.elements.search_protocole, "");
      },
      callback: function(input, queryString){
        return queryString + "&chir_id=" + $V(input.form.chir_id);
      }
    });
  });
</script>