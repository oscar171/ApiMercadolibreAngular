<?php 
if(!isset($_SESSION)){
session_start();
}
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';
require_once 'ltable_olib.php';

$meli = new Meli(ML_APPID, ML_KEY);
error_log('UNO');
$fo = new lt_form();
///$fo->tipo = LT_FORM_DIRECT;
$fo->bHeader = FALSE;
if ($fo->dbopen())
{
	error_log('DOS');
	$fo->style("#prod_no_publicados { background-color: #ff2; }");

	//	seleccionamos todos los productos de la base de datos que no han sido publicados en ML
	$sql="SELECT producto_id,cod_adux,id_ficha,descripcion FROM productos LIMIT 15";
	if (($q = myquery::q($fo, $sql, 'MLPRNOPUB-1')))
	{
		error_log('TRES');
		$fo->hdra(2, 0, '', 'margin-bottom:20px; float:left;');
		$fo->span("<i class=\"fa fa-list\" aria-hidden=\"true\"></i>");
		$fo->buf .= 'PUBLICAR ';
		$fo->span(sprintf("(%d)", $q->sz), '', 'font-size:0.8em');
		$fo->hdrx(2);
	
		$fo->div('listado');
		$fo->tbl(0, 1, 0, 'table table-responsive no_public', 'border: hidden;', FALSE, 'getValues'); 
		$fo->tr('mv-yellow no_public');
		$fo->thh(); $fo->spann(); $fo->chk('marcarTodo'); $fo->spanx();
		$fo->thh(); $fo->span('Imagen');
		$fo->thh(); $fo->span('Titulo');
		$fo->thh(); $fo->span('Precio');
		$fo->thh(); $fo->span('Cantidad');
		$fo->thh(); $fo->span('Condicion');
		$fo->thh(); $fo->span('Tipo');
		$fo->thx();
		$fo->trx();
		$i = 0;
		foreach ($q->a as $it)
		{
			$sql3 = "SELECT cantidad FROM existencias WHERE almacen_id=105 AND producto_id=".$it->producto_id;
			if (($q2 = myquery::q($fo, $sql3, 'MLPRNOPUB-2')))
			{
				if ($q2->r->cantidad > 0)
				{
					$fo->tr();
					$fo->td();
					$fo->chk('selecionado');
					$fo->buf .= "<input type=\"checkbox\" name=\"producto_id\" id=\"selecionado\" value=\"$it->producto_id\">";
					$fo->td();
					if (($q3 = myquery::q($fo, "SELECT imagen2 FROM productos_fichas WHERE prodficha_id=".$it->id_ficha, 'MLPRNOPUB-3')))
					{
						$fo->img("data:image/jpeg;base64,".$q3->r->imagen2, 'img-rounded', 'width:100px;');
					}
					$fo->tdc($it->descripcion);
					$fo->tdc(nes(lt_registro::campo($fo, 'productos_precios', array($it->producto_id, 1, 'A'), 'precio_base', 0)), 2);
					$fo->tdc($q2->r->cantidad, 2);
					$fo->td();
					$radio = new lt_radiobutton();
					$radio->options = array(array('Nuevo','new'), array('Usado','used'));
					$radio->render($fo);

					/*
					 <td><label><input class="no_public" type="radio" value="new" name="<?="condicion".$i ?>" >Nuevo</label>
					
					 <label><input type="radio" value="used" name="<?="condicion".$i ?>">Usado</label></td>
					
					 <td><label><input type="radio" value="gold_special" name="<?="tipo".$i ?>" id="tipo" >Premium</label>
					
					 <label><input  type="radio" value="bronze" name="<?="tipo".$i ?>" id="tipo">Bronce</label>
					 <label><input  type="radio" value="free" name="<?="tipo".$i ?>" id="tipo">Gratis</label></td>
					 	*/
					
					$fo->trx();
				
					$i++;
				}
			}
		}
		$fo->tblx();
		$fo->buf .= "<button type='submit' class='btn btn-info btn-sm ' id='publicar'> Publicar Productos </button>";
		$fo->js('../js/products_no_publicados.js');
	}
	else $fo->warn('No hay productos disponibles para publicacion');
}
$fo->show();
?>