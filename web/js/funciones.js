/**
 * Funciones auxiliares de javascripts 
 */
function confirmarBorrar(nombre,id){
  if (confirm("¿Quieres eliminar el cliente:  "+nombre+"?"))
  {
   document.location.href="?orden=Borrar&id="+id;
  }
}







function gestionandoevento(evento) {
  if (document.readyState == 'complete') {
    console.log(evento)

    console.log("------------------------")

    console.log(evento.srcElement.location.search)
  }
}


document.addEventListener('readystatechange',gestionandoevento,false);



/*
    navigation.addEventListener("navigate", e => {
      console.log(`navigate ->`,e.destination.url)
      console.log('page changed');
    });

    window.addEventListener('popstate', function (event) {
      // Log the state data to the console
      console.log(event.state);
    });
*/