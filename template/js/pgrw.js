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
        publicMethod.modal  	= $('<div class="modal fade " tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"></div><div class="modal-footer"></div></div></div></div>');
				publicMethod.loading 	= $('<div class="progress"><div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div></div>');
				/*QUITO LOS EVENTOS LINKS Y ASIGNO NUEVO*/
        publicMethod.links  = $('a').on("click", function (e) {
					e.preventDefault();
          asignar_eventos_ajax($(this));
        });
				init();
      }

			function init(){
				toggle_pass();
				$('#main').height();
				var hash 	= location.hash.substr(1);
				tag				=	$("a[href='#"+hash+"']")
				//localStorage.clear();
				var objeto_inicial	=	{href:"#",title:"JobEPR",id:0,url:document.location.href}
				if(localStorage.getItem("navegacion")===null){
					localStorage.setItem("navegacion", JSON.stringify(objeto_inicial));
				}
				//console.log(objeto_inicial)
				var navegacion = JSON.parse(localStorage.getItem("navegacion"));
				if(tag && navegacion){
					$("div#historyback").attr("href",navegacion.href)
														.data("title",navegacion.title)
														.attr("data-id",navegacion.id)
														.attr("data-url",navegacion.url);
					if(navegacion.url!==''){
						publicMethod.fetch($("div#historyback"));
					}
				}

				window.onpopstate = function(event) {
					if(event.state){
						localStorage.setItem("navegacion", JSON.stringify(event.state));
						$("div#historyback").attr("href",event.state.href).attr("data-url",event.state.url).attr("data-title",event.state.title).attr("data-id",event.state.id);
						publicMethod.fetch($("div#historyback"));
					}
				};
			}

			function toggle_pass(){
				$(".toggle_pass").click(function(){
					var objeto	=	$($(this).data("rel"));
					if(objeto.attr("type") ==="password"){
						objeto.attr("type","text");
						$(this).html($(this).data("inactive"))
					}else{
						objeto.attr("type","password");
						$(this).html($(this).data("active"))
					}
				});
			}

			function asignar_eventos_ajax(tag){
				// if((tag.data("type")==='modal' || tag.data("type")==='iframe') && tag.attr("href")!='#'){
				// 	publicMethod.iframe(tag);
				// 	return;
				// }
				if(tag.attr("href")!='#' && !tag.data("formato") ){
					publicMethod.fetch(tag);
        }else if(tag.attr("href")!='#' && tag.data("formato")==='json' ){
					$.post(tag.attr("data-url"), function(data){
						if(data.redirect){
							document.location.href=data.redirect;
						}
	        },"json");
				}
      }

			publicMethod.iframe = function(tag){
				modal("",tag);
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
									objeto_link.data("content",'iframe');
									objeto_link.data("size",'modal-lg');
									objeto_link.data("height",480);
									modal("",objeto_link);
									return false;
								}else{
									publicMethod.fetch($(this));
									var obj_push={href:tag.attr("href"),url:tag.attr("data-url"),title:tag.attr("data-title"),id:tag.attr("data-id")}
									//console.log("Se actualiza")
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
          modal("Ocurrió un error, no pudo consultarse "+message);
        });
      }

		  function modal(message,obj){
        var modal   =	publicMethod.modal.clone();
				var size		=	"modal-md";
				var height	=	220;
				if(!obj){obj={};}
				if(obj.data("size")){
					size	=	obj.data("size");
				}
				if(obj.data("height")){
					height	=	obj.data("height");
				}
				if(obj.data("content")==='iframe'){
					modal.find(".modal-body").height(height);
					var iframe	=	$('<iframe allowfullscreen width="100%" height="100%" class="iframe" src="'+obj.data("url")+'"></iframe>');
					modal.find(".modal-body").html(iframe);
				}else if(obj.data("content")==='ajax'){
					$.post(obj.attr("data-url"), function(data){
						var contenido	=	$('<div class="">'+data+'</div>');
						modal.find(".modal-body").html(contenido);
	        });
				}else{
					modal.find(".modal-body").html('<div class="text-center">'+message+'</div>');
				}
        var contenido  	=	modal.find(".modal-dialog").find(".modal-content");
        modal.addClass("pgrw_modal_alert_"+pluginName).attr("aria-labelledby","modalLabel_alert_"+pluginName).find(".modal-dialog").addClass(size);
        contenido.find(".modal-header").html('<h5 class="m-0 p-0">Atención </h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        //contenido.find(".modal-footer").html('<!--button type="button" class="btn btn-primary aceptar" data-dismiss="modal">Aceptar</button-->');
        $("body").append(modal);
		    modal.modal({ keyboard: false});
				modal.on('hidden.bs.modal', function() {
					modal.remove();
				});
				//contenido.find(".modal-body")
				modal.find(".modal-header").on("mousedown", function(mousedownEvt) {
						var $draggable = $(this);
						var x = mousedownEvt.pageX - $draggable.offset().left,
						y = mousedownEvt.pageY - $draggable.offset().top;
						$("body").on("mousemove.draggable", function(mousemoveEvt) {
							$draggable.closest(".modal-dialog").offset({
								"left": mousemoveEvt.pageX - x,
								"top": mousemoveEvt.pageY - y
							});
						});
						$("body").one("mouseup", function() {
							$("body").off("mousemove.draggable");
						});
						$draggable.closest(".modal").one("bs.modal.hide", function() {
							$("body").off("mousemove.draggable");
						});
				});
    	}

			function test(){
				alert(5);
			}

      $(document).ready(function(){
        $.pgrwAjax();
      });


}(jQuery, document, window));

jQuery.fn.SoloNumeros =
  function() {
    return this.each(function() {
      $(this).keydown(function(e) {
        var key = e.charCode || e.keyCode || 0;
        // allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
        return (
          key == 8 ||
          key == 9 ||
          key == 46 ||
          (key >= 37 && key <= 40) ||
          (key >= 48 && key <= 57) ||
          (key >= 96 && key <= 105));
      });
    });
  };
