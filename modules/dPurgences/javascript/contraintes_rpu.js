ContraintesRPU = {
  contraintesProvenance: [],
  contraintesDestination: [],
  contraintesOrientation: [],

  updateProvenance: function(mode_entree, clearField) {
    var oSelect = document.editRPU._provenance;

    // Le champ peut être caché
    if (!oSelect) {
      return;
    }

    // On remet la valeur à zéro
    if (clearField) {
      oSelect.value = "";
    }

    if (mode_entree == "") {
      $A(oSelect).each( function(input) {
        input.disabled = false;
      });
      return;
    }

    var valeursPossibles = this.contraintesProvenance[mode_entree];

    if (!valeursPossibles) {
      $A(oSelect).each( function(input) {
        input.disabled = true;
      });
      return;
    }

    $A(oSelect).each( function(input) {
      input.disabled = !valeursPossibles.include(input.value);
    });
  },

  checkObligatory : function(rpu_id, callback) {
    new Url("dPurgences", "ajax_check_obligatory")
      .addParam("rpu_id", rpu_id)
      .requestJSON(function (data) {
        if (data.length == 1) {
          callback();
        }
        else {
          var miss_input = "";
          for(var i=1;i<data.length;i++) {
            miss_input += "\n"+$T(data[i]);
          }
          alert("Veuillez renseigner les champs suivants :"+miss_input);
        }
      });
  },

  updateDestination: function(mode_sortie, clearField) {
    var oSelect = document.editRPUDest._destination;

    // Le champ peut être caché
    if (!oSelect) {
      return;
    }

    // On remet la valeur à zéro
    if (clearField) {
      oSelect.value = "";
    }

    if (mode_sortie == "") {
      $A(oSelect).each( function(input) {
        input.disabled = false;
      });
      return;
    }

    var valeursPossibles = this.contraintesDestination[mode_sortie];

    if(!valeursPossibles){
      $A(oSelect).each( function(input) {
        input.disabled = true;
      });
      return;
    }

    $A(oSelect).each( function(input) {
      input.disabled = !valeursPossibles.include(input.value);
    });
  },

  updateOrientation: function(mode_sortie, clearField) {
    var oSelect = document.editRPUDest.orientation;

    // Le champ peut être caché
    if (!oSelect) {
      return;
    }

    // On remet la valeur à zéro
    if (clearField) {
      oSelect.value = "";
    }

    if (mode_sortie == "") {
      $A(oSelect).each( function(input) {
        input.disabled = false;
      });
      return;
    }

    var valeursPossibles = this.contraintesOrientation[mode_sortie];

    if(!valeursPossibles){
      $A(oSelect).each( function(input) {
        input.disabled = true;
      });
      return;
    }

    $A(oSelect).each( function(input) {
      input.disabled = !valeursPossibles.include(input.value);
    });
  }
};
