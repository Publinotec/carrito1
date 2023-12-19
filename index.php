<?php
// Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");

// O, si quieres permitir solo un origen específico
// header("Access-Control-Allow-Origin: http://localhost:8080"); // Reemplaza con tu URL frontend

// Otros encabezados CORS necesarios
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

session_start();

// Configuración de la conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "Palamor_5";
$database = "carrito_compras";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Agregar producto al carrito
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $sql = "SELECT * FROM products WHERE id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $cart_product = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'quantity' => $quantity,
            'total' => $row['price'] * $quantity
        ];

        // Inicializar la sesión del carrito si no existe
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Verificar si el producto ya está en el carrito
        $product_index = array_search($product_id, array_column($_SESSION['cart'], 'id'));

        if ($product_index !== false) {
            // Si el producto ya está en el carrito, actualiza la cantidad y el total
            $_SESSION['cart'][$product_index]['quantity'] += $quantity;
            $_SESSION['cart'][$product_index]['total'] += $row['price'] * $quantity;
        } else {
            // Agregar producto al carrito
            $_SESSION['cart'][] = $cart_product;
        }
    }
}

// Eliminar producto del carrito
if (isset($_GET['remove_from_cart'])) {
    $remove_id = $_GET['remove_from_cart'];
    $_SESSION['cart'] = array_values(
        array_filter($_SESSION['cart'], function ($item) use ($remove_id) {
            return $item['id'] != $remove_id;
        })
    );
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            text-align: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
            transition: color 0.3s;
        }

        h2:hover {
            color: #4CAF50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .cart-container {
            margin-top: 30px;
        }

        .cart-item {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .cart-item a {
            margin-left: 10px;
            color: red;
            cursor: pointer;
            text-decoration: none;
        }

        .quantity-input {
            width: 40px;
            text-align: center;
            margin-right: 10px;
        }

        .cart-controls {
            display: flex;
            align-items: center;
        }

        .cart-controls button {
            margin-right: 10px;
            padding: 5px 10px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            transition: background-color 0.3s;
        }

        .cart-controls button:hover {
            background-color: #45a049;
        }

        .delete-btn {
            background-color: #f44336;
        }

        .delete-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <a href='cart.php'><img src='images/pagar.png' alt='Ir al Carrito' width='50' height='50'>Ir al Carrito</a>

    <h2>Productos Disponibles</h2>

    <?php
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class='cart-item'>";
            echo "<div>";
            echo "<p><strong>{$row['name']}</strong> - {$row['price']} USD</p>";
            echo "</div>";
            echo "<div class='cart-controls'>";
            echo "<form method='post' action='index.php'>";
            echo "<input class='quantity-input' type='number' name='quantity' value='1' min='1' />";
            echo "<input type='hidden' name='product_id' value='{$row['id']}' />";
            echo "<button type='submit' name='add_to_cart'>Agregar</button>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "No hay productos disponibles.";
    }
    ?>

    <h2>Items agregados</h2>

    <?php
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        echo "<table>";
        echo "<tr>";
        echo "<th>Producto</th>";
        echo "<th>Cantidad</th>";
        echo "<th>Valor Unitario</th>";
        echo "<th>Subtotal</th>";
        echo "<th>Eliminar</th>";
        echo "</tr>";

        foreach ($_SESSION['cart'] as $item) {
            echo "<tr>";
            echo "<td>{$item['name']}</td>";
            echo "<td>{$item['quantity']}</td>";
            echo "<td>{$item['price']} USD</td>";
            echo "<td>{$item['total']} USD</td>";
            echo "<td><a class='delete-btn' href='index.php?remove_from_cart={$item['id']}'>Eliminar</a></td>";
            echo "</tr>";
        }

        // Calcular la sumatoria de precios en el carrito
        $total_price = array_sum(array_column($_SESSION['cart'], 'total'));

        echo "<tr class='total'>";
        echo "</tr>";

        echo "</table>";
    } else {
        echo "<p>No tienes items agregados al carrito.</p>";
    }
    ?>

</body>
</html>
