(function ($, document, window, undefined ) {

	var 	pluginName 		= 	'pgrwForms',
			defaults 		= 	{
				message:{"form_require":'This data is required'},
				hasestatus:{"success":'has-success',"warning":'has-warning',"danger":'has-danger'},
				formcontrol:{"success":'form-control-success',"warning":'form-control-warning',"danger":'form-control-danger'},
				contenedor_message:$('<div class="form-control-feedback"/>')
			},
			publicMethod,
			$forms,
			$submit,
			$ApiRest,
			$modal_base	= 	$('<div class="modal fade " tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h6 class="p-0 m-0 h5">Atención</h6><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"></div><div class="modal-footer"></div></div></div></div>');
			publicMethod 	=	$[pluginName]	=	function (options) {
		var $obj 	= 	this;
		if($.isFunction($obj)){
			$forms	=	publicMethod.formsAjax();
			$ApiRest=	publicMethod.formsApiRest();
			publicMethod.formsFilters();
		}
  }

	publicMethod.formsFilters 	= 	function () {
		$forms	=	$("form");
		if($forms.length>0){
			make_modal_alert();
			$submit		=	false;
		}
		$forms.each(function(index,v){
			if($(v).attr("ajax")){
				return false;
			}else{

			}
			$(v).submit(function( event ) {
				if(inpustRequires($(v))){
					return true;
				}
				return false;
			})
		});
	}

	publicMethod.formsAjax 		= 	function () {
		$forms=$("form[ajax]");
		if($forms.length>0){
			make_modal_alert();
			$submit		=	false;
		}
		$forms.each(function(index,v){
			require_complement($(this));
			$(v).submit(function( event ) {
				if(inpustRequires($(v))){
					post($(this));
				}
				return false;
			})
		});
	}

	publicMethod.formsApiRest	= 	function () {
		$forms=$("form[ApiRest]");
		if($forms.length>0){
			make_modal_alert();
			$submit		=	false;
		}
		$forms.each(function(index,v){
			require_complement($(this));
			$(v).submit(function( event ) {
				if(inpustRequires($(v))){
					postApiRest($(this));
				}
				return false;
			})
		});
	}

	function require_complement(form){
		var require		=	form.find('[require]');
		require.each(function(index,v){
			$(v).popover('hide');
			var obj					=		$(v);
			var require_selector	=		$(require[index-1]);
			obj.keyup(function(){
				var require_selector	=		$(require[index+1]);
				if($(this).val()!=''){
					require_selector.removeAttr("readonly");
				}
			});
		});
	}

	function inpustRequires(form) {
		var return_value	=	false;
		form.find('[require]').each(function(index,v){
			if($(v).val()==''){
				inpustRequireEmpty($(v));
				return_value	=	false;
				callback_modal("Por favor complete la información");
				return false;
			}else{
				return_value	=	true;
			}
		});
		return return_value;
	}

	function inpustRequireEmpty(elem) {
		var feedback				=	{
											"clone":defaults.contenedor_message.clone(),
											"grand_contenedor":elem.parent("div").parent("div.form-group"),
											"form_control_feedback":"",
											"elem":elem
										}
		;

		if(feedback.grand_contenedor.find(".form-control-feedback").length==0){
			feedback.clone.html(defaults.message.form_require);
			feedback.grand_contenedor.addClass(defaults.hasestatus.danger).find("div").append(feedback.clone);
			efx(feedback.clone);
			feedback.elem.focus().addClass(defaults.formcontrol.danger);
			feedback.form_control_feedback	=	feedback.grand_contenedor.find(".form-control-feedback");
			keychange(feedback);
		}
	}

	function keychange(obj){
		obj.elem.keyup(function(){
			if($(this).val()!=''){
				clear(obj);
			}
		});
		obj.elem.focusout(function(){
			if($(this).val()!=''){
				clear(obj);
			}
		});
	}

	function clear(obj){
		obj.form_control_feedback.remove();
		obj.elem.removeClass(defaults.formcontrol.success);
		obj.elem.removeClass(defaults.formcontrol.warning);
		obj.elem.removeClass(defaults.formcontrol.danger);
		obj.grand_contenedor.removeClass(defaults.hasestatus.success);
		obj.grand_contenedor.removeClass(defaults.hasestatus.warning);
		obj.grand_contenedor.removeClass(defaults.hasestatus.danger);
	}

	function efx(obj,fx) {
		if(!fx){fx	=	'bounceIn';}
		obj.addClass("animated animated "+fx+" active");
		setTimeout(function(){obj.removeClass("animated animated bounceIn");}, 1000);
	}

	function post(elem) {
		$.post(elem.attr("action"),elem.serialize(),function(data){
		},'json').fail(function() {
			callback_modal("Error consult system administrator");
		}).always(function(data) {
			if(data.message){
				callback_modal(data.message);
			}
			if(data.code==200){
				if(data.redirect){
					document.location.href	=	data.redirect;
				}
				var redirect = elem.find('[name="redirect"]');
				if( redirect.length >0){
					document.location.href	=	redirect.val();
				}
			}
			if(data.callback){
				eval(data.callback);
			}
		});
	}

	function postApiRest(elem) {
		$.ajax({
			url: elem.attr("action"),
			type: 'put',
			data: elem.serialize(),
			headers: {
				'x-auth-token': localStorage.accessToken,
				"Content-Type": "application/json"
			},
			dataType: 'json'
		}).fail(function() {
			callback_modal("Error consult system administrator");
		}).always(function(data) {
			if(data.message){
				callback_modal(data.message);
			}
			if(data.code==200){
				var redirect = elem.find('[name="redirect"]');
				if( redirect.length >0){
					document.location.href	=	redirect.val();
				}
			}
			if(data.redirect){
				document.location.href	=	data.redirect;
			}
			if(data.callback){
				eval(data.callback);
			}
		});
	}

	function make_modal_confirm(){
		$modal			=	$modal_base.clone();
		var contenido  	=	$modal.find(".modal-dialog").find(".modal-content");
		$modal.addClass("pgrw_modal_confirm_"+pluginName).attr("aria-labelledby","modalLabel_confirm_"+pluginName).find(".modal-dialog").addClass("modal-md");
		contenido.find(".modal-header h6").html('Atención');
		contenido.find(".modal-footer").html('<!--button type="button" class="btn btn-primary aceptar" data-dismiss="modal">To accept</button><button type="button" class="btn btn-danger cancelar" data-dismiss="modal">Cancelar</button-->');
		$("body").append($modal);
	}

	function make_modal_alert(){
		$modal			=	$modal_base.clone();
		var contenido  	=	$modal.find(".modal-dialog").find(".modal-content");
		$modal.addClass("pgrw_modal_alert_"+pluginName).attr("aria-labelledby","modalLabel_alert_"+pluginName).find(".modal-dialog").addClass("modal-sm");
		contenido.find(".modal-header h6").html('Atención');
		contenido.find(".modal-footer").html('<!--button type="button" class="btn btn-primary aceptar" data-dismiss="modal">To accept</button-->');
		$("body").append($modal);
	}

	function callback_modal(message){
		$modal.find(".modal-body").html('<div class="text-center">'+message+'</div>');
		$modal.modal({ keyboard: false})
		$modal.find(".modal-header").on("mousedown", function(mousedownEvt) {
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

	function callback_popover(selector,message){
			var _popover = selector.popover({
								title: 'Atención',
								trigger: 'manual',
								placement: 'bottom',
								content: message,
							});
			selector.popover('show');
			selector.focusout(function(){
				$(this).popover('hide');
			});
	}

	$(document).ready(function(){
		$.pgrwForms();
	});

}(jQuery, document, window));
