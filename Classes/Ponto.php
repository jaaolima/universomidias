<?php
    require_once("conexao.php");
	require_once("Geral.php");
	
    class Ponto{
		public function reArrayFiles($file_post) {

			$file_ary = array();
			$file_count = count($file_post['name']);
			$file_keys = array_keys($file_post);
		
			for ($i=0; $i<$file_count; $i++) {
				foreach ($file_keys as $key) {
					$file_ary[$i][$key] = $file_post[$key][$i];
				} 
			}
		
			return $file_ary;
		}
		public function listarOptionsBairro($ds_bairro)
		{
			try{
				$con = Conecta::criarConexao();
				$select = "SELECT id_bairro, ds_bairro
							FROM tb_bairro 
							order by ds_bairro";
				$stmt = $con->prepare($select);
				$stmt->execute();

				$options = "";

				while($dados = $stmt->fetch())
				{
					if($dados["ds_bairro"] == $ds_bairro){
                        $options.= "<option value='".$dados['ds_bairro']."' selected>".$dados['ds_bairro']."</option>";
                    }else{
                        $options.= "<option value='".$dados['ds_bairro']."'>".$dados['ds_bairro']."</option>";
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
		public function gravarPonto()
		{
			$id_usuario	    	= $_POST['id_usuario'];
			$id_empresa	    	= $_POST['id_empresa'];
			$ds_localidade	    = $_POST['ds_localidade'];
			$ds_descricao	    = $_POST['ds_descricao'];
			$nu_localidade	    = $_POST['ds_latitude'] . "/" . $_POST['ds_longitude'];
			$id_tipo			= $_POST['id_tipo'];
			$ds_sentido			= $_POST['ds_sentido'];
			$ds_tamanho			= $_POST['ds_tamanho'];
			$id_tipo_cobranca	= $_POST['id_tipo_cobranca'];
			$nu_valor_ponto		= $_POST['nu_valor_ponto'];
			$ds_foto            = $_FILES['fotos'];

			try{
				$con = Conecta::criarConexao();
				$insert = "INSERT into tb_ponto (id_usuario, id_empresa, ds_localidade, ds_descricao, nu_localidade, id_tipo, ds_sentido, ds_tamanho, id_tipo_cobranca, nu_valor_ponto, dt_cadastro)
							VALUES (:id_usuario, :id_empresa, :ds_localidade, :ds_descricao, :nu_localidade, :id_tipo, :ds_sentido, :ds_tamanho, :id_tipo_cobranca, :nu_valor_ponto, curdate())";
				
				$stmt = $con->prepare($insert);
				
				$params = array(':id_usuario' => $id_usuario,
								':id_empresa' => $id_empresa,
								':ds_localidade' => $ds_localidade,
								':ds_descricao' => $ds_descricao,
								':nu_localidade' => $nu_localidade,
								':id_tipo' => $id_tipo,
								':ds_sentido' => $ds_sentido,
								':ds_tamanho' => $ds_tamanho,
								':id_tipo_cobranca' => $id_tipo_cobranca,
								':nu_valor_ponto' => $nu_valor_ponto
							);
								
				$stmt->execute($params);

				$id_ponto = $con->lastInsertId();

				$nome = $ds_foto['name'][0];
				$tipo = $ds_foto['type'][0];
				$tmp = $ds_foto['tmp_name'][0];
				$tamanho = $ds_foto['size'][0];

				//gravar foto
				$tamanho = 20000000;

				$error = array();
				$tamanho_mb = $tamanho/1024/1024;

				// Pega extensão da imagem
				preg_match("/\.(gif|bmp|png|jpg|jpeg|doc|docx|pdf){1}$/i", $nome, $ext);
				// Gera um nome único para o arquivo
				$nome_arquivo = md5(uniqid(time())) . "arquivo.". $ext[1];
				// Caminho de onde ficará o arquivo
				$caminho_arquivo = "/var/www/painelpro/docs_pontos/" . $nome_arquivo;

				$gravar_caminho_arquivo = "docs_pontos/" . $nome_arquivo;
				

				// Faz o upload da imagem para seu respectivo caminho
				$moved = move_uploaded_file($tmp,  $caminho_arquivo);

				$insert_foto = "insert into rl_ponto_foto(id_ponto, ds_foto) values (:id_ponto, :ds_foto)";

				$stmt_foto = $con->prepare($insert_foto);
		
				$params_foto = array(':id_ponto' => $id_ponto,
								':ds_foto' => $gravar_caminho_arquivo
								);
								
				$stmt_foto->execute($params_foto); 


				
				echo "Dados gravados com sucesso!"; 
				
			}
			catch(exception $e) 
			{
				header('HTTP/1.1 500 Internal Server Error');
				print "ERRO:".$e->getMessage();		
			} 	
        }
		public function listarOutdoor($id_usuario) 
		{
			try{
				$con = Conecta::criarConexao();
				
				$select = "SELECT p.id_ponto, ds_localidade, ds_descricao, nu_localidade, id_tipo,if(min(a.dt_final) >= CURDATE(), min(a.dt_inicial), (select min(dt_inicial) from tb_alugado al where dt_final >= CURDATE() and al.id_ponto=p.id_ponto )) as dt_inicial, if(min(a.dt_final) >= CURDATE(), min(a.dt_final), (select min(dt_final) from tb_alugado al where dt_final >= CURDATE() and al.id_ponto=p.id_ponto )) as dt_final, ds_sentido, nu_valor_ponto, pf.ds_foto
							FROM tb_ponto p
							left join tb_alugado a on p.id_ponto=a.id_ponto
							left join rl_ponto_foto pf on p.id_ponto=pf.id_ponto
							where id_tipo = 1 and id_usuario = :id_usuario
							group by p.id_ponto
							";
							
				$stmt = $con->prepare($select); 
				$params = array(':id_usuario' => $id_usuario);
				
				$stmt->execute($params);

				return $stmt;
				
					 
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}
		public function listarFront($id_usuario) 
		{
			try{
				$con = Conecta::criarConexao();
				
				$select = "SELECT p.id_ponto, ds_localidade, ds_descricao, nu_localidade, id_tipo,if(min(a.dt_final) >= CURDATE(), min(a.dt_inicial), (select min(dt_inicial) from tb_alugado al where dt_final >= CURDATE() and al.id_ponto=p.id_ponto )) as dt_inicial, if(min(a.dt_final) >= CURDATE(), min(a.dt_final), (select min(dt_final) from tb_alugado al where dt_final >= CURDATE() and al.id_ponto=p.id_ponto )) as dt_final, ds_sentido, nu_valor_ponto, pf.ds_foto
							FROM tb_ponto p
							left join tb_alugado a on p.id_ponto=a.id_ponto
							left join rl_ponto_foto pf on p.id_ponto=pf.id_ponto
							where id_tipo = 2 and id_usuario = :id_usuario
							group by p.id_ponto
							";
							
				$stmt = $con->prepare($select); 
				$params = array(
								':id_usuario' => $id_usuario); 
				
				$stmt->execute($params);

				return $stmt;
				
					 
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}
		public function listarTodosPonto($id_empresa) 
		{
			try{ 
				$con = Conecta::criarConexao(); 

				$select =	"SELECT p.id_ponto, ds_localidade, ds_descricao, ds_tamanho, nu_localidade, p.id_tipo,if(min(a.dt_final) >= CURDATE(), min(a.dt_inicial), (select min(dt_inicial) from tb_alugado al where dt_final >= CURDATE() and al.id_ponto=p.id_ponto )) as dt_inicial, if(min(a.dt_final) >= CURDATE(), min(a.dt_final), (select min(dt_final) from tb_alugado al where dt_final >= CURDATE() and al.id_ponto=p.id_ponto )) as dt_final, ds_sentido, nu_valor_ponto, pf.ds_foto, t.ds_tipo, p.id_tipo,
							count(a2.id_ponto) as total,
							c.ds_empresa
							FROM tb_ponto p
							join tb_alugado a2
							left join tb_alugado a on p.id_ponto=a.id_ponto
							left join tb_cliente c on a.id_cliente = c.id_cliente
							left join tb_tipo t on t.id_tipo=p.id_tipo 
							left join rl_ponto_foto pf on p.id_ponto=pf.id_ponto 
							where p.id_empresa = :id_empresa
							group by p.id_ponto
							ORDER BY id_ponto ASC
				";

				$stmt = $con->prepare($select); 
				$params = array(
								':id_empresa' => $id_empresa);
				
				$stmt->execute($params);

				return $stmt->fetchAll();
					 
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}
		public function listarPontoInicial($id_empresa) 
		{ 
			try{
				$con = Conecta::criarConexao();
				
				$select =	"SELECT p.id_ponto, ds_localidade, c.ds_empresa, ds_descricao, ds_tamanho, nu_localidade, p.id_tipo,if(min(a.dt_final) >= CURDATE(), min(a.dt_inicial), (select min(dt_inicial) from tb_alugado al where dt_final >= CURDATE() and al.id_ponto=p.id_ponto )) as dt_inicial, if(min(a.dt_final) >= CURDATE(), min(a.dt_final), (select min(dt_final) from tb_alugado al where dt_final >= CURDATE() and al.id_ponto=p.id_ponto )) as dt_final, ds_sentido, nu_valor_ponto, pf.ds_foto, t.ds_tipo, p.id_tipo,
							count(a2.id_ponto) as total
							FROM tb_ponto p
							join tb_alugado a2
							left join tb_alugado a on p.id_ponto=a.id_ponto
							left join tb_cliente c on a.id_cliente = c.id_cliente
							left join tb_tipo t on t.id_tipo=p.id_tipo 
							left join rl_ponto_foto pf on p.id_ponto=pf.id_ponto 
							where p.id_empresa = :id_empresa
							group by p.id_ponto
							ORDER BY total DESC
							LIMIT 10
							";

				$stmt = $con->prepare($select); 
				$params = array(
								':id_empresa' => $id_empresa);
				
				$stmt->execute($params);

				return $stmt;
				
					 
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}
		public function BuscarLocacaoEmpresa($id_empresa) 
		{
			try{
				$con = Conecta::criarConexao();
				
				$select =	"SELECT c.ds_empresa, a.dt_inicial, a.dt_final, p.ds_localidade, p.ds_descricao, rl.ds_foto, p.id_ponto, p.id_tipo, t.ds_tipo
							From tb_alugado a
							inner join tb_ponto p on a.id_ponto = p.id_ponto
							inner join tb_tipo t on t.id_tipo = p.id_tipo
							inner join rl_ponto_foto rl on rl.id_ponto = p.id_ponto
							inner join tb_cliente c on c.id_cliente = a.id_cliente
							where p.id_empresa = :id_empresa
							and a.dt_final > curdate()";

				$stmt = $con->prepare($select); 
				$params = array(
								':id_empresa' => $id_empresa);
				
				$stmt->execute($params);

				return $stmt;
				
					 
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}

		public function BuscarLocacaoEmpresaInicial($id_empresa) 
		{
			try{
				$con = Conecta::criarConexao();
				
				$select =	"SELECT c.ds_empresa, a.dt_inicial, a.dt_final, p.ds_localidade, p.ds_descricao, rl.ds_foto, p.id_ponto, p.id_tipo, t.ds_tipo
							From tb_alugado a
							inner join tb_ponto p on a.id_ponto = p.id_ponto
							inner join tb_tipo t on t.id_tipo = p.id_tipo
							inner join rl_ponto_foto rl on rl.id_ponto = p.id_ponto
							inner join tb_cliente c on c.id_cliente = a.id_cliente
							where p.id_empresa = :id_empresa
							and a.dt_final > curdate()
							order by a.id_alugado
							limit 5";

				$stmt = $con->prepare($select); 
				$params = array(
								':id_empresa' => $id_empresa);
				
				$stmt->execute($params);

				return $stmt;
				
					 
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}
		public function listarPontoCliente($id_cliente)
		{
			try{
				$con = Conecta::criarConexao();
				
				$select = "SELECT p.id_ponto, ds_localidade, ds_descricao, nu_localidade, p.id_tipo,dt_inicial, dt_final, ds_sentido, nu_valor_ponto, pf.ds_foto, a.nu_valor, t.ds_tipo
							FROM tb_alugado a
							left join tb_ponto p on p.id_ponto=a.id_ponto
							left join rl_ponto_foto pf on p.id_ponto=pf.id_ponto
							left join tb_tipo t on t.id_tipo=p.id_tipo 
							where a.id_cliente = :id_cliente and dt_final >= curdate()
							group by p.id_ponto
							order by dt_inicial
							";

				$stmt = $con->prepare($select); 
				$params = array(':id_cliente' => $id_cliente);
				
				$stmt->execute($params);

				return $stmt;
				
					
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}
		public function listarTotalCliente($id_cliente)
		{
			try{
				$con = Conecta::criarConexao();
				
				$select = "SELECT nu_valor
							from tb_alugado
							where id_cliente = :id_cliente and dt_final >= curdate()";
				
				$stmt = $con->prepare($select); 
				$params = array(':id_cliente' => $id_cliente);
				
				$stmt->execute($params);

				return $stmt;
					
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}

		public function listarPontosTipo($id_tipo, $id_usuario) 
		{
			try{
				$con = Conecta::criarConexao();
				
				$select = "SELECT p.id_ponto, ds_localidade, ds_descricao, nu_localidade, id_tipo,if(min(a.dt_final) >= CURDATE(), min(a.dt_inicial), (select min(dt_inicial) from tb_alugado al where dt_final >= CURDATE() and al.id_ponto=p.id_ponto )) as dt_inicial, if(min(a.dt_final) >= CURDATE(), min(a.dt_final), (select min(dt_final) from tb_alugado al where dt_final >= CURDATE() and al.id_ponto=p.id_ponto )) as dt_final, ds_sentido, nu_valor_ponto, pf.ds_foto
							FROM tb_ponto p
							left join tb_alugado a on p.id_ponto=a.id_ponto
							left join rl_ponto_foto pf on p.id_ponto=pf.id_ponto
							where id_tipo = :id_tipo and id_usuario = :id_usuario
							group by p.id_ponto
							";
							
				$stmt = $con->prepare($select); 
				$params = array(':id_tipo' => $id_tipo,
								':id_usuario' => $id_usuario);
				
				$stmt->execute($params);

				return $stmt;
				
					 
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
				print "ERRO:".$e->getMessage();		
			}
		}
		public function BuscarDadosPonto($id_ponto)
		{
			try{
				$con = Conecta::criarConexao();
				
				$select = "SELECT p.id_ponto, ds_localidade, ds_descricao, nu_localidade, id_tipo_cobranca, t.ds_tipo, p.id_tipo, ds_sentido, nu_valor_ponto, pf.ds_foto, ds_tamanho, id_tipo_cobranca, nu_valor_ponto, if(min(a.dt_final) >= CURDATE(), min(a.dt_inicial), (select min(dt_inicial) from tb_alugado al where dt_final >= CURDATE() and al.id_ponto=p.id_ponto )) as dt_inicial, if(min(a.dt_final) >= CURDATE(), min(a.dt_final), (select min(dt_final) from tb_alugado al where dt_final >= CURDATE() and al.id_ponto=p.id_ponto )) as dt_final
							FROM tb_ponto p 
							join tb_alugado a2
							left join tb_alugado a on p.id_ponto=a.id_ponto
							left join rl_ponto_foto pf on p.id_ponto=pf.id_ponto
							inner join tb_tipo t on p.id_tipo=t.id_tipo
							where p.id_ponto = :id_ponto";
				
				$stmt = $con->prepare($select); 
				$params = array(':id_ponto' => $id_ponto);
				
				
				$stmt->execute($params);

				return $stmt->fetch();
				
					
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}
		public function gravarAlterarPonto(array $dados)
		{
			
			$ds_localidade		= $dados['ds_localidade'];
			$ds_descricao		= $dados['ds_descricao'];
			$nu_localidade	    = $dados['ds_latitude'] . "/" . $dados['ds_longitude'];
			$id_tipo    		= $dados['id_tipo'];
			$id_ponto    		= $dados['id_ponto'];
			$ds_sentido    		= $dados['ds_sentido'];
			$ds_tamanho    		= $dados['ds_tamanho'];
			$id_tipo_cobranca   = $dados['id_tipo_cobranca'];
			$nu_valor_ponto   	= $dados['nu_valor_ponto'];
			$ds_foto            = $_FILES['fotos'];
			
			try{
				$con = Conecta::criarConexao();
				$update = "UPDATE tb_ponto set ds_localidade = :ds_localidade, ds_descricao = :ds_descricao, nu_localidade = :nu_localidade, id_tipo = :id_tipo, ds_sentido = :ds_sentido, ds_tamanho = :ds_tamanho, id_tipo_cobranca = :id_tipo_cobranca, nu_valor_ponto = :nu_valor_ponto
						WHERE id_ponto = :id_ponto";
				
				$stmt = $con->prepare($update);
				
				$params = array(':ds_localidade' => $ds_localidade, 
								':ds_descricao' => $ds_descricao, 
								':nu_localidade' => $nu_localidade,
								':id_tipo' => $id_tipo,
								':ds_sentido' => $ds_sentido,
								':ds_tamanho' => $ds_tamanho,
								':id_tipo_cobranca' => $id_tipo_cobranca,
								':nu_valor_ponto' => $nu_valor_ponto,
								':id_ponto'=>$id_ponto);
				$stmt->execute($params);

				if($ds_foto['name'][0] !== ""){
					$nome = $ds_foto['name'][0];
					$tipo = $ds_foto['type'][0];
					$tmp = $ds_foto['tmp_name'][0];
					$tamanho = $ds_foto['size'][0];
	
					//gravar foto
					$tamanho = 20000000;
	
					$error = array();
					$tamanho_mb = $tamanho/1024/1024;
	
					// Pega extensão da imagem
					preg_match("/\.(gif|bmp|png|jpg|jpeg|doc|docx|pdf){1}$/i", $nome, $ext);
					// Gera um nome único para o arquivo
					$nome_arquivo = md5(uniqid(time())) . "arquivo.". $ext[1];
					// Caminho de onde ficará o arquivo
					$caminho_arquivo = "/var/www/painelpro/docs_pontos/" . $nome_arquivo;
	
					$gravar_caminho_arquivo = "docs_pontos/" . $nome_arquivo;
					
	
					// Faz o upload da imagem para seu respectivo caminho
					$moved = move_uploaded_file($tmp,  $caminho_arquivo);
	
					$insert_foto = "Update rl_ponto_foto set ds_foto = :ds_foto where id_ponto=:id_ponto";
	
					$stmt_foto = $con->prepare($insert_foto);
			
					$params_foto = array(':id_ponto' => $id_ponto,
										':ds_foto' => $gravar_caminho_arquivo);
									
					$stmt_foto->execute($params_foto);
	
					
				}
				echo "Dados alterados com sucesso!";
				
				
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}
		public function gravarAlugado()
		{

			$id_cliente	    		= $_POST['id_cliente'];
			$id_ponto	   			= $_POST['id_ponto'];
			$id_tipo_aluguel		= $_POST["id_tipo_aluguel"];
			$Rvirgula = str_replace(",", "", $_POST["nu_valor"]); 
			$ReplaceValor = str_replace("R$ ", "", $Rvirgula);
			if($id_tipo_aluguel == 1){
				if(isset($_POST['bisemana'])){
					$listaCheckbox = $_POST['bisemana'];
	
					$id_bisemana= '';
					$nu_valor		= ($ReplaceValor/count($listaCheckbox));
					for ($i=0; $i < count($listaCheckbox); $i++) { 
						
							$id_bisemana = $listaCheckbox[$i];
							$con = Conecta::criarConexao();
							$select = "SELECT dt_inicial, dt_final 
										from tb_bisemana 
										where id_bisemana = :id_bisemana";
							
							$stmt = $con->prepare($select);
							
							$params = array(':id_bisemana' => $id_bisemana);
											
							$stmt->execute($params);
							$dados = $stmt->fetch();
							$dt_inicial = $dados["dt_inicial"];
							$dt_final = $dados["dt_final"];


						try{
							$con = Conecta::criarConexao();
							$insert = "INSERT into tb_alugado (id_cliente, id_ponto, nu_valor, dt_inicial, dt_final, dt_cadastro)
										VALUES (:id_cliente, :id_ponto, :nu_valor, :dt_inicial, :dt_final, curdate())";
							
							$stmt = $con->prepare($insert);
							
							$params = array(':id_cliente' => $id_cliente,
											':id_ponto' => $id_ponto,
											':nu_valor' => $nu_valor,
											':dt_inicial' => $dt_inicial,
											':dt_final' => $dt_final);
											
							$stmt->execute($params);
						}
						catch(exception $e) 
						{
							header('HTTP/1.1 500 Internal Server Error');
							print "ERRO:".$e->getMessage();		
						} 	
					}
					echo "Dados gravados com sucesso!";
				}


			}
			if($id_tipo_aluguel == 2){
				
				$meses	= $_POST["meses"];
				$nu_valor	= $ReplaceValor;
				$dt_inicial	= date('Y-m-d',strtotime($_POST["dt_inicial"]));
				$date = new DateTime(date('Y-m-d',strtotime($_POST["dt_inicial"])));
				$dias = $meses * 30;
				$date->modify('+'.$dias.'days');
				$dt_final = $date->format('Y-m-d');
				
				try{
					$con = Conecta::criarConexao();
					$insert = "INSERT into tb_alugado (id_cliente, id_ponto,  nu_valor, dt_inicial, dt_final)
								VALUES (:id_cliente, :id_ponto, :nu_valor, :dt_inicial, :dt_final)";
					
					$stmt = $con->prepare($insert);
					
					$params = array(':id_cliente' => $id_cliente,
									':id_ponto' => $id_ponto,
									':nu_valor' => $nu_valor,
									':dt_inicial' => $dt_inicial,
									':dt_final' => $dt_final);
									
					$stmt->execute($params);
					
					echo "Dados gravados com sucesso!";
					
				}
				catch(exception $e) 
				{
					header('HTTP/1.1 500 Internal Server Error');
					print "ERRO:".$e->getMessage();		
				}
			}
			

        }
		public function listarAlugado($id_ponto) 
		{
			try{
				$con = Conecta::criarConexao();
				
				$select = "SELECT id_alugado, ds_empresa, dt_final, dt_inicial
							FROM tb_alugado a
							inner join tb_cliente c on a.id_cliente=c.id_cliente
							where id_ponto=:id_ponto and dt_final >= curdate()
							order by dt_inicial";
				
				$stmt = $con->prepare($select); 
				$params = array(':id_ponto' => $id_ponto);
				
				$stmt->execute($params);

				return $stmt;
				
					 
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}

		public function BuscarTotalTipo($id_empresa) 
		{
			try{
				
				$con = Conecta::criarConexao();
				$select = "select 
							ds_tipo,
							(SELECT count(id_ponto) as id_ponto
											FROM tb_ponto p
											where id_empresa=:id_empresa and p.id_tipo = t.id_tipo) as qtd
						from tb_tipo t;";
	
				$stmt = $con->prepare($select); 
				$params = array(':id_empresa' => $id_empresa);
				
				$stmt->execute($params);

				return $stmt;
					 
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}

		public function excluirPonto(array $dados) 
		{
			$id_ponto = $dados["id_ponto"];
			try{
				$con = Conecta::criarConexao();
				
				$select = "delete from tb_ponto
							where id_ponto=:id_ponto";
				
				$stmt = $con->prepare($select); 
				$params = array(':id_ponto' => $id_ponto);
				
				$stmt->execute($params);
				
					 
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}

		public function excluirAlugado(array $dados) 
		{
			$id_alugado = $dados["id_alugado"];
			try{
				$con = Conecta::criarConexao();
				
				$select = "delete from tb_alugado
							where id_alugado=:id_alugado";
				
				$stmt = $con->prepare($select); 
				$params = array(':id_alugado' => $id_alugado);
				
				$stmt->execute($params);
				
					 
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}
		public function FazerRelatorio($dados, $id_empresa){ 
			if(isset($_REQUEST['array_excluir']) && $_REQUEST['array_excluir'] != ''){
				$array_excluir = $_REQUEST['array_excluir'];
			}else{
				$array_excluir = 0;
			}

			$query_status = '';
			if($dados['id_status'] == 'disponivel'){
				$query_status = 'and case
									when a.dt_inicial is not null
									then curdate() not between a.dt_inicial and a.dt_final
									else 1 = 1
								end';
			}elseif($dados['id_status'] == "indisponivel"){
				$query_status = ' and curdate() between a.dt_inicial and a.dt_final';
			}

			$query_localidade = '';
			if($dados['id_busca'] == 'localidade'){
				$query_localidade = 'and ds_localidade in (select ds_bairro from tb_bairro where id_bairro in '.$dados["localidade"].')';
			}

			$query_tipo = '';
			if($dados['ds_tipo'] != 'Todos'){
				$query_tipo = 'and p.id_tipo = '.$dados['ds_tipo'];
			}

			try{
				$con = Conecta::criarConexao();
				
				$select = "SELECT p.id_ponto, c.ds_empresa, ds_localidade, ds_descricao, nu_localidade, p.id_tipo,a.dt_inicial,a.dt_final, ds_sentido, nu_valor_ponto, pf.ds_foto, t.ds_tipo, p.id_tipo_cobranca, p.ds_tamanho
							FROM tb_ponto p
							left join tb_tipo t on t.id_tipo=p.id_tipo 
							left join tb_alugado a on p.id_ponto=a.id_ponto and id_alugado = (select id_alugado from tb_alugado a2 where a2.id_ponto=p.id_ponto and a2.dt_final > curdate() order by dt_inicial limit 1)
							left join tb_cliente c on a.id_cliente = c.id_cliente
							left join rl_ponto_foto pf on p.id_ponto=pf.id_ponto
							where p.id_empresa = :id_empresa
							".$query_status."
							".$query_localidade."
							".$query_tipo."
							and p.id_ponto not in (".$array_excluir.")
							group by p.id_ponto
							";
							
				$stmt = $con->prepare($select); 
				$params = array(':id_empresa' => $id_empresa);
				
				$stmt->execute($params);

				return $stmt;
				
					 
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
				print "ERRO:".$e->getMessage();		
			}
		}

		function buscarDadosDesseMes($id_empresa) 
		{
			function outdoorMes($id_empresa){
				try{
					$con = Conecta::criarConexao(); 
					
					
					$select = "SELECT 
							count(id_alugado) as qtd
							FROM tb_alugado a
							right join tb_ponto p on a.id_ponto = p.id_ponto
							WHERE p.id_empresa = :id_empresa
							and p.id_tipo = 1
							and (month(dt_inicial) = month(now()) or month(dt_final) = month(now()))
							or (id_alugado in (select id_alugado from tb_alugado where now() between dt_inicial and dt_final))";
	
					$stmt = $con->prepare($select);
					$params = array(':id_empresa' => $id_empresa);
				   
					$stmt->execute($params);
					$dados = $stmt->fetch();
					return $dados["qtd"];
	
					
				}	
				catch(Exception $e)
				{
					header('HTTP/1.1 500 Internal Server Error');
					print "ERRO:".$e->getMessage();	
				}	
			}

			function FrontMes($id_empresa){ 
				try{
					$con = Conecta::criarConexao(); 
					
					
					$select = "SELECT 
							count(id_alugado) as qtd
							FROM tb_alugado a
							right join tb_ponto p on a.id_ponto = p.id_ponto
							WHERE p.id_empresa = :id_empresa
							and p.id_tipo = 2
							and (month(dt_inicial) = month(now()) or month(dt_final) = month(now()))
							or (id_alugado in (select id_alugado from tb_alugado where now() between dt_inicial and dt_final))";
	
					$stmt = $con->prepare($select);
					   $params = array(':id_empresa' => $id_empresa);
				   
					$stmt->execute($params);
					$dados = $stmt->fetch();
					return $dados["qtd"];
	
					
				}	
				catch(Exception $e)
				{
					header('HTTP/1.1 500 Internal Server Error');
					print "ERRO:".$e->getMessage();	
				}	
			}

			return array(outdoorMes($id_empresa), FrontMes($id_empresa));
			
		}

		function buscarDadosMesPassado($id_empresa) 
		{
			function outdoorPassado($id_empresa){
				try{
					$con = Conecta::criarConexao(); 
					
					
					$select = "SELECT 
					count(id_alugado) as qtd
					FROM tb_alugado a
					right join tb_ponto p on a.id_ponto = p.id_ponto
					WHERE p.id_empresa = :id_empresa
					and p.id_tipo = 1
					and ((month(dt_inicial)-1) = month(now()) or month(dt_final) = (month(now())-1))
					or (id_alugado in (select id_alugado from tb_alugado where date_sub(now(), interval 1 month) between dt_inicial and dt_final))";
	
					$stmt = $con->prepare($select);
					   $params = array(':id_empresa' => $id_empresa);
				   
					$stmt->execute($params);
					$dados = $stmt->fetch();
					return $dados["qtd"];
	
					
				}	
				catch(Exception $e)
				{
					header('HTTP/1.1 500 Internal Server Error');
					print "ERRO:".$e->getMessage();	
				}	
			}

			function FrontPassado($id_empresa){
				try{
					$con = Conecta::criarConexao(); 
					
					
					$select = "SELECT 
							count(id_alugado) as qtd
							FROM tb_alugado a
							right join tb_ponto p on a.id_ponto = p.id_ponto
							WHERE p.id_empresa = :id_empresa
							and p.id_tipo = 2
							and ((month(dt_inicial)-1) = month(now()) or month(dt_final) = (month(now())-1))
							or (id_alugado in (select id_alugado from tb_alugado where date_sub(now(), interval 1 month) between dt_inicial and dt_final))";
	
					$stmt = $con->prepare($select);
					   $params = array(':id_empresa' => $id_empresa);
				   
					$stmt->execute($params);
					$dados = $stmt->fetch();
					return $dados["qtd"];
	
					
				}	
				catch(Exception $e)
				{
					header('HTTP/1.1 500 Internal Server Error');
					print "ERRO:".$e->getMessage();	
				}	
			}

			return array(outdoorPassado($id_empresa), FrontPassado($id_empresa));
			
		}
		// public function listarOptionsLocalidade($id_bairro)
		// {
		// 	try{
		// 		$con = Conecta::criarConexao();
		// 		$select = "SELECT id_bairro, ds_bairro
		// 					FROM tb_bairro ";
		// 		$stmt = $con->prepare($select);
		// 		$stmt->execute();

		// 		$options = "";

		// 		while($dados = $stmt->fetch())
		// 		{
		// 			$valores = explode(",", $id_bairro);
		// 			if(in_array($dados['id_bairro'], $valores)){
		// 				$options.= "<input type='checkbox' id='".$dados['id_bairro']."' name='localidade[]' value='".$dados['id_bairro']."' checked>
		// 					<label for='".$dados['id_bairro']."'>".$dados['ds_bairro']."</label></br>"; 
		// 			}
		// 			else{
		// 				$options.= "<input type='checkbox' id='".$dados['id_bairro']."' name='localidade[]' value='".$dados['id_bairro']."'>
		// 					<label for='".$dados['id_bairro']."'>".$dados['ds_bairro']."</label></br>"; 
		// 			} 
				
					

					
		// 		}
		// 		return $options;

		// 	}
		// 	catch(exception $e)
		// 	{
		// 	header('HTTP/1.1 500 Internal Server Error');
		// 	print $e->getMessage();
		// 	}
		// }
		public function listarOptionsLocalidade() 
		{
			try{
				$con = Conecta::criarConexao();
				
				$select = "SELECT id_bairro, ds_bairro
							FROM tb_bairro
							order by id_bairro";
				
				$stmt = $con->prepare($select); 
				
				$stmt->execute();

				return $stmt;
				
					
			}
			catch(exception $e)
			{
				header('HTTP/1.1 500 Internal Server Error');
    			print "ERRO:".$e->getMessage();		
			}
		}

        
    }



?>