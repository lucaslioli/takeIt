<?php defined('BASEPATH') OR exit('OPSSS... Não é permitido direto acesso ao script!!');

class Cadastro extends CI_Controller{

	function __construct(){
		parent::__construct();
		$this->load->helper('url');
	}

	public function index(){
		$dados["titulo"] = "Cadastre-se";
		$dados["slogan"] = "TakeIt - Ajude quem precisa, doando o que você não precisa.";
		$dados["css"]    = "welcome.css";
		$dados["css2"]   = "cadastro.css";	
		$dados["js"]	 = "jquery.mask.js";
		$dados["js2"] 	 = "cadastro.js";

		$this->load->model("CidadeEstado_model", "CEM");
		$dados["estados"] = $this->CEM->todosEstados();

		$this->load->view('templates/head', $dados);
		$this->load->view('templates/menuWelcome');
		$this->load->view('cadastro', $dados);
		$this->load->view('templates/footer');
	}

	/**
	 * Salva o usuário preenchido no formulário
	 * @return void Da echo em um json de resultado
	 */
	public function salvarUsuario(){
		foreach($this->input->post() as $k => $v)
			$$k = $v;

		$this->load->model("Usuario_model", "usuario");

		if($tipo_usuario == "Pessoa"){
			$this->load->model("Pessoa_model", "pessoa");
			$resposta = $this->pessoa->inserePessoa($this->input->post());

			echo json_encode($resposta); 
			return;
		}else if($tipo_usuario == "Instituição"){
			$this->load->model("Instituicao_model", "instituicao");
			$resposta = $this->instituicao->insereInstituicao($this->input->post());
			
			echo json_encode($resposta);
			return;
		}else echo json_encode(["tipo" => "erro", "msg" => "Problema inesperado no sistema. Tente novamente mais tarde!"]);
	}

	/**
	 * Será chamada por ajax para buscar as cidades
	 * @return json Da um echo em um JSON
	 */
	public function selecionaCidades(){
		$this->load->model("CidadeEstado_model", "CEM");

		$id_estado = $this->uri->segment(3, null);

		if(isset($id_estado) && !is_null($id_estado) && !empty($id_estado)){
			$cidades = $this->CEM->selecionaCidades((int) $id_estado);	

			if($cidades !== FALSE && !isset($cidades["tipo"])){
				echo json_encode(["cidades" => $cidades]);

				return;
			}
		}

		echo json_encode(["tipo" => "erro", "msg" => "Ocorreu um erro ao sistema, tente novamente mais tarde."]);
	}

	public function licencaUso(){
		$dados["titulo"] = "Licença de Uso do sistema takeIt";
		$dados["slogan"] = "TakeIt - Ajude quem precisa, doando o que você não precisa.";
		$dados["css"]    = "welcome.css";
		
		$this->load->view('templates/head', $dados);
		$this->load->view('licenca_uso', $dados);
		$this->load->view('templates/footer');
	}

	public function teste(){
		$dados["titulo"] = "Cadastre-se";
		$dados["slogan"] = "TakeIt - Ajude quem precisa, doando o que você não precisa.";
		$dados["css"]    = "welcome.css";
		$dados["css2"]   = "cadastro.css";	
		$dados["js"]	 = "jquery.mask.js";
		$dados["js2"] 	 = "cadastro.js";

		$this->load->model("Usuario_model", "usuario");
		$this->load->model("Instituicao_model", "instituicao");

		$this->load->view('templates/head', $dados);
		$this->load->view('templates/menuWelcome'); ?>
		
		<main id="mainCadastro" class="footer-align">
			<div class="container">
				<? $this->instituicao->selecionaInstituicao(7);
				var_dump($this->instituicao->associarInstituicaoCategorias([5,3,6,7,8])); ?>
			</div>
		</main>

		<? $this->load->view('templates/footer');
	}
}
