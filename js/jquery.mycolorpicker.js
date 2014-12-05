/** 
 * Créez un sélecteur de couleur en jQuery 
 * par Jay Salvat - http://blog.jaysalvat.com/ 
 */ 
(function($) { 
   $.fn.myColorPicker = function() { 
      return this.each(function(){ 
         var $$ = $(this);    
         var x  = $$.offset().left; 
         var y  = $$.offset().top + $$.outerHeight(true); 
          
         // Lorsque le curseur entre dans le champ de saisi 
         $$.focus(function() { 
            buildColorPicker(); 
         }); 
 
         // Fonction de création de la palette 
         function buildColorPicker() { 
            // On supprime d'éventuelles autres palettes déjà ouvertes 
            removeColorPicker(); 
 
            // On construit le Html de la palette 
            var values  = ['00', '33', '66', '99', 'CC', 'FF'];  
            var content = ''; 
            content += '<div id="myColorPicker">'; 
            content += '<ul>'; 
            for(r = 0; r < 6; r++) {  
               for(g = 0; g < 6; g++) {  
                  for(b = 0; b < 6; b++) {  
                     color = '#' + values[r] + values[g] + values[b];  
                     content += '<li><a rel="'+ color +'" style="background:'+ color +'" title="'+ color +'" alt="'+ color +'"></a></li>';  
                   }  
               }  
            } 
            content += '</ul>';  
            content += '<a class="close">Fermer</a>';  
            content += '</div>';  
 
            // On la place dans la page aux coordonnées du textfield 
            $(content).css({  
               position:'absolute',  
               left:x,  
               top:y, 
               backgroundColor:$$.val()   
            }).appendTo('body'); 
             
            // Au survol d'une couleur, on change le fond de la palette 
            $('#myColorPicker a').hover(function() {                 
                $('#myColorPicker').css('backgroundColor', $(this).attr('rel') ); 
            }, function() { 
                $('#myColorPicker').css('backgroundColor', $$.val() );         
            }); 
 
            // Lorsqu'une couleur est cliqué, on affiche la valeur dans le textfield 
            $('#myColorPicker a').not('.close').click(function() { 
               $$.val( $(this).attr('rel') ); 
               removeColorPicker(); 
               return false; 
            }); 
 
            // Au survol d'une couleur, on change le fond 
            $('#myColorPicker a').mouseover(function() { 
               $('#myColorPicker').css('backgroundColor', $(this).attr('rel') ); 
            }); 
 
            // On supprime la palette si le lien "Fermer" est cliqué 
            $('#myColorPicker a.close').click(function() { 
               removeColorPicker(); 
               return false; 
            });
			
			$( '#myColorPicker a' ).not( '.close' ).click(
				function()
				{
					var color = $( this ).attr( 'rel' );
					var r = color.substr( 1, 2 );
					var g = color.substr( 3, 2 );
					var b = color.substr( 5, 2 );
					$$.val( color );
					$$.css( 'background-color', color );
					if( ( parseInt( r, 16 ) * 0.3 ) + ( parseInt( g, 16 ) * 0.59 ) + ( parseInt( b, 16 ) * 0.11 ) > 128 )
					{
					$$.css( 'color', 'black' );
					}
					else
					{
					$$.css( 'color', 'white' );
					}
					removeColorPicker();
					return false;
				} 
			);
			
			
			
         } 
 
         // Fonction de suppression de la palette 
         function removeColorPicker() { 
            $('#myColorPicker').remove(); 
         }    
      }); 
   }; 
})(jQuery); 