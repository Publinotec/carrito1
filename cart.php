<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Resumen</title>

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

        .total {
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <h2>Resumen del Carrito de Compras</h2>

    <?php
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        echo "<table>";
        echo "<tr>";
        echo "<th>Producto</th>";
        echo "<th>Cantidad</th>";
        echo "<th>Valor Unitario</th>";
        echo "<th>Subtotal</th>";
        echo "</tr>";

        foreach ($_SESSION['cart'] as $item) {
            echo "<tr>";
            echo "<td>{$item['name']}</td>";
            echo "<td>{$item['quantity']}</td>";
            echo "<td>{$item['price']} </td>";
            echo "<td>{$item['total']} </td>";
            echo "</tr>";
        }

        // Calcular la sumatoria de precios en el carrito
        $total_price = array_sum(array_column($_SESSION['cart'], 'total'));

        echo "<tr class='total'>";
        echo "<td colspan='3'><strong>Total General</strong></td>";
        echo "<td><strong>{$total_price} USD</strong></td>";
        echo "</tr>";

        echo "</table>";
    } else {
        echo "<p>El carrito está vacío.</p>";
    }
    ?>

    <br>
    <div style='text-align: center;'><a href='index.php'>Volver a la Tienda</a></div>

</body>
</html>
