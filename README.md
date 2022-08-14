<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Acerca del proyecto

El proyecto se hizo con laravel version 9.

Para los casos se utilizó el archivo **routes/api.php**

### Listas de rutas
1. ***/registro/cliente:*** Ruta para registrar cliente, el registro del cliente se hizo en una controller aparte llamado  **RegisterController** Ahí se encuentra el método RegistroCliente.
1. ***billetera/cargar:*** Donde se carga el dinero a la billetera.
1. ***billetera/generar/pago:*** Es donde se genera los pagos.
2. ***billetera/confirmar/{token}/pago:*** Es donde se confirma los pagos, verificar el **token** en correo ya que se envia por correo.
3. ***billetera/saldo:*** Es donde se puede ver el saldo de la billetera.

##Notas:
* Para los envios de correos usé la web de **mailtrap**.
* En el caso de SOAP se vieron ejemplos de respuestas y envios de W3School.
* Si van a usar Postman se adjunta el archivo **epayco.postman_collection.json**.
* Los migration ya fueron creados y se usó PHP v8.1 para el desarrollo.
* Verificar que se usó Traits y se tuvo que usar una malidación aparte de la **Request Laravel** esto por el uso de SOAP.
