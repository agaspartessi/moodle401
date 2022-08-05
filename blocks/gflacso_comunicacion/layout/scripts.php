<?php 
/*
 * @package     block_gflacso_comunicacion
 * @copyright   2016 FLACSO & Cooperativa de trabajo GENEOS (www.geneos.com.ar}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$PAGE->requires->js_amd_inline("require(['jquery'], function($) {
              $(document).ready(function(){
                  $('.block_gflacso_comunicacion').each(function(){
                         console.log('bloque comunicacion',this);
                        $('.additional_links .copytoclipboard').each(function(){
                              var _this = $(this);
                              _this.on(\"click\", function(){

                                  var email = _this.next('.email');
                                  console.log(email.attr('href'));
                                  //Crea elemento auxiliar 
                                  var aux = document.createElement(\"input\");
                                  aux.setAttribute(\"value\", email.attr('href').replace(\"mailto:\",\"\") );

                                  // Añade el campo a la página
                                  document.body.appendChild(aux);

                                  // Selecciona el contenido del campo
                                  aux.select();

                                  // Copia el texto seleccionado
                                  document.execCommand(\"copy\");

                                  // Elimina el campo de la página
                                  document.body.removeChild(aux);
                              });

                             
                        });
                  });

              })

});");
?>
