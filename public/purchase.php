<?php
include '../src/db.php';
require '../libs/fpdf/fpdf.php';

if (isset($_GET['id'])) {
    $eventId = $_GET['id'];
    $result = $conn->query("SELECT * FROM eventos WHERE id=$eventId");
    $event = $result->fetch_assoc();

    if (!$event) {
        die("Evento no encontrado");
    }
} else {
    die("ID del evento no especificado");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evento_id = $_POST['evento_id'];
    $nombre_comprador = $_POST['nombre_comprador'];
    $correo_comprador = $_POST['correo_comprador'];
    $cantidad_tickets = $_POST['cantidad_tickets'];
    $numero_tarjeta = $_POST['tarjeta'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $cvv = $_POST['cvv'];

    if (strlen($numero_tarjeta) === 19 && strlen($cvv) === 3) {
        // Insertar venta en la base de datos
        $sql = "INSERT INTO ventas (evento_id, fecha_venta, nombre_comprador, correo_comprador, cantidad_tickets)
                VALUES (?, NOW(), ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $evento_id, $nombre_comprador, $correo_comprador, $cantidad_tickets);
        $stmt->execute();
        $stmt->close();

        // Generar PDF
        $ticket_id = uniqid('ticket_', true);
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Ticket de Compra', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Nombre del Evento: ' . htmlspecialchars($event['nombre']), 0, 1);
        $pdf->Cell(0, 10, 'Fecha y Hora: ' . htmlspecialchars($event['fecha_hora']), 0, 1);
        $pdf->Cell(0, 10, 'Nombre del Comprador: ' . htmlspecialchars($nombre_comprador), 0, 1);
        $pdf->Cell(0, 10, 'ID del Ticket: ' . $ticket_id, 0, 1);

        $pdf_path = '../uploads/ticket_' . $ticket_id . '.pdf';
        $pdf->Output('F', $pdf_path);

        // Redirigir a confirmation.php con la ruta del archivo PDF
        header("Location: confirmation.php?event_id=$evento_id&quantity=$cantidad_tickets&pdf=" . urlencode($pdf_path));
        exit;

    } else {
        $error = "Número de tarjeta o CVV inválidos. Por favor, inténtelo de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra de Entradas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function updateTotal() {
            const precio = parseFloat(document.getElementById('precio_evento').value);
            const cantidad = parseInt(document.getElementById('cantidad_tickets').value);
            const total = precio * cantidad;
            document.getElementById('total_compra').value = total.toFixed(2);
        }
    </script>
</head>

<body>
    <div class="container mt-4">
        <h2>Compra de Entradas</h2>
        <?php if (isset($event) && $event): ?>
            <form method="post">
                <input type="hidden" name="evento_id" value="<?php echo htmlspecialchars($event['id']); ?>">
                <div class="row">
                    <!-- Columna 1 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_evento">Nombre del Evento</label>
                            <input type="text" class="form-control" id="nombre_evento" name="nombre_evento"
                                value="<?php echo htmlspecialchars($event['nombre']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="descripcion_evento">Descripción</label>
                            <input type="text" class="form-control" id="descripcion_evento" name="descripcion_evento"
                                value="<?php echo htmlspecialchars($event['descripcion']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="fecha_hora_evento">Fecha y Hora</label>
                            <input type="text" class="form-control" id="fecha_hora_evento" name="fecha_hora_evento"
                                value="<?php echo htmlspecialchars($event['fecha_hora']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="lugar_evento">Lugar</label>
                            <input type="text" class="form-control" id="lugar_evento" name="lugar_evento"
                                value="<?php echo htmlspecialchars($event['lugar']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="precio_evento">Precio</label>
                            <input type="number" step="0.01" class="form-control" id="precio_evento" name="precio_evento"
                                value="<?php echo number_format($event['precio'], 2); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="total_compra">Total</label>
                            <input type="text" class="form-control" id="total_compra" name="total_compra" value="0.00"
                                readonly>
                        </div>
                    </div>

                    <!-- Columna 2 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_comprador">Nombre del Comprador</label>
                            <input type="text" class="form-control" id="nombre_comprador" name="nombre_comprador" required>
                        </div>
                        <div class="form-group">
                            <label for="correo_comprador">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo_comprador" name="correo_comprador" required>
                        </div>
                        <div class="form-group">
                            <label for="cantidad_tickets">Cantidad de Tickets</label>
                            <input type="number" class="form-control" id="cantidad_tickets" name="cantidad_tickets" min="1"
                                required onchange="updateTotal()">
                        </div>
                        <div class="form-group">
                            <label for="numero_tarjeta">Número de Tarjeta</label>
                            <input type="text" class="form-control" id="tarjeta" name="tarjeta"
                                placeholder="1234 5678 1234 5678" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha_expiracion">Fecha de Expiración</label>
                            <input type="text" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento"
                                placeholder="MM/YY" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Comprar</button>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">
                No se pudo encontrar la información del evento.
            </div>
        <?php endif; ?>

        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger mt-3">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
    </div>
    </div>

    <!-- Incluir el archivo JavaScript -->
    <script src="../js/form-validation.js"></script>

    <!-- Scripts necesarios para Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>