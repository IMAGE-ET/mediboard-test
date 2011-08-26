Event.observe( window , 'load', init );

function init(){//ok
	
	//Tous les éléments draggables
	elements=$('divGauche').getElementsByTagName('td');
	for (var i = 0; i < elements.length; i++) { 
	   new Draggable( elements[i].id, {revert:true});
	}
	
	elements=$('divDroite').getElementsByTagName('span');
	for (var i = 0; i < elements.length; i++) { 
		if(elements[i].id){new Draggable( elements[i].id, {revert:true});}	 	
	}
	
	//Toutes les zones droppables
	Droppables.add('divGauche',{onDrop:TraiterDrop});
	
	elements=document.getElementById('divDroite').getElementsByTagName('div');
	for (var i = 0; i < elements.length; i++) { 
		Droppables.add(elements[i].id,{onDrop:TraiterDrop});
	}
}

function TraiterDrop(element, zoneDrop)
{
	zoneDrop.appendChild(element); // Ajouter un fils à 'zoneDrop'
}

function savePlan(){
/*	
 * //Enregistrement des salles placées sur le plan déplacées ou nouvellement placées
	elements=$('divDroite').getElementsByTagName('div');
	for (var i = 0; i < elements.length; i++) { 
		var url=new Url("dPhospi", "ajax_modif_plan_etage");
		url.addParam("chambre_id",elements[i].id);
		if(elements[i].parentNode.parentNode.id[4]!=null){
			url.addParam("zone",elements[i].parentNode.parentNode.id[4]+elements[i].parentNode.parentNode.id[5]);
		}
		url.requestUpdate('id');
	}
	
	//Enregistrement des salles non placées
	elements=$('divGauche').getElementsByTagName('div');
	for (var i = 0; i < elements.length; i++) { 

		var url=new Url("dPhospi", "ajax_modif_plan_etage");
		url.addParam("chambre_id",elements[i].id);
		url.addParam("zone",null);
		url.requestUpdate('id');
	
}
*/	
	}