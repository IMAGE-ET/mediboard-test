<script type="text/javascript">
  afterActeDentaire = function(id, obj) {
    var url = new Url("dPpatients", "ajax_list_actes_dentaires");
    url.addParam("devenir_dentaire_id", obj.devenir_dentaire_id);
    url.requestUpdate("list_actes_dentaires");
    var form = getForm("editActeDentaire");
    $V(form.code, "");
    $V(form._codes_ccam, "");
    $V(form.commentaire, "");
  }
</script>
<form name="editActeDentaire" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_acte_dentaire_aed" />
  <input type="hidden" name="acte_dentaire_id" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="afterActeDentaire"/>
  <input type="hidden" name="_chir" value="{{$userSel->_id}}" />
  <input type="hidden" name="_patient_id" value="{{$consult->patient_id}}" />
  <input type="hidden" name="_object_class" value="{{$consult->_class}}" />
  <input type="hidden" name="code" value=""/>
  <fieldset>
    <legend>Ajouter un code</legend>
    <input name="_selCode" type="hidden" value="" />
    <button class="search" type="button" onclick="CCAMSelector.init()">
      {{tr}}Search{{/tr}}
    </button>
 
    <input type="text" size="10" name="_codes_ccam" />
    <button class="add" name="addCode" type="button" onclick="this.form.onsubmit();">
      {{tr}}Add{{/tr}}
    </button>
    <br />
    
    {{mb_field object=$acte_dentaire field=commentaire}}
    
    <script type="text/javascript">   
      CCAMSelector.init = function(){
        this.sForm = "editActeDentaire";
        this.sClass = "_object_class";
        this.sChir = "_chir";
        this.sView = "_codes_ccam";
      this.pop();
      }

      var form = getForm("editActeDentaire");
      Main.add(function() {
        var url = new Url("dPccam", "httpreq_do_ccam_autocomplete");
        url.autoComplete(form._codes_ccam, '', {
          minChars: 1,
          dropdown: true,
          width: "250px",
          updateElement: function(selected) {
            $V(form.code, selected.down("strong").innerHTML);
            form.onsubmit();
          }
        });
      });
    </script>
  </fieldset>
</form>
<div id="list_actes_dentaires">
  {{mb_include module=dPpatients template=inc_list_actes_dentaires}}
</div>
