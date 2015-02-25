
{{assign var=onchange_mode value='return true;'}}

{{if $mode->_class == 'CModeSortieSejour'}}
  <script type="text/javascript">
    changeDestination = function(form) {
      //Contrainte à appliquer pour la destination
      var contrainteDestination = {
        "mutation"  : ["", 1, 2, 3, 4],
        "transfert" : ["", 1, 2, 3, 4],
        "normal"    : ["", 0, 6, 7],
        "deces"     : ["", 0]
      };

      var destination = form.elements.destination;
      var mode_sortie = $V(form.elements.mode);

      // Aucun champ trouvé
      if (!destination) {
        return true;
      }

      //Pas de mode de sortie, activation de tous les options
      if (!mode_sortie) {
        $A(destination).each(function (option) {
          option.disabled = false
        });
        return true;
      }

      //Application des contraintes
      $A(destination).each(function (option) {
        option.disabled = !contrainteDestination[mode_sortie].include(option.value);
      });

      if (destination[destination.selectedIndex].disabled) {
        $V(destination, "");
      }

      if (!$V(destination) && destination.hasClassName("notNull") && (mode_sortie == "deces" || mode_sortie == "normal")) {
        $V(destination, "0");
      }

      return true;
    }

    changeOrientation = function(form) {
      //Contrainte à appliquer pour l'orientation
      var contrainteOrientation = {
        "mutation"  : ["", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST"],
        "transfert" : ["", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST"],
        "normal"    : ["", "FUGUE", "SCAM", "PSA", "REO"],
        "deces"     : [""]
      };

      var orientation = form.elements.orientation;
      var mode_sortie = $V(form.elements.mode);

      // Aucun champ trouvé
      if (!orientation) {
        return true;
      }

      //Pas de mode de sortie, activation de tous les options
      if (!mode_sortie) {
        $A(orientation).each(function (option) {
          option.disabled = false
        });

        return true;
      }

      //Application des contraintes
      $A(orientation).each(function (option) {
        option.disabled = !contrainteOrientation[mode_sortie].include(option.value);
      });
      if (orientation[orientation.selectedIndex].disabled) {
        $V(orientation, "");
      }

      return true;
    }

    Main.add(function() {
      var form = getForm('edit-mode-{{$mode->_class}}');
      changeDestination(form);
      changeOrientation(form);
    });
  </script>

  {{assign var=onchange_mode value='changeDestination(this.form); changeOrientation(this.form);'}}
{{/if}}

<form name="edit-mode-{{$mode->_class}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this, function(){document.location.reload()})">
  <input type="hidden" name="m" value="planningOp" />
  {{mb_class object=$mode}}
  {{mb_key object=$mode}}
  {{mb_field object=$mode field=group_id hidden=true}}

  <table class="main form">
    {{mb_include module=system template=inc_form_table_header object=$mode}}

    <tr>
      <th>{{mb_label object=$mode field=code}}</th>
      <td>{{mb_field object=$mode field=code}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$mode field=libelle}}</th>
      <td>{{mb_field object=$mode field=libelle}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$mode field=mode}}</th>
      <td>{{mb_field object=$mode field=mode onchange=$onchange_mode}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$mode field=actif}}</th>
      <td>{{mb_field object=$mode field=actif}}</td>
    </tr>

    {{if $mode->_class == 'CModeSortieSejour'}}
      <tr>
        <th>{{mb_label object=$mode field=destination}}</th>
        <td>{{mb_field object=$mode field=destination}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$mode field=orientation}}</th>
        <td>{{mb_field object=$mode field=orientation}}</td>
      </tr>
    {{/if}}

    <tr>
      <td colspan="2" class="button">
        {{if $mode->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax: true, typeName:'', objName:'{{$mode->_view|smarty:nodefaults|JSAttribute}}'},function(){document.location.reload()})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
