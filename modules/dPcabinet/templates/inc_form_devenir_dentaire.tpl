<table class="main">
  <tr>
    <td>
      <form name="editDevenir" action="?" method="post"
        onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_devenir_dentaire_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$devenir_dentaire}}
        {{mb_field object=$devenir_dentaire field=patient_id hidden=1}}
        <input type="hidden" name="callback" value="editProjet" />
        {{mb_field object=$devenir_dentaire field=etudiant_id hidden=1}}
        <table class="form">
          {{mb_include object=$devenir_dentaire module=system template=inc_form_table_header colspan=3}}
          <tr>
            <td style="width: 50%">
              <fieldset>
                <legend>{{mb_label object=$devenir_dentaire field=description}}</legend>
                {{mb_field object=$devenir_dentaire field=description}}
              </fieldset>
            </td>
            <td style="width: 50%">
              <fieldset>
                <legend>Etudiant</legend>
                {{if $devenir_dentaire->_id}}
                  <button type="button" style="float: right;" class="search" onclick="chooseEtudiant('{{$devenir_dentaire->_id}}')">Choisir un étudiant</button>
                  <strong>Etudiant choisi :</strong>
                  {{if $devenir_dentaire->etudiant_id}}
                    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$devenir_dentaire->_ref_etudiant}}
                  {{else}}
                    Aucun
                  {{/if}}
                {{else}}
                  {{tr}}CDevenirDentaire.alert_new_project{{/tr}}
                {{/if}}
              </fieldset>
            </td>
          </tr>
          <tr>
            <td colspan="3" style="text-align: center;">
              <button type="button" class="submit" onclick="this.form.onsubmit();">{{tr}}Save{{/tr}}</button>
              {{if $devenir_dentaire->_id}}
                <button type="button" class="trash" onclick="$V(this.form.del, 1); this.form.onsubmit();">{{tr}}Delete{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  {{if $devenir_dentaire->_id}}
    <tr>
      <td>
        <form name="editActeDentaire" action="?" method="post" 
          onsubmit="return onSubmitFormAjax(this, {onComplete: function() { updateRank(1); }})">
          <input type="hidden" name="m" value="dPpatients" />
          <input type="hidden" name="dosql" value="do_acte_dentaire_aed" />
          <input type="hidden" name="acte_dentaire_id" value="" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="callback" value="afterActeDentaire"/>
          <input type="hidden" name="devenir_dentaire_id" value="{{$devenir_dentaire->_id}}" />
          <input type="hidden" name="code" value=""/>
          <input type="hidden" name="rank" value="{{$acte_dentaire->rank}}" />
          
          <fieldset>
            <legend>Ajouter un code</legend>
            <input name="_selCode" type="hidden" value="" />
            <button class="search" type="button" onclick="CCAMSelector.init()">
              {{tr}}Search{{/tr}}
            </button>
         
            <input type="text" size="10" name="_codes_ccam" onchange="$V(this.form.code, $V(this))" />
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
              };
        
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
      </td>
    </tr>
  {{/if}}
</table>
<div id="list_actes_dentaires">
  {{mb_include module=cabinet template=inc_list_actes_dentaires}}
</div>