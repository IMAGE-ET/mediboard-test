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
        if (data.length == 0) {
          callback();
        }
        else {
          var miss_input = [];
          for(var i=0;i<data.length;i++) {
            miss_input[i] = DOM.li(null, $T(data[i]));
          }
          Modal.alert(
            DOM.div(null,
              DOM.p(null, "Le paramétrage de votre établissement impose la saisie d'un certain nombre de champs."),
              DOM.p(null, "Veuillez renseigner les champs suivants :"),
            DOM.ul(null, miss_input)), {className: "modal alert big-info"});
        }
      });

    return false;
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
  },

  //@todo a factoriser avec updateOrientation
  //Changement de l'orientation en fonction du mode sortie
  changeOrientation : function(form) {
    //Contrainte à appliquer pour l'orientation
    var contrainteOrientation = {
      "mutation"  : ["", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST"],
      "transfert" : ["", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST"],
      "normal"    : ["", "FUGUE", "SCAM", "PSA", "REO"],
      "deces"     : [""]
    };

    var orientation = form.elements.orientation;
    var mode_sortie = $V(form.elements.mode_sortie);

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
};
