<?php
	// Salva lead recebido via JSON em CSV simples e envia e-mail
	header('Content-Type: application/json');
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

		// Garante diretório
		$dir = __DIR__;
		$file = $dir . DIRECTORY_SEPARATOR . 'data.csv';
		if(!file_exists($file)){
			file_put_contents($file, "timestamp,midia,nome,telefone,email,local,page\n");
		}
		$linha = sprintf("%s,%s,%s,%s,%s,%s,%s\n",
			str_replace(["\n","\r"],'',$ts),
			str_replace([',','\n','\r'],' ',$midia),
			str_replace([',','\n','\r'],' ',$nome),
			str_replace([',','\n','\r'],' ',$telefone),
			str_replace([',','\n','\r'],' ',$email),
			str_replace([',','\n','\r'],' ',$local),
			str_replace([',','\n','\r'],' ',$page)
		);
		file_put_contents($file, $linha, FILE_APPEND);

		// Envia e-mail simples
		$to = 'contato.universomidia@gmail.com, rodrigouniversomidia@gmail.com, victorespucoc@gmail.com';
		$subject = 'Novo lead - Universo Mídia';
		$body = "Novo lead recebido:\n\n".
			"Mídia: $midia\n".
			"Nome: $nome\n".
			"Telefone: $telefone\n".
			"Email: $email\n".
			"Local: $local\n".
			"Página: $page\n".
			"Data: $ts\n";
		$headers = 'From: noreply@universomidia.com.br' . "\r\n" .
				   'Reply-To: noreply@universomidia.com.br' . "\r\n" .
				   'X-Mailer: PHP/' . phpversion();
		@mail($to, $subject, $body, $headers);

		echo json_encode(['ok'=>true]);
	}catch(Exception $e){
		http_response_code(400);
		echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
	} 