<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include '../src/db.php'; // Asegúrate de que la conexión a la base de datos esté bien configurada

// Consulta para obtener las ventas realizadas
$sql = "
    SELECT e.nombre AS evento_nombre, v.fecha_venta, v.nombre_comprador, 
           v.cantidad_tickets, (v.cantidad_tickets * e.precio) AS total_recaudado
    FROM ventas v
    JOIN eventos e ON v.evento_id = e.id
    ORDER BY v.fecha_venta DESC
";
$result = $conn->query($sql);

// Verificar si la consulta fue exitosa
if (!$result) {
    die("Error al consultar las ventas: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include '../templates/navbar.php'; ?>
    <div class="container mt-4">
        <h2>Reporte de Ventas</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nombre del Evento</th>
                    <th>Fecha y Hora de la Venta</th>
                    <th>Nombre del Comprador</th>
                    <th>Cantidad de Tickets Vendidos</th>
                    <th>Total Recaudado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['evento_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha_venta']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre_comprador']); ?></td>
                    <td><?php echo htmlspecialchars($row['cantidad_tickets']); ?></td>
                    <td><?php echo number_format($row['total_recaudado'], 2); ?> €</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript y Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
