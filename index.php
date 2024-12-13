<?php 

require_once './clases/Producto.php';
require_once './clases/Inventario.php';
require_once './clases/Ventas.php';

    function displayMenu(){
        echo "---- Menu de la Tiendita ---- \n";
        echo "1. Agregar nuevo producto \n";
        echo "2. Eliminar producto \n";
        echo "3. Actualizar producto \n";
        echo "4. Generar Venta \n";
        echo "5. Generar informe \n";
        echo "6. Salir \n";
        echo "Seleccione una opcion: ";
    }

    function prompt($mensaje){
        echo $mensaje;
        $input = trim(fgets(STDIN));
        return $input;
    }

      
    //funcion para generar informe de productos sin stock
    function generarInformeProductosSinStock($inventario) {
        $productosSinStock = array_filter($inventario->getListaProductos(), function($producto) {
            return $producto->getStock() == 0;
        });
        if (empty($productosSinStock)) {
            echo "No hay productos sin stock \n";
        } else {
            echo "Productos sin stock: \n";
            foreach ($productosSinStock as $producto) {
                echo "_" . $producto->getNombre() . "(ID: " . $producto->getId() . ")\n";
            }
        }
    }
    

    //funcion para generar informe de productos con stock mas bajo 
    function generarInformeProductosConStockMasBajo($inventario, $stockLimite) {
        $productosConStockMasBajo = array_filter($inventario->getListaProductos(), function($producto) use ($stockLimite) {
            return $producto->getStock() < $stockLimite;
        });
        if (empty($productosConStockMasBajo)) {
            echo "No hay productos con stock menor a $stockLimite \n";
        } else {
            echo "Productos con stock bajo: \n";
            foreach ($productosConStockMasBajo as $producto) {
                echo "_" . $producto->getNombre() . "(Stock: " . $producto->getStock() . " ID: " . $producto->getId() . ")\n";
            }
        }
    }
    

  //funcion para generar informe de productos de X precio
  function generarInformeProductosPorPrecio($inventario, $precioLimite) {
        $productosPorPrecio = array_filter($inventario->getListaProductos(), function($producto) use ($precioLimite) {
            return $producto->getPrecio() > $precioLimite;
        });
        if (empty($productosPorPrecio)) {
            echo "No hay productos con precio mayor a $precioLimite \n";
        } else {
            echo "Productos con precio mayor a $precioLimite: \n";
            foreach ($productosPorPrecio as $producto) {
                echo "_" . $producto->getNombre() . "(Precio: $" . $producto->getPrecio() . " ID: " . $producto->getId() . ")\n";
            }
        }
    }


    // Arreglo a trabajar


    $inventario = new Inventario([]);

    // agregar productos iniciales.

    $producto1 = new Producto(1, "Cuadril", "Ala, Piernita, Culito de pollo empanizado", 1, 10, "Don Pollo", "Comida");
    $producto2 = new Producto(2, "SuperCheese", "Pizza con orilla de queso", 5, 10, "Little Pizza", "Comida");
    $producto3 = new Producto(3, "Texas Whopper", "Hamburguesa con jalapeño", 8, 10, "Burguerking", "Comida");
    $inventario->agregarProducto($producto1);
    $inventario->agregarProducto($producto2);
    $inventario->agregarProducto($producto3);

    // FUNCION QUE NOS MUESTRA LOS DATOS A COMPRAR

function ProductoDisponible($inventario) {


    echo "Productos Disponibles: \n";

    foreach ($inventario->getListaProductos() as $producto) {

        echo "ID: " . $producto->getId() . " | ";
        echo "Nombre: " . $producto->getNombre() . " | ";
        echo "Precio: $" . $producto->getPrecio() . " | ";
        echo "Stock: " . $producto->getStock() . "\n";
    }

    echo "-------------------------------------------------------------\n";
}

    // MENU DE OPCIONES. 

    
    $flag = true;
    $idProducto = 0;
    while($flag){
        displayMenu();
       $opcion = prompt("");
        switch($opcion){
            case 1: 
                //Obtenemos valores de producto a traves del uso de prompt (funcion para obtener valores de la terminal)
                $idProducto = $idProducto+1;
                $nombre = prompt("Ingrese el nombre del producto:\n");
                $descripcion = prompt("Ingrese la descripcion del producto:\n");
                $precio = prompt("Ingrese el precio del producto:\n");
                $cantidad = prompt("Ingrese la cantidad del producto:\n");
                $categoria = prompt("Ingrese la categoria de su producto: \n");
                $proveedor = prompt("Ingrese quien es el proveedor de su producto: \n");
                //Creamos nuevo producto con los valores recibidos por prompt
                $producto = new Producto($idProducto,$nombre,$descripcion,$precio,$cantidad,$proveedor,$categoria);
                
                //Agregamos el nuevo producto a nuestro inventario
                $inventario->agregarProducto($producto);
                echo "Su nueva menu es: \n";
                productoDisponible($inventario);
                break;
            case 2: 
                echo "Estas eliminando un producto. \n";

                productoDisponible($inventario);

                $idProducto = (int)prompt("Ingrese el id a eliminar: \n");

                $resultado = $inventario->eliminarProducto($idProducto);

                if($resultado){
                    echo "Se elimino el producto correctamente, el nuevo menu es: \n"; 
                    productoDisponible($inventario);
                }else {
                    echo "No se encontro el producto a eliminar,revise el ID";
                    productoDisponible($inventario);
                }
                break;

            case 3:
                echo "Estas editando un producto \n";

                productoDisponible($inventario);

                $idProducto = (int)prompt("Ingresa el Id a editar: \n"); 

                // Buscamos en el inventario. 

                $producto = $inventario->obtenerIdInventario($idProducto);

                // validación 

                if($producto === null) {

                    echo "Producto no encontrado\n";
                } else {
                    echo "El producto encontrado es:" .$producto->getNombre(). "\n";
                }

                // Formulario para cambiar el producto.

                echo "Modifica producto // Deja en blanco si no deseas cambiar el valor: \n \n";

                // Promps 

                $nombre = prompt("Ingrese el nuevo nombre: \n");
                $descripcion = prompt("Ingrese la nueva descripción: \n");
                $precio = prompt("Agrega el nuevo precio: \n");
                $categoria = prompt("Cambia a una categoría el producto:\n");

                // Creamos el nuevo arreglo 

                $datosActualizados = [];

                // Agregamos los datos con ternarios

                $datosActualizados = [
                    'nombre' => $nombre !== "" ? $nombre : null,
                    'descripcion' => $descripcion !== "" ? $descripcion : null, 
                    'precio' => $precio !== "" ? (float)$precio : null,
                    'categoria' => $categoria !== "" ? $categoria : null,
                ];

                // pusheamos los cambios 

                $producto->editarProducto($datosActualizados);

                echo "Producto actualizado con exito. Cambios realizados: \n";
                print_r($producto);
              
                break;

            case 4:

                echo "PUEDES COMPRAR: \n";

                productoDisponible($inventario);

                // iniciamos un array auxiliar

                $listaProductoVenta = []; 
            

                while (true) {
                    $idProductoVenta = (int)prompt("Ingresa ID del producto a comprar (0 para finalizar): \n");
                    
                    if ($idProductoVenta === 0) {
                        break; // Salimos del bucle si el usuario ingresa "0".
                    }
                
                    // Buscamos el producto en el inventario.
                    $producto = $inventario->obtenerIdInventario($idProductoVenta); 
                    if ($producto === null) {
                        echo "Producto no encontrado, intente nuevamente.\n"; 
                    } else { 
                        $listaProductoVenta[] = $producto; // Agregamos el producto al array.
                        echo "Producto agregado: " . $producto->getNombre() . "\n"; 
                    }
                }

                if(empty($listaProductoVenta)) {
                    echo "No se agregaron productos a la venta. \n";
                    break;
                }

                // Generador de factura de venta. 

                $idVenta = uniqid(); 
                $venta = new Venta($idProducto, $listaProductoVenta); 

                // utilizamos metodo calcular ya dado en clase. 
                $totalventa = $venta->calcularTotal();

                echo "EN ESTA COMPRA SE ADQUIERE:\n";
                echo "ID de la Venta: $idVenta\n";
                echo "Productos incluidos:\n";

                foreach($venta->getListaProductos() as $producto) {
                    echo "1- " . $producto->getNombre() . " | Precio: $" . $producto->getPrecio() . "\n";

                }
                echo  "---------------------------------------------\n";
                echo "Total de la Venta: $" . $totalventa . "\n \n";



                foreach($venta->getListaProductos() AS $producto) {
                    $nuevoStock = $producto->getStock() -1;
                    $producto->actualizarStock($nuevoStock);
                }


                break;

            case 5:

                echo "Seleccione una opcion para generando un informe \n";
                echo "1. Informe de productos sin stock \n";
                echo "2. Informe de productos con stock mas bajo \n";
                echo "3. Informe de productos por precio mayor de X precio \n";
            
                // Capturamos la opción del usuario
                $opcion = prompt("Ingrese la opcion deseada: \n");
            
                // Verificamos la opción ingresada y ejecutamos la acción correspondiente
                switch ($opcion) { // Cambiado de $opcionInforme a $opcion
                    case 1:
                        generarInformeProductosSinStock($inventario);
                        break;
                    case 2:
                        $stockBajo = prompt("Ingrese el stock minimo: \n");
                        generarInformeProductosConStockMasBajo($inventario, $stockBajo);
                        break;
                    case 3:
                        $precioLimite = prompt("Ingrese el precio limite: \n");
                        generarInformeProductosPorPrecio($inventario, $precioLimite);
                        break;
                    default: 
                        echo "Seleccione una opcion valida \n";
                break;

                    }

            case 6:
                echo "Estas saliendo ... \n";
                $flag = false;
                break;

            default: 
            echo "Seleccione una opcion valida \n";

        }


    }
?>