Event.observe( window , 'load', init );

function init(){
	
	//Tous les éléments draggables
	elements=$('divGauche').getElementsByTagName('div');
	for (var i = 0; i < elements.length; i++) { 
	   new Draggable( elements[i].id, {revert:true});
	}
	
	/*
	elements=$('divDroite').getElementsByTagName('div');
	for (var i = 0; i < elements.length; i++) { 
		new Draggable( elements[i].id, {revert:true});	 	
	}
	*/
	
	//Toutes les zones droppables
	Droppables.add('divGauche',{onDrop:TraiterDrop});
	
	elements=document.getElementById('divDroite').getElementsByTagName('td');
	for (var i = 0; i < elements.length; i++) { 
		Droppables.add(elements[i].id,{onDrop:TraiterDrop});
	}
}

function TraiterDrop(element, zoneDrop)
{
	zoneDrop.appendChild(element); // Ajouter un fils à 'zoneDrop'
}

function savePlan(){
	
}