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
        publicMethod.contenedor = $('#main_ajax');
				/*ACÁ LOS LA MODAL ALERTA CLONAR*/
        publicMethod.modal  	= $('<div class="modal fade " tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"></div><div class="modal-body"></div><div class="modal-footer"></div></div></div></div>');
				publicMethod.loading 	= $('<div class="progress"><div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div></div>');
				/*QUITO LOS EVENTOS LINKS Y ASIGNO NUEVO*/
        publicMethod.links  = $('a').on("click", function (e) {
          e.preventDefault();
          asignar_eventos_ajax($(this));
        });
				init();
      }

			function init(){
				$('#main').height();
				var hash 	= location.hash.substr(1);
				tag				=	$("a[href='#"+hash+"']")
				//localStorage.clear();
				var objeto_inicial	=	{href:"#",title:"JobEPR",id:0,url:document.location.href}
				if(localStorage.getItem("navegacion")===null){
					//localStorage.setItem("navegacion", JSON.stringify(objeto_inicial));
				}
				//console.log(objeto_inicial)
				var navegacion = JSON.parse(localStorage.getItem("navegacion"));
				if(tag){
					$("div#historyback").attr("href",navegacion.href)
														.data("title",navegacion.title)
														.attr("data-id",navegacion.id)
														.attr("data-url",navegacion.url);
					publicMethod.fetch($("div#historyback"));
				}

				window.onpopstate = function(event) {
					localStorage.setItem("navegacion", JSON.stringify(event.state));
					$("div#historyback").attr("href",event.state.href).attr("data-url",event.state.url).attr("data-title",event.state.title).attr("data-id",event.state.id);
					publicMethod.fetch($("div#historyback"));
				};
			}

			function asignar_eventos_ajax(tag){
        if(tag.attr("href")!='#'){
					publicMethod.fetch(tag);
        }
      }

			publicMethod.fetch_iframe = function(tag){
				$.fancybox.open({
					src  : 'link-to-your-page.html',
					type : 'iframe',
					opts : {
						afterShow : function( instance, current ) {
							console.info( 'done!' );
						}
					}
				});
			}

      publicMethod.fetch = function(tag){
				publicMethod.contenedor.html(publicMethod.loading);
        $.post(tag.attr("data-url"), function(data){
        },tag.attr("formato")).done(function(data) {
					var html	=	$(data);
					publicMethod.contenedor.html(html);

					var obj_push={href:tag.attr("href"),url:tag.attr("data-url"),title:tag.attr("data-title"),id:tag.attr("data-id")}
					console.log("Se actualiza")
					window.history.pushState(obj_push, "Title", tag.attr("href"));
					localStorage.setItem("navegacion", JSON.stringify(obj_push));
					$(document).attr("title", tag.attr("data-title"));

					var a_en_ajax=html.find("a");
					if(a_en_ajax.length > 0){
						a_en_ajax.each(function(){
							$(this).click(function(e){
								e.preventDefault();
								if($(this).hasClass("pgrw_iframe")){
									var objeto_link	=	$(this);
									$.fancybox.open({
										src  : objeto_link.data("url"),
										type : 'iframe',
										animationEffect:"elastic",
										opts : {
											afterShow : function( instance, current ) {
												//console.info( 'done!' );
											}
										}
									});
									return false;
								}else{
									publicMethod.fetch($(this));
									var obj_push={href:tag.attr("href"),url:tag.attr("data-url"),title:tag.attr("data-title"),id:tag.attr("data-id")}
									console.log("Se actualiza")
									window.history.pushState(obj_push, "Title", tag.attr("href"));
									localStorage.setItem("navegacion", JSON.stringify(obj_push));
									$(document).attr("title", tag.attr("data-title"));
								}
							});
						})
					}
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
