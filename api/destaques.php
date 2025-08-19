<?php
	header('Content-Type: application/json; charset=utf-8');
	try{
		require_once("../Classes/Ponto.php");
		$ponto = new Ponto();
		$stmt = $ponto->listarPontoInicial(2);
		$rows = [];
		while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
			$rows[] = [
				'id_ponto' => (int)$r['id_ponto'],
				'localidade' => $r['ds_localidade'],
				'tipo' => $r['ds_tipo'],
				'tamanho' => $r['ds_tamanho'],
				'foto' => 'https://painelpro.pro/'.$r['ds_foto'],
				'descricao' => $r['ds_descricao']
			];
		}
		echo json_encode(['ok'=>true,'data'=>$rows], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
	}catch(Exception $e){
		http_response_code(500);
		echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
	} 