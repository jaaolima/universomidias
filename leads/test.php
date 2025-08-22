<?php
	header('Content-Type: text/plain; charset=utf-8');
	echo "=== Teste de Permissões - Diretório Leads ===\n\n";
	
	$dir = __DIR__;
	echo "Diretório atual: $dir\n";
	echo "Permissões: " . substr(sprintf('%o', fileperms($dir)), -4) . "\n";
	echo "Pode escrever: " . (is_writable($dir) ? 'SIM' : 'NÃO') . "\n";
	echo "Pode ler: " . (is_readable($dir) ? 'SIM' : 'NÃO') . "\n\n";
	
	$file = $dir . DIRECTORY_SEPARATOR . 'data.csv';
	echo "Arquivo CSV: $file\n";
	if(file_exists($file)){
		echo "Existe: SIM\n";
		echo "Permissões: " . substr(sprintf('%o', fileperms($file)), -4) . "\n";
		echo "Pode escrever: " . (is_writable($file) ? 'SIM' : 'NÃO') . "\n";
		echo "Tamanho: " . filesize($file) . " bytes\n";
	}else{
		echo "Existe: NÃO\n";
		echo "Pode criar: " . (is_writable($dir) ? 'SIM' : 'NÃO') . "\n";
	}
	
	echo "\n=== Teste de Escrita ===\n";
	$test_file = $dir . DIRECTORY_SEPARATOR . 'test_write.txt';
	$test_content = "Teste de escrita em " . date('Y-m-d H:i:s') . "\n";
	
	if(file_put_contents($test_file, $test_content) !== false){
		echo "Escrita de teste: SUCESSO\n";
		if(unlink($test_file)){
			echo "Remoção de teste: SUCESSO\n";
		}else{
			echo "Remoção de teste: FALHOU\n";
		}
	}else{
		echo "Escrita de teste: FALHOU\n";
	}
	
	echo "\n=== Informações do Servidor ===\n";
	echo "PHP Version: " . phpversion() . "\n";
	echo "User: " . (function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'N/A') . "\n";
	echo "Temp dir: " . sys_get_temp_dir() . "\n";
	
	// Tenta criar o CSV se não existir
	echo "\n=== Criando CSV se não existir ===\n";
	if(!file_exists($file)){
		$header = "timestamp,midia,nome,telefone,email,local,page\n";
		if(file_put_contents($file, $header) !== false){
			echo "CSV criado com sucesso!\n";
		}else{
			echo "Falha ao criar CSV\n";
		}
	}else{
		echo "CSV já existe\n";
	} 