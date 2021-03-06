var obrigatorio = ["nome", "email", "senha", "confirmacao", "estado", "cidade", "cpf", "cnpj"];

$(function(){
	// Mascara dos campos
	$('#input_cpf').mask('000.000.000-00', {reverse: true});
	$('#input_cnpj').mask('00.000.000/0000-00', {reverse: true});

	// Remove a classe de erro	
	obrigatorio.forEach(function(item, index){
		$("[name='" + item + "']").on("change", function(){ changeClass($(this), "remove"); });
	});
});

/* Altera a imagem para o arquivo que foi selecionado */
$("#file_foto").on("change", function(a){
	$("#perfil_img").hide();
	verificaMostraNovaImagem();
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
        	$("#nova_foto").attr('src', e.target.result)
    	};
    	reader.readAsDataURL(input.files[0]);
    }
    else {
        var img = input.value;
        $(input).next().attr('src',img);
    }
} 

function verificaMostraNovaImagem(){
    $('input[type=file]').each(function(index){
        if ($('input[type=file]').eq(index).val() != ""){
            readURL(this);
        }
    });
}

/* Ajax responsável por buscar as cidades correspondentes com o estado selecionado */
$("#select_estado").on("change", function(a){
	var estado 	= $(this).val();

	changeClass($(this), "remove");

	$("#select_estado").prop('disabled', true);
	$.ajax({
		url: "selecionaCidades/" + estado,
		type: "POST",
		success: function(data){
			if(typeof JSON.parse(data) != "undefined"){
				var json = JSON.parse(data);

				if(typeof json["tipo"] != "undefined" && json["tipo"] == "erro"){
					$("#select_cidade option").remove();
					$('#select_cidade').prop('disabled', true);

					$("#group_mensagem").show();
					mensagem(json["tipo"], json["msg"], "mensagem");
				}else{
					$("#select_cidade option").remove();

					json["cidades"].forEach(function(item, index){
						$("#select_cidade").append('<option value="' + item["id"] + '">' + item["nome"] + '</option>');
					});

					$('#select_cidade').prop('disabled', false);
				}
			}

			$("#select_estado").prop('disabled', false);
		},
		error: function(data){
			$("#select_cidade option").remove();
			$('#select_cidade').prop('disabled', false);
		}
	}).done(function(){
		$('#select_estado').prop('disabled', false);
	});
});

/**
 * Quando é dado submit no formulário, aqui ele é tratado e enviado por ajax
 */
$("#form_perfil").on("submit", function(event){
	event.preventDefault();

	var erro = false;
	$("#btnSend").button('loading');

	nivel = $("#user_nivel").val();

	obrigatorio.forEach(function(item, index){
		var obj = $('[name="' + item + '"]');

		if(($.trim(obj.val()).length === 0)){
			var flag = false;

			if(nivel == "Pessoa" && item == "cnpj") flag = true;
			if(nivel == "Instituição" && item == "cpf") flag = true;

			if(!flag){
				changeClass(obj, "add");
				erro = true;
			}
		}
	});

	// Caso tenha sido selecionada uma imagem para foto
	if(!erro && $("#file_foto").val().split('\\').pop().trim()){
		$('input[type=file]').upload("/Usuario/alterarPerfil",{
			nome: 		 $("[name='nome']").val(),
			website: 	 $("[name='website']").val(),
			resumo: 	 $("[name='resumo']").val(),
			cpf: 		 $("[name='cpf']").val(),
			cnpj: 		 $("[name='cnpj']").val(),
			email: 		 $("[name='email']").val(),
			senha: 		 $("[name='senha']").val(),
			confirmacao: $("[name='confirmacao']").val(),
			endereco: 	 $("[name='endereco']").val(),
			bairro: 	 $("[name='bairro']").val(),
			numero: 	 $("[name='numero']").val(),
			complemento: $("[name='complemento']").val(),
			estado: 	 $("#select_estado option:selected").val(),
			cidade: 	 $("#select_cidade option:selected").val(),
			telefone: 	 $("[name='telefone']").val(),
			old_foto: 	 $("[name='old_foto'").val(),
			old_path: 	 $("[name='old_path'").val(),
			flag_foto: 	 1,
			ajax: 		 1
		}, function(data){
			$("#btnSend").button('reset');
			mensagem(data["tipo"], data["msg"], "mensagem");
			$("#div_mensagem").show();

			if(data["tipo"] == "erro" && data["campo"]){
				changeClass($("[name='" + data["campo"] + "']"), "add");
			}
		});
	// Caso não tenha imagem
	} else if(!erro){
		$.ajax({
			url: 'alterarPerfil',
			type: 'POST',
			data: $("#form_perfil").serialize(),
			dataType: "json",
			success: function(data){
				$("#btnSend").button('reset');
				mensagem(data["tipo"], data["msg"], "mensagem");
				$("#div_mensagem").show();
				if(data["tipo"] == "erro" && data["campo"])
					changeClass($("[name='" + data["campo"] + "']"), "add");
			},
			error: function(data){
				$("#btnSend").button('reset');
				mensagem("erro", "Ocorreu um problema na hora de realizar o cadastro. Por favor, tente mais tarde ou contate o suporte através do e-mail: <b>suporte@takeit.com.br</b>", "mensagem");
			},
			finally: function(data){
				$("#btnSend").button('reset');
			}
		});
	}else{
		$("#btnSend").button('reset');
		mensagem("erro", "Campos obrigatórios não estão preenchidos.", "mensagem");
		$("#div_mensagem").show();
	}

	event.preventDefault();
});

/**
 * Modifica as classes dos objetos que são colocados como erros e para remover esses erros
 * @param  {html-object} obj Objeto a ser modificado
 * @param  {string} way Tipo de modificação, podendo ser remove ou add
 * @return {void}
 */
function changeClass(obj, way){
	var mainDiv = function(ini){
		var safetyCount = 0;
		while(true){
			if(safetyCount == 20) break;

			ini = ini.parent();
			if(ini.hasClass("form-group")) return ini;

			safetyCount += 1;
		}
	}

	if(way == "add") mainDiv(obj).addClass("has-error");
	else mainDiv(obj).removeClass("has-error");
}