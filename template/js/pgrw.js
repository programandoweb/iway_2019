(function ($, document, window, undefined ) {

	var pluginName 		= 	'pgrwAjax',
			defaults 		= 	{
				message:{"form_require":'This data is required'},
				hasestatus:{"success":'has-success',"warning":'has-warning',"danger":'has-danger'},
				formcontrol:{"success":'form-control-success',"warning":'form-control-warning',"danger":'form-control-danger'},
				contenedor_message:$('<div class="form-control-feedback"/>')
			},
		  publicMethod,

      publicMethod 	=	$[pluginName]	=	function (options) {
        publicMethod.contenedor = $('#main');
        /*ACÁ LOS LA MODAL ALERTA CLONAR*/
        publicMethod.modal  = $('<div class="modal fade " tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"></div><div class="modal-body"></div><div class="modal-footer"></div></div></div></div>');
        /*QUITO LOS EVENTOS LINKS Y ASIGNO NUEVO*/
        publicMethod.links  = $('a').on("click", function (e) {
          e.preventDefault();
          asignar_eventos_ajax($(this));
        });

      }

      function asignar_eventos_ajax(tag){
        if(tag.attr("href")!='#'){
          publicMethod.fetch(tag);
        }
      }

      publicMethod.fetch = function(tag){
        $.post(publicMethod.contenedor.data("url"), function(data){
          console.log(data);
        },'json').done(function(data) {

        }).fail(function(data) {
          var message = "";
          if(data.error){ message = " :"+data.error;}
          alerta("Ocurrió un error, no pudo consultarse "+message);
        });
      }

      function alerta(message){
        modal          =	publicMethod.modal.clone();
        modal.find(".modal-body").html('<div class="text-center">'+message+'</div>');
        var contenido  	=	modal.find(".modal-dialog").find(".modal-content");
            modal.addClass("pgrw_modal_alert_"+pluginName).attr("aria-labelledby","modalLabel_alert_"+pluginName).find(".modal-dialog").addClass("modal-md");
            contenido.find(".modal-header").html("<h5>Atención</h5>");
            contenido.find(".modal-footer").html('<button type="button" class="btn btn-primary aceptar" data-dismiss="modal">Aceptar</button>');
            $("body").append(modal);
    		    modal.modal({ keyboard: false})
    	}

      $(document).ready(function(){
        $.pgrwAjax();
      });

}(jQuery, document, window));
