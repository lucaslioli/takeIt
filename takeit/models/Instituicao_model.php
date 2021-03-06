<?php defined('BASEPATH') OR exit('OPSSS... Não é permitido direto acesso ao script!!');

class Instituicao_model extends Usuario_model{

    public function __construct(){
        parent::__construct();

        $this->load->database();
    }

	/**
	 * Insere uma instituição no banco de dados
	 * @param  array $dados  Array com todos os dados da instituição vindo do formulário de cadastro
	 * @return array         Array com a resposta caso tenha dado erro ou não
	 */
	public function insereInstituicao($dados){
		$this->load->helper("validacao");

		if(!isset($dados['cnpj']))
			return ["tipo" => "erro", "msg" => "Campos obrigatórios ainda não foram preenchidos.", "campo" => "cnpj"];
		else if(!validaCNPJ($dados['cnpj']))
			return ["tipo" => "erro", "msg" => "CNPJ informado não é válido.", "campo" => "cnpj"];
		else if($this->buscaInstituicao($dados['cnpj']))
			return ["tipo" => "erro", "msg" => "CNPJ já registrado no sistema.", "campo" => "cnpj"];

		$resposta = $this->insereUsuario($dados);

		if($resposta["tipo"] == "sucesso"){
			try{
				$sql = "INSERT INTO instituicao (instituicao_id, instituicao_cnpj, instituicao_site) 
				values (".$this->db->escape($this->getId()).", ".$this->db->escape($dados['cnpj']).", ".$this->db->escape($dados['website']).")";

				$this->db->trans_begin();
				if(!$query = $this->db->query($sql)){
					if($this->usuario->excluiUsuario($this->getId()) !== true)
						return ["tipo" => "erro", "msg" => "Ocorreu um problema na hora de cadastrar a Instituição. Por favor, mude os dados inseridos ou tente mais tarde."];

					if($this->db->error()){
						$this->db->trans_rollback();
						return ["tipo" => "erro", "msg" => "Ocorreu um problema na hora de cadastrar a Instituição. Por favor, mude os dados inseridos ou tente mais tarde. Código: ".$error["code"]];
					}
				}else{
        			$this->db->trans_commit();
					return ["tipo" => "sucesso", "msg" => "Cadastro efetuado com sucesso."];
				}
				
			}catch(PDOException $PDOE){
				$this->db->trans_rollback();
				return ["tipo" => "erro", "msg" => "Problema ao processar os dados no sistema. - Código: " . $PDOE->getCode()];
			}catch(Exception $E){
				$this->db->trans_rollback();
				return ["tipo" => "erro", "msg" => "Problema interno do sistema. Por favor, tente mais tarde!"];
			}
		}
	}

	/**
	 * Altera o cnpj e o site de uma instituição no Banco de Dados
	 * @param 	$idInst		ID da instituição a ser alterada
	 * @param  	$dados 	Array com todos os dados da conta preenchidos no formulário
	 * @return 				Boolean indicando o sucesso da alteração ou array com mensagem de erro
	 *	Array format:
	 *		array(
	 *			"Error" => ""
	 *		)
	 */
	public function alteraInstituicao($idInst, $dados){
		$this->load->helper("validacao");
		$usuario = $this->selecionaUsuario($idInst, TRUE);

		if(!isset($idInst) || !isset($dados))
			return ["tipo" => "erro", "msg" => "Parâmetros insuficientes para atualizar a Instituição."];
		else if(!isset($dados['cnpj']))
			return ["tipo" => "erro", "msg" => "Campos obrigatórios ainda não foram preenchidos.", "campo" => "cnpj"];
		else if(!validaCNPJ($dados['cnpj']))
			return ["tipo" => "erro", "msg" => "CNPJ informado não é válido.", "campo" => "cnpj"];
		else if($this->buscaInstituicao($dados['cnpj']) && $usuario['instituicao_cnpj']!=$dados['cnpj'])
			return ["tipo" => "erro", "msg" => "CNPJ já registrado no sistema.", "campo" => "cnpj"];

		try{
			$this->db->trans_begin();
			$resposta = $this->alteraUsuario($idInst, $dados);
			
			if($resposta["tipo"] == "sucesso"){
				$sql = "UPDATE instituicao SET instituicao_cnpj = ".$this->db->escape($dados['cnpj']).", instituicao_site = ".$this->db->escape($dados['website'])." WHERE instituicao_id = ".$idInst;

				if(!$query = $this->db->query($sql)){
					if($this->db->error()){
						$this->db->trans_rollback();
						return ["tipo" => "erro", "msg" => "Ocorreu um problema na hora de atualizar a Instituição. Por favor, mude os dados inseridos ou tente mais tarde. Código: ".$error["code"]];
					}
				} else {
	    			$this->db->trans_commit();
					return ["tipo" => "sucesso", "msg" => "Atualização efetuada com sucesso."];
				}
			}else{
				$this->db->trans_rollback();
				return $resposta;
			}			
		} catch(Exception $E) {
			return ["tipo" => "erro", "msg" => "Problema interno do sistema. Por favor, tente mais tarde!"];
		}
	}

	/**
	 * Busca no banco a Instituição a partir de seu ID
	 * @param  int          $idInst  	  ID da instituição a ser buscada
	 * @param  bool|boolean $return_array Se deseja que os dados sejam retornados em forma de array
	 * @return bool|array                 Se $returns for true, retorna um array com todos os dados, se não retorna true se a instituição foi selecionada	
	 */
	public function selecionaInstituicao(int $idInst, bool $return_array = false){
		if(!isset($idInst) or empty(trim($idInst)))
			return ["tipo" => "erro", "msg" => "ID não informado para a busca."];

		$resposta = $this->selecionaUsuario($idInst, $return_array);

		if($return_array && isset($resposta["tipo"]) && $resposta["tipo"] == "erro")
			return ["tipo" => "erro", "msg" => "Não foi possivel selecionar a instituição. Por favor, tente mais tarde!"];
		else if(!$return_array && $resposta !== true)
			return ["tipo" => "erro", "msg" => "Não foi possivel selecionar a instituição. Por favor, tente mais tarde!"];

		try{
			$sql = "SELECT instituicao_cnpj, instituicao_site FROM instituicao
			WHERE instituicao_id = ".$this->db->escape($idInst);

			if(!$query = $this->db->query($sql)){
				if($this->db->error())
					return ["tipo" => "erro", "msg" => "Não foi possivel selecionar a instituição. Por favor, tente mais tarde!"];
			}else{
				if(!count($query->result()))
					return false;

				if($return_array === true){
					foreach($query->result()[0] as $campo => $valor)
						$dados[$campo] = $this->$campo = $valor;

					$dados = array_merge($dados, $resposta);

					return $dados;
				}else{
					foreach($query->result()[0] as $campo => $valor)
						$this->$campo = $valor;

					return true;
				}
			}			
		}catch(PDOException $PDOE){
			return ["tipo" => "erro", "msg" => "Problema ao processar os dados no sistema. - Código: " . $PDOE->getCode()];
		}catch(Exception $NE){
			return ["tipo" => "erro", "msg" => "Problema ao executar a tarefa no sistema. - Código: " . $NE->getCode()];
		}

		return ["tipo" => "erro", "msg" => "Problema inesperado no sistema. Tente novamente mais tarde!"];
	}

	/**
	 * Seleciona todas as instituições cadastradas no banco
	 * @return array Array com todas as instituições no formato:
	 *     [0][
	 *     		"usuario_id" => "Valor",
	 *       	"usuario_nome" => "Valor",
	 *        	"cidade_nome" => "Valor",
	 *         	"estado_uf" => "Valor"
	 *     ]
	 */
	public function todasInstituicoes(){
		try{
			$sql = "SELECT DISTINCT usuario_id, usuario_nome, cidade_nome, estado_uf
 				FROM estado NATURAL JOIN cidade NATURAL JOIN usuario WHERE usuario_nivel='Instituição'";

 			if(!$query = $this->db->query($sql)){
				if($this->db->error()){
					return array("Error" => "$error[message]");
				}
			}else if(!count($query->result()))
				return array("tipo" => "erro", "msg" => "Nenhuma instituição cadastrada no momento.");
			else{
				$count = 0;
				foreach($query->result() as $row){
					foreach ($row as $campo => $valor) {
						$result[$count][$campo] = $valor;
					}
					$count++;
				}

 				return $result;
 			}
		}catch(PDOException $PDOE){
			return ["tipo" => "erro", "msg" => "Problema ao processar os dados no sistema. Por favor, tente mais tarde! - Código: " . $PDOE->getCode()];
		}catch(Exception $E){
			return ["tipo" => "erro", "msg" => "Problema interno do sistema. Por favor, tente mais tarde! - Código: " . $E->getCode()];
  		}
	}
	
	/**
	 * Busca uma instituição pelo seu cnpj (Mais usado na hora de cadastrar, para ver se o cpf já existe no sistema)
	 * @param  string $cnpj CNPJ a ser buscado
	 * @return boolean       TRUE se já existe uma instituição cadastrada no sistema com esse CNPJ, FALSE se não.
	 */
    public function buscaInstituicao($cnpj){
		if(!isset($cnpj))
			return ["tipo" => "erro", "msg" => "Problema interno do sistema. Por favor, tente mais tarde!"];

		try{
			$sql = "SELECT instituicao_cnpj FROM instituicao
			WHERE instituicao_cnpj = ".$this->db->escape($cnpj);

			if(!$query = $this->db->query($sql)){
				if($error = $this->db->error()){
					return ["tipo" => "erro", "msg" => "Ocorreu um problema ao tentar buscar a pessoa. Por favor, tente mais tarde! Código: ".$error["code"]];
				}
			}else{
				if(!count($query->result()))
					return false;

				return true;
			}
			
		}catch(Exception $E){
			return ["tipo" => "erro", "msg" => "Problema interno do sistema. Por favor, tente mais tarde!"];
		}
    }
	
	/**
	 * Associa categorias a uma instituição
	 * @param  array  $idsCat Array com IDs das categorias a serem inseridas
	 * @return bool|array     Array informando sucesso ou erro
	 */
    public function associarInstituicaoCategorias(array $idsCat){
    	if(!isset($idsCat) || !is_array($idsCat))
			return ["tipo" => "erro", "msg" => "Nenhuma categoria informada"];

		$id_instituicao = $this->session->userdata('user_id');
		try{
			$sql_cleaner = "DELETE FROM instituicao_categoria WHERE instituicao_id = ".$this->db->escape($id_instituicao);
			
			if($query = $this->db->query($sql_cleaner)){
				if($idsCat[0]!=NULL){
					$sql = "INSERT INTO instituicao_categoria (instituicao_id, categoria_id) VALUES ";

					foreach($idsCat as $i => $idCat)
						$sql .= "(".$this->db->escape($id_instituicao).", ".$this->db->escape($idCat)."), ";

					$sql = substr($sql, 0, -2)."";
					if(!$query = $this->db->query($sql)){
						if($this->db->error())
							return ["tipo" => "erro", "msg" => "Ocorreu um problema na hora de cadastrar as categorias para a Instituição. Por favor, mude os dados inseridos ou tente mais tarde."];
					}else
						return ["tipo" => "sucesso", "msg" => "Preferências salvas com sucesso!"];
				}else
					return ["tipo" => "sucesso", "msg" => "Preferências salvas com sucesso!"];
			}else
				return ["tipo" => "erro", "msg" => "Ocorreu um problema na hora de cadastrar as categorias para a Instituição. Por favor, tente mais tarde ou entre em contato com nosso suporte."];

		}catch(PDOException $PDOE){
			return ["tipo" => "erro", "msg" => "Problema ao processar os dados no sistema. - Código: " . $PDOE->getCode()];
		}catch(Exception $NE){
			return ["tipo" => "erro", "msg" => "Problema ao executar a tarefa no sistema. - Código: " . $NE->getCode()];
		}

		return ["tipo" => "erro", "msg" => "Problema inesperado no sistema. Tente novamente mais tarde!"];
    }

    /**
     * Descrição: Função que retorna as instituições interessadas em uma categoria específica passada por parâmetro
     * @param [int] id da instuição
     * @return [array] array os dados da instituição
     */
    
    public function instituicoesInteressadas($idCategoria){
    	if (!isset($idCategoria))
    		return ["tipo" => "erro", "msg" => "Nenhuma categoria informada!"];

    	$result = array();
    	try{
			$sql = "SELECT instituicao_id FROM instituicao_categoria WHERE categoria_id =".$this->db->escape($idCategoria);

			if(!$query = $this->db->query($sql)){
				if($this->db->error())
					return ["tipo" => "erro", "msg" => "Ocorreu um problema ao buscar as categorias. Por favor, mude os dados inseridos ou tente mais tarde."];
			}else{
				$count = 0;
				foreach($query->result() as $row){
					foreach ($row as $campo => $valor) {
						$result[$count][$campo] = $valor;
					}
					$count++;
				}
				return $result;
			} 
				
		}catch(PDOException $PDOE){
			return ["tipo" => "erro", "msg" => "Problema ao processar os dados no sistema. - Código: " . $PDOE->getCode()];
		}catch(Exception $NE){
			return ["tipo" => "erro", "msg" => "Problema ao executar a tarefa no sistema. - Código: " . $NE->getCode()];
		}

		return ["tipo" => "erro", "msg" => "Problema inesperado no sistema. Tente novamente mais tarde!"];
    }

    /**
     * Retorna os IDs de todas as categorias de interesse da instituição
     * @param  int  $id_intituicao 	ID da instituição
     * @return array 				Array com os IDs de todas as categorias
     */
    public function buscaCategoriasInteresse($id_intituicao){
    	if (!isset($id_intituicao))
    		return ["tipo" => "erro", "msg" => "Nenhuma instituição informada!"];

    	$result = array();
    	try{
    		$sql = "SELECT categoria_id FROM instituicao_categoria WHERE instituicao_id =".$this->db->escape($id_intituicao);

    		if(!$query = $this->db->query($sql)){
				if($this->db->error())
					return ["tipo" => "erro", "msg" => "Ocorreu um problema ao buscar as categorias. Por favor, contate nosso suporte."];
			}else{
				$count = 0;
				foreach($query->result() as $row){
					foreach ($row as $campo => $valor) {
						$result[$count] = $valor;
					}
					$count++;
				}
				return $result;
			} 

    	}catch(PDOException $PDOE){
			return ["tipo" => "erro", "msg" => "Problema ao processar os dados no sistema. - Código: " . $PDOE->getCode()];
		}catch(Exception $NE){
			return ["tipo" => "erro", "msg" => "Problema ao executar a tarefa no sistema. - Código: " . $NE->getCode()];
		}
    }

}