<?php 
/*
 * @package     mod_gflacsotext
 * @copyright   2016 FLACSO & Cooperativa de trabajo GENEOS (www.geneos.com.ar}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$PAGE->requires->js_amd_inline("require(['jquery'], function($) {
              $(document).ready(function(){
                  $('.gflacso_items_container').each(function(){
                        var items = 0;
                        var itemsToShow = $qtyshow;
                        _container = $(this)
                        _container.find('.gflacso_items_item').each(function(index){
                              items++;
                              _this = $(this);

                              //Agrego botones a los elementos SML (See More Less)
                              _this.find('.sml').each(function() {
                                    _sml = $(this);
                                    // Solo agrego el buton si existe la opcion FULL cargada
                                    if (_sml.find('.full').length > 0) {
                                          var full = _sml.find('.full');
                                          var short =  _sml.find('.short');                                          
                                          _seeMoreText = '".get_string('seemorelabel', 'block_gflacso_items_viewmore')."';
                                          _seeLessText = '".get_string('seelesslabel', 'block_gflacso_items_viewmore')."';

                                          _sml.append('<a class=\"seemoreless more\" href=\"#\">'+_seeMoreText+'</a>');
                                          _sml.find('.seemoreless').click(function(e){
                                                e.preventDefault();
                                                short.toggle();
                                                full.toggle();
                                                if ($(this).hasClass(\"more\")){
                                                      $(this).removeClass(\"more\");
                                                      $(this).text(_seeLessText);
                                                      $(this).addClass(\"less\")
                                                }
                                                else {
                                                      $(this).removeClass(\"less\");
                                                      $(this).text(_seeMoreText);
                                                      $(this).addClass(\"more\")
                                                }

                                          });
                                    }
                              });

                              //Lo escondo si supera la cantidad inicial
                              if ( (index+1) > itemsToShow)
                                    _this.hide();

                        });
                        _seeAllText = '".get_string('seealllabel', 'block_gflacso_items_viewmore')."';
                        //Si hay mas items que los que se muestran incialmente entonces agrego el boton de ver todos
                        if ( (items) > itemsToShow){
                              _container.append('<a href=\"#\" class=\"btn seeall\">'+_seeAllText+'<a/>');
                              _container.find('.seeall').click(function(e){
                                    e.preventDefault();
                                    _container.find('.gflacso_items_item').show();
                                    $(this).hide();
                              });
                        }


                  });

                 
              })

});");
?>
