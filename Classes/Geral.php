<?php
	require_once("conexao.php");
	class Geral {
		function formata_cpf_cnpj($cpf_cnpj){
		    /*
		        Pega qualquer CPF e CNPJ e formata
		
		        CPF: 000.000.000-00
		        CNPJ: 00.000.000/0000-00
		    */
		
		    ## Retirando tudo que não for número.
		    $cpf_cnpj = preg_replace("/[^0-9]/", "", $cpf_cnpj); 
		    $tipo_dado = NULL;
		    if(strlen($cpf_cnpj)==11){ 
		        $tipo_dado = "cpf";
		    }
		    if(strlen($cpf_cnpj)==14){
		        $tipo_dado = "cnpj";
		    }
		    switch($tipo_dado){
		        default:
		            $cpf_cnpj_formatado = "Não foi possível definir tipo de dado";
		        break;
		
		        case "cpf":
		            $bloco_1 = substr($cpf_cnpj,0,3);
		            $bloco_2 = substr($cpf_cnpj,3,3);
		            $bloco_3 = substr($cpf_cnpj,6,3);
		            $dig_verificador = substr($cpf_cnpj,-2);
		            $cpf_cnpj_formatado = $bloco_1.".".$bloco_2.".".$bloco_3."-".$dig_verificador;
		        break;
		
		        case "cnpj":
		            $bloco_1 = substr($cpf_cnpj,0,2);
		            $bloco_2 = substr($cpf_cnpj,2,3);
		            $bloco_3 = substr($cpf_cnpj,5,3);
		            $bloco_4 = substr($cpf_cnpj,8,4);
		            $digito_verificador = substr($cpf_cnpj,-2);
		            $cpf_cnpj_formatado = $bloco_1.".".$bloco_2.".".$bloco_3."/".$bloco_4."-".$digito_verificador;
		        break;
		    }
		    return $cpf_cnpj_formatado;
		}

		


		
		public function listarOptionsConvenio($id_tipo_convenio = null )
		{
			try{
				$con = Conecta::criarConexao();

				$select = "SELECT id_convenio, ds_convenio
							FROM tb_convenio
							WHERE id_tipo_convenio = :id_tipo_convenio
							ORDER BY ds_convenio";
				
				$stmt = $con->prepare($select);
				$params=array(":id_tipo_convenio" => $id_tipo_convenio);
				$stmt->execute($params);

				$options = "<option value=''>Selecione..</option>";

				while($dados = $stmt->fetch())
				{
					
					$options.= "<option value='".$dados['id_convenio']."'>".$dados['ds_convenio']."</option>";	
					
				}
				return $options;

			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print $e->getMessage();
			}
		}

		public function listarOptionsTipoConvenio($id_tipo_convenio = null)
		{
			try{
				$con = Conecta::criarConexao();

				$select = "SELECT id_tipo_convenio, ds_tipo_convenio
							FROM tb_tipo_convenio";
				
				$stmt = $con->prepare($select);
				$stmt->execute();

				$options = "<option value=''>Selecione..</option>";

				while($dados = $stmt->fetch())
				{
					if ($dados['id_tipo_convenio'] == $id_tipo_convenio)
					{
						$options.= "<option value='".$dados['id_tipo_convenio']."' selected>".$dados['ds_tipo_convenio']."</option>";	
					}
					else
					{
						$options.= "<option value='".$dados['id_tipo_convenio']."'>".$dados['ds_tipo_convenio']."</option>";
					}
				}
				return $options;

			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print $e->getMessage();
			}
		}

		public function listarOptionsConvenioalt($id_convenio)
		{
			try{
				$con = Conecta::criarConexao();

				$select = "SELECT id_convenio, ds_convenio
							FROM tb_convenio";
				
				$stmt = $con->prepare($select);
				$stmt->execute();

				$options = "<option value=''>Selecione..</option>";

				while($dados = $stmt->fetch())
				{
					
					
					if ($dados['id_convenio'] == $id_convenio)
					{
						$options.= "<option value='".$dados['id_convenio']."' selected>".$dados['ds_convenio']."</option>";	
					}
					else
					{
						$options.= "<option value='".$dados['id_convenio']."'>".$dados['ds_convenio']."</option>";
					}
				}
				return $options;

			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print $e->getMessage();
			}
		}
	
		public function mandaEmail($email,$mensagem)
 		{
 			$quebra_linha = "\n";
 
			$emailsender='suporte@ahoraeagora.org';

			$headers = "MIME-Version: 1.1".$quebra_linha;
			$headers .= "Content-type: text/html; charset=utf-8".$quebra_linha;
			// Perceba que a linha acima contém "text/html", sem essa linha, a mensagem não chegará formatada.
			$headers .= "From: ".$emailsender.$quebra_linha;
			$headers .= "Return-Path: " . $emailsender . $quebra_linha;

			$headers .= "Reply-To: ".$emailsender.$quebra_linha;
			// Note que o e-mail do remetente será usado no campo Reply-To (Responder Para)
			 
			/* Enviando a mensagem */
			$emaildestinatario = $email;
			$assunto = "Cadastro Ensaio Clínico";
			$mensagemHTML = $mensagem;
			mail($emaildestinatario, $assunto, $mensagemHTML, $headers, "-r". $emailsender);
 		}
		
		public function formataData($data)
		{
			return date('d/m/Y', strtotime($data));	
		}

		public function formataDataHora($data)
		{
			return date('d/m/Y H:i', strtotime($data));	
		}

		public function formataHora($data)
		{
			return date('H:i', strtotime($data));	
		}
		
		public function formataDataSQL($data)
		{
			return date('Y-m-d', strtotime(str_replace('/','-',$data)));	
		}

		

		public function listarCheckProblema($co_seq, $desc)
		{
			try{
				$con = Conecta::criarConexao();
				$select = "SELECT 
								tp.co_tipo_problema, 
								tp.ds_tipo_problema,
							    case when rr.co_seq_resposta_questionario_recrutado is not null then 1 else 0 end as resposta
							FROM
								tb_tipo_problema tp
							LEFT JOIN rl_resposta_recrutado_problema rr ON tp.co_tipo_problema=rr.co_tipo_problema
							AND rr.co_seq_resposta_questionario_recrutado = :co_seq
							ORDER BY co_tipo_problema";

				$stmt = $con->prepare($select);
				$params = array(':co_seq' => $co_seq);
				$stmt->execute($params);

				$check = "";

				while($dados = $stmt->fetch())
				{
					$ds_tipo_problema = str_replace("[Testes/Vouchers]", $desc, $dados['ds_tipo_problema']);

					if ($dados['resposta'] == 1)
					{
						$check.= "<label class='m-checkbox'>
										<input type='checkbox' name='problema[]' id='problema_".$dados['co_tipo_problema']."' value='".$dados['co_tipo_problema']."' checked> ".$ds_tipo_problema.".
										<span></span>
									</label>";	
					}
					else
					{
						$check.= "<label class='m-checkbox'>
										<input type='checkbox' name='problema[]' id='problema_".$dados['co_tipo_problema']."' value='".$dados['co_tipo_problema']."'> ".$ds_tipo_problema.".
										<span></span>
									</label>";	
					}
					
				}
				return $check;

			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print $e->getMessage();
			}
		}

		
		
	

		
		public function listarOptionsFono($id_usuario = null)
		{
			try{
				$con = Conecta::criarConexao();

				$select = "SELECT id_usuario, ds_nome
							FROM tb_usuario
							WHERE st_ativo = 'A'";
				
				$stmt = $con->prepare($select);
				$stmt->execute();

				$options = "<option value=''>Selecione..</option>";

				while($dados = $stmt->fetch())
				{
					if ($dados['id_usuario'] == $id_usuario)
					{
						$options.= "<option value='".$dados['id_usuario']."' selected>".$dados['ds_nome']."</option>";	
					}
					else
					{
						$options.= "<option value='".$dados['id_usuario']."'>".$dados['ds_nome']."</option>";
					}
				}
				return $options;

			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print $e->getMessage();
			}
		}
		
		
		public function listarOptionsUnidade($id_unidade = null)
		{
			try{
				$con = Conecta::criarConexao();

				$select = "SELECT id_unidade, ds_unidade 
							FROM tb_unidade
							WHERE st_unidade = 'A'";
				
				$stmt = $con->prepare($select);
				$stmt->execute();

				$options = "<option value=''>Selecione..</option>";

				while($dados = $stmt->fetch())
				{
					if ($dados['id_unidade'] == $id_unidade)
					{
						$options.= "<option value='".$dados['id_unidade']."' selected>".$dados['ds_unidade']."</option>";	
					}
					else
					{
						$options.= "<option value='".$dados['id_unidade']."'>".$dados['ds_unidade']."</option>";
					}
				}
				return $options;

			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print $e->getMessage();
			}
		}

		public function listarOptionsDieta($id_grupo_dieta = null, $id_dieta = null)
		{
			try{
				$con = Conecta::criarConexao();

				$select = "SELECT id_dieta, ds_dieta 
							FROM tb_dieta 
							WHERE id_grupo_dieta = :id_grupo_dieta";
				
				$stmt = $con->prepare($select);
				$params = array(':id_grupo_dieta' => $id_grupo_dieta);
				$stmt->execute($params);

				$options = "<option value=''>Selecione..</option>";

				while($dados = $stmt->fetch())
				{
					if ($dados['id_dieta'] == $id_dieta)
					{
						$options.= "<option value='".$dados['id_dieta']."' selected>".$dados['ds_dieta']."</option>";	
					}
					else
					{
						$options.= "<option value='".$dados['id_dieta']."'>".$dados['ds_dieta']."</option>";
					}
				}
				return $options;

			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print $e->getMessage();
			}
		}
		public function listarOptionsTipo($id_tipo = null)
		{
			try{
				$con = Conecta::criarConexao();

				$select = "SELECT id_tipo, ds_tipo 
							FROM tb_tipo";
				
				$stmt = $con->prepare($select);
				$stmt->execute();

				$options = "";

				while($dados = $stmt->fetch())
				{
					if ($dados['id_tipo'] == $id_tipo)
					{
						$options.= "<option value='".$dados['id_tipo']."' selected>".$dados['ds_tipo']."</option>";	
					}
					else
					{
						$options.= "<option value='".$dados['id_tipo']."'>".$dados['ds_tipo']."</option>";
					}
				}
				return $options;

			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print $e->getMessage();
			}
		}

		public function listarOptionsModoventilatorio($id_modo_ventilatorio = null)
		{
			try{
				$con = Conecta::criarConexao();

				$select = "SELECT id_modo_ventilatorio, ds_modo_ventilatorio 
							FROM tb_modo_ventilatorio";
				
				$stmt = $con->prepare($select);
				$stmt->execute();

				$options = "<option value=''>Selecione..</option>";

				while($dados = $stmt->fetch())
				{
					if ($dados['id_modo_ventilatorio'] == $id_modo_ventilatorio)
					{
						$options.= "<option value='".$dados['id_modo_ventilatorio']."' selected>".$dados['ds_modo_ventilatorio']."</option>";	
					}
					else
					{
						$options.= "<option value='".$dados['id_modo_ventilatorio']."'>".$dados['ds_modo_ventilatorio']."</option>";
					}
				}
				return $options;

			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print $e->getMessage();
			}
		}


		

		public function buscarDadosUnidade($id_unidade)
		{
			try{
				$con = Conecta::criarConexao();
				
				$select = "SELECT id_unidade, ds_unidade
							FROM tb_unidade
							WHERE id_unidade = :id_unidade";
				
				$stmt = $con->prepare($select);
				$params = array(':id_unidade' => $id_unidade);
				
				$stmt->execute($params);
				$dados = $stmt->fetch();
				return $dados['ds_unidade'];
				
					
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}

		
}
?>