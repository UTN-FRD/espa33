# espa33
Sistema de biblioteca de la Facultad Regional Delta de la Universidad Tecnológica Nacional.
## ¿Qué es?
El sistema es un fork de EspaBiblio 3.3 Giordano Bruno con la interfaz actualizada con Bootstrap.
Se añadió la posibilidad de manejar los libros mediante RFID.
Se terminó el modulo de usuario.
Se corrigieron muchos errores.
### Instalación

    git clone https://github.com/brunosagaste/espa33 && cd espa33
    docker-compose up --build -d

Igual que EspaBiblio. A la base de datos de EspaBilbio se le añadió una columna rfid_number de tipo varchar al final de biblio_copy, se cambió renewal_count a bigint en biblio_copy y biblio_status_hist, y se cambio session_timeout a bigint en settings.
Estos cambios deben hacerse manualmente luego de la instalación ya que no se generan automáticamente.
## Herramientas
PHP, MySql y Bootstrap.
## Autores
Software Factory, Bruno Sagaste y Bruno Fernández del Laboratorio de Sistemas de Información de la UTN FRD.
## Licencia
GPLv2
