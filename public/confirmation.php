<?php
include '../src/db.php';

// Obtener detalles del evento y la cantidad de tickets
$eventId = $_GET['event_id'];
$quantity = $_GET['quantity'];
$pdfPath = urldecode($_GET['pdf']);

$result = $conn->query("SELECT * FROM eventos WHERE id=$eventId");
$event = $result->fetch_assoc();

if (!$event) {
    die("Evento no encontrado");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Compra</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .event-details {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .pdf-link {
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-4 confirmation-container">
        <h2 class="mb-4">Confirmación de Compra</h2>
        <div class="alert alert-success" role="alert">
            ¡Gracias por tu compra! A continuación, encontrarás el resumen de tu compra.
        </div>
        <div class="event-details">
            <h3 class="mb-3">Detalles del Evento</h3>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($event['nombre']); ?></p>
            <p><strong>Fecha y Hora:</strong> <?php echo htmlspecialchars($event['fecha_hora']); ?></p>
            <p><strong>Lugar:</strong> <?php echo htmlspecialchars($event['lugar']); ?></p>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($event['descripcion']); ?></p>
            <p><strong>Precio por Ticket:</strong> <?php echo number_format($event['precio'], 2); ?> USD</p>
            <p><strong>Cantidad de Tickets:</strong> <?php echo htmlspecialchars($quantity); ?></p>
            <p><strong>Total:</strong> <?php echo number_format($event['precio'] * $quantity, 2); ?> USD</p>
        </div>
        <div class="text-center">
            <a href="<?php echo htmlspecialchars($pdfPath); ?>" class="btn btn-success btn-lg pdf-link">Descargar Ticket en PDF</a>
        </div>
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary btn-lg">Volver a la Página de Eventos</a>
        </div>
    </div>

    <!-- JavaScript y Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
