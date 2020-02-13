<?php
	//iniciar a sessao
	session_start();

	//validar o login
	include "verificalogin.php";

	//configurar para serem mostrados os erros do php
	ini_set("display_error",1);
	ini_set("display_startup_errors",1);
	error_reporting(E_ALL);

	//incluir a conexao com o banco e as funcoes
	include "config/conexao.php";
	include "config/funcoes.php";

	$porta = $_SERVER["SERVER_PORT"];

?>
<!DOCTYPE html>
<html lang="pr-BR">
<head>
	<title>SCHq - Sistema de Controle de HQs</title>
	<meta charset="utf-8">

	<base href="http://<?=$_SERVER['SERVER_NAME']. ":$porta" . $_SERVER['SCRIPT_NAME']?>">

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/all.min.css">

	<link rel="shortcut icon" href="images/icone.png">

	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/popper.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>

</head>
<body class="bg">
	<?php

		if ( $_POST ) {

			$venda = $produto = $valor = $quantidade = "";
			foreach ($_POST as $key => $value) {
				$$key = trim ( $value );
			}

			$valor = formataValor($valor);
			
			$sql = "insert into venda_quadrinho values(:venda, :quadrinho, :quantidade, :valor)";
			$consulta = $pdo->prepare($sql);
			$consulta->bindValue(":venda", $venda, PDO::PARAM_INT);
			$consulta->bindValue(":quadrinho", $produto, PDO::PARAM_INT);
			$consulta->bindValue(":quantidade", $quantidade, PDO::PARAM_INT);
			$consulta->bindValue(":valor", $valor);

			if( $consulta->execute() ){
				echo "<script>alert('Quadrinho inserido');</script>";
			}else{
				echo "<script>alert('Erro ao inserir');</script>";
				echo $consulta->errorInfo()[2];
			}
			
		}

		if( empty( $venda ) ) $venda = trim ( $_GET["venda"] );

	?>

	<table class="table table-bordered table-striped table-houver">
		<thead>
			<tr>
				<td>Quadrinho</td>
				<td>Quantidade</td>
				<td>Valor</td>
				<td>Total</td>
				<td>Opções</td>
			</tr>
		</thead>
	

	<?php
		$soma=0;
		$sql = "select q.id, q.titulo, vq.valor, vq.quantidade, (vq.valor * vq.quantidade) total
			from venda_quadrinho vq
			inner join quadrinho q on (q.id = vq.quadrinho_id)
			where vq.venda_id = :venda order by q.titulo";
		
		$consulta = $pdo->prepare($sql);
		$consulta->bindValue(":venda", $venda);
		$consulta->execute();

		while ( $linha = $consulta->fetch( PDO::FETCH_OBJ ) ) {

			$valor = number_format($linha->valor,2,",",".");
			$total = number_format($linha->total,2,",",".");

			$soma = $soma + $linha->total;

			echo "
			<tr>
				<td>$linha->titulo</td>
				<td>$linha->quantidade</td>
				<td>$linha->valor</td>
				<td>$linha->total</td>
				<td>
					<a href='javascript:excluir($venda,$linha->id)' class='btn btn-danger'>
						<i class='fas fa-trash'></i>
					</a>
				</td>
			</tr>
			";
		}

		$soma = number_format($soma,2,",",".");
	?>

	<tfoot>
		<tr>
			<td>TOTAL:</td>
			<td></td>
			<td></td>
			<td>R$ <?=$soma;?></td>
		</tr>
	</tfoot>
	</table>

	<script type="text/javascript">
		function excluir(venda, quadrinho){
			if( confirm ( "Deseja mesmo excluir?")){
				location.href = "excluirQuadrinho.php?venda="+venda+"&quadrinho="+quadrinho;
			}

		}
	</script>
</body>
</html>