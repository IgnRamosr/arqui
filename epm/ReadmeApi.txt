Utilización de metodos POSTMAN

1- Obtener usuario (GET)

 -Colocar metodo GET en POSTMAN
 
 - Colocar la url de la función ' http://localhost/ionic-backend/save-data.php?action=getUserById&id= '




2- Eliminar usuario(GET)

 -Colocar metodo GET en POSTMAN
 
 - Colocar la url de la función ' http://localhost/ionic-backend/save-data.php?action=deleteUser&id= '


3- Editar usuario(POST)

 -Colocar metodo POST en POSTMAN
 
 - Colocar la url de la función ' http://localhost/ionic-backend/save-data.php?action=updateUser '

 - Cambiar de etiqueta Params a Body y seleccionar raw
 
 - En la etiqueta text se cambia por formato JSON y se envían datos en formato JSON 
   como muestra el ejemplo

{
  "id": 19,
  "data":{
  "nombre": "Pepe",
  "apellido": "Grillo",
  "direccion": "Direccion 3",
  "numTarjCred": "8888 9999 1111 2222",
  "banco": "Santander",
  "fechaVencimiento": "13-02-2030",
  "codCvv": 123
}
}