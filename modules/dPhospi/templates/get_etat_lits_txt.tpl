{{foreach from=$list_affectations item="affectation"}}
   {{$affectation.nom}};{{$affectation.prenom}};{{$affectation.id}};{{$affectation.service}};{{$affectation.chambre}};{{$affectation.lit}};{{$affectation.sexe}};{{$affectation.naissance}};{{$affectation.date_entree}};{{$affectation.heure_entree}};{{$affectation.date_sortie}};{{$affectation.heure_sortie}};
   {{/foreach}}