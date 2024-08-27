<?php
include '../src/db.php';
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Fetch all events
$result = $conn->query("SELECT * FROM eventos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Eventos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function searchEvents() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let rows = document.querySelectorAll('#eventsTable tbody tr');
            rows.forEach(row => {
                let name = row.querySelector('td:first-child').textContent.toLowerCase();
                if (name.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</head>
<body>
    <?php include '../templates/navbar.php'; ?>
<div class="container mt-4">
    <h2>Lista de Eventos</h2>
    <input type="text" class="form-control mb-3" id="searchInput" onkeyup="searchEvents()" placeholder="Buscar eventos...">

    <table class="table table-striped" id="eventsTable">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Fecha y Hora</th>
            <th>Lugar</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($event = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($event['nombre']); ?></td>
                <td><?php echo htmlspecialchars($event['fecha_hora']); ?></td>
                <td><?php echo htmlspecialchars($event['lugar']); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- JavaScript y Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
