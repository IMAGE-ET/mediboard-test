{{mb_script module=maternite script=naissance ajax=1}}
{{assign var=patient value=$operation->_ref_patient}}
{{assign var=sejour value=$operation->_ref_sejour}}
{{assign var=grossesse value=$sejour->_ref_grossesse}}

<script type="text/javascript">
  Main.add(function() {
    Naissance.reloadNaissances('{{$operation->_id}}');

      Control.Tabs.create("tab-grossesse", true, {
        afterChange: function(container) {
          switch (container.id) {
              {{if $conf.dPsalleOp.enable_surveillance_perop}}
                case "surveillance_perop":
                  var url = new Url("salleOp", "ajax_vw_surveillance_perop");
                  url.addParam("operation_id","{{$operation->_id}}");
                  url.requestUpdate("surveillance_perop");
                  break;
              {{/if}}

            case 'grossesse-data':
              var url = new Url('maternite', 'ajax_edit_grossesse', "action");
              url.addParam('grossesse_id', '{{$grossesse->_id}}');
              url.addParam('with_buttons', 1);
              url.addParam('standalone', 1);
              url.requestUpdate("grossesse-data");
              break;
          }
        }
      });
  });
</script>

{{if $patient->nom|is_numeric}}
  <div class="big-info">
     {{tr}}CGrossesse-born_under_x{{/tr}}
  </div>
{{/if}}

<ul class="control_tabs small" id="tab-grossesse">
  <li><a href="#grossesse-data">{{tr}}CGrossesse{{/tr}}</a></li>
  <li><a href="#naissance_area">{{tr}}CNaissance{{/tr}}(s)</a></li>
  {{if $conf.dPsalleOp.enable_surveillance_perop}}
  <li><a href="#surveillance_perop">Partogramme</a></li>
  {{/if}}
</ul>

<div id="grossesse-data"></div>

<div id="naissance_area" style="display: none;"></div>

<div id="surveillance_perop" style="display: none;"></div>