<?php
	// Salva lead recebido via JSON em CSV simples e envia e-mail
	header('Content-Type: application/json');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Headers: Content-Type');
	
	try{
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);
		if(!$data){ throw new Exception('Payload inválido'); }

		$midia = isset($data['midia']) ? $data['midia'] : '';
		$nome = isset($data['nome']) ? $data['nome'] : '';
		$telefone = isset($data['telefone']) ? $data['telefone'] : '';
		$email = isset($data['email']) ? $data['email'] : '';
		$local = isset($data['local']) ? $data['local'] : '';
		$page = isset($data['page']) ? $data['page'] : '';
		$ts = isset($data['ts']) ? $data['ts'] : date('c');

		// Tenta salvar no CSV
		$csv_salvo = false;
		try{
			$dir = __DIR__;
			$file = $dir . DIRECTORY_SEPARATOR . 'data.csv';
			
			// Verifica se pode escrever no diretório
			if(!is_writable($dir) && !is_writable($file)){
				// Tenta criar com permissões
				if(!file_exists($file)){
					$header = "timestamp,midia,nome,telefone,email,local,page\n";
					$csv_salvo = file_put_contents($file, $header) !== false;
				}
			}
			
			if(!$csv_salvo && file_exists($file)){
				$csv_salvo = is_writable($file);
			}
			
			if($csv_salvo){
				$linha = sprintf("%s,%s,%s,%s,%s,%s,%s\n",
					str_replace(["\n","\r"],'',$ts),
					str_replace([',','\n','\r'],' ',$midia),
					str_replace([',','\n','\r'],' ',$nome),
					str_replace([',','\n','\r'],' ',$telefone),
					str_replace([',','\n','\r'],' ',$email),
					str_replace([',','\n','\r'],' ',$local),
					str_replace([',','\n','\r'],' ',$page)
				);
				$csv_salvo = file_put_contents($file, $linha, FILE_APPEND) !== false;
			}
		}catch(Exception $csv_err){
			// Log do erro CSV mas continua
			error_log("Erro CSV: " . $csv_err->getMessage());
		}

		// Envia e-mail simples (sempre tenta)
		$email_enviado = false;
		try{
			$to = 'contato.universomidia@gmail.com, rodrigouniversomidia@gmail.com';
			$subject = 'Novo lead - Universo Mídia';
			$body = "Novo lead recebido:\n\n".
				"Mídia: $midia\n".
				"Nome: $nome\n".
				"Telefone: $telefone\n".
				"Email: $email\n".
				"Local: $local\n".
				"Página: $page\n".
				"Data: $ts\n".
				"CSV salvo: " . ($csv_salvo ? 'Sim' : 'Não') . "\n";
			$headers = 'From: noreply@universomidia.com.br' . "\r\n" .
					   'Reply-To: noreply@universomidia.com.br' . "\r\n" .
					   'X-Mailer: PHP/' . phpversion();
			$email_enviado = @mail($to, $subject, $body, $headers);
		}catch(Exception $email_err){
			error_log("Erro email: " . $email_err->getMessage());
		}

		// Retorna sucesso mesmo se CSV falhar
		echo json_encode([
			'ok' => true,
			'csv_salvo' => $csv_salvo,
			'email_enviado' => $email_enviado,
			'msg' => 'Lead processado com sucesso'
		]);
		
	}catch(Exception $e){
		http_response_code(400);
		echo json_encode([
			'ok' => false,
			'error' => $e->getMessage(),
			'file' => $e->getFile(),
			'line' => $e->getLine()
		]);
	} 