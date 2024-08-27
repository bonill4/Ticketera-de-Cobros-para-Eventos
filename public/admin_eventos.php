<?php
include '../src/db.php';
session_start();

// Verificar si el usuario es un administrador
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Mostrar mensaje de éxito/error
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Actualizar evento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $event_id = $_POST['event_id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha_hora = $_POST['fecha_hora'];
    $lugar = $_POST['lugar'];
    $precio = $_POST['precio'];

    $sql = "UPDATE eventos SET nombre=?, descripcion=?, fecha_hora=?, lugar=?, precio=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssdi', $nombre, $descripcion, $fecha_hora, $lugar, $precio, $event_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Evento actualizado exitosamente.";
    } else {
        $_SESSION['message'] = "Error al actualizar el evento.";
    }
    header('Location: admin_eventos.php');
    exit();
}

// Eliminar evento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $event_id = $_POST['event_id'];

    $sql = "DELETE FROM eventos WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $event_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Evento eliminado exitosamente.";
    } else {
        $_SESSION['message'] = "Error al eliminar el evento.";
    }
    header('Location: admin_eventos.php');
    exit();
}

// Crear evento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha_hora = $_POST['fecha_hora'];
    $lugar = $_POST['lugar'];
    $precio = $_POST['precio_evento'];

    $sql = "INSERT INTO eventos (nombre, descripcion, fecha_hora, lugar, precio) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssd', $nombre, $descripcion, $fecha_hora, $lugar, $precio);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Evento creado exitosamente.";
    } else {
        $_SESSION['message'] = "Error al crear el evento.";
    }
    header('Location: admin_eventos.php');
    exit();
}

// Obtener la lista de eventos
$result = $conn->query("SELECT * FROM eventos ORDER BY fecha_hora DESC");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Eventos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-control {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include '../templates/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Administrar Eventos</h2>

        <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Botón para crear evento -->
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createEventModal">
            Crear Evento
        </button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha y Hora</th>
                    <th>Lugar</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($event = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($event['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($event['fecha_hora']); ?></td>
                        <td><?php echo htmlspecialchars($event['lugar']); ?></td>
                        <td><?php echo '$' . number_format($event['precio'], 2); ?></td>
                        <td>
                            <form method="post" class="form-inline">
                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                <button type="button" class="btn btn-primary btn-sm mr-2" onclick="fillForm(<?php echo $event['id']; ?>, '<?php echo htmlspecialchars($event['nombre']); ?>', '<?php echo htmlspecialchars($event['descripcion']); ?>', '<?php echo $event['fecha_hora']; ?>', '<?php echo htmlspecialchars($event['lugar']); ?>', '<?php echo $event['precio']; ?>')">Actualizar</button>
                                <button type="submit" name="delete" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Formulario para actualizar -->
        <div id="updateForm" style="display: none;">
            <h4>Actualizar Evento</h4>
            <form method="post">
                <input type="hidden" id="event_id" name="event_id">
                <div class="form-group">
                    <label for="nombre">Nombre del Evento</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                </div>
                <div class="form-group">
                    <label for="fecha_hora">Fecha y Hora</label>
                    <input type="datetime-local" class="form-control" id="fecha_hora" name="fecha_hora" required>
                </div>
                <div class="form-group">
                    <label for="lugar">Lugar</label>
                    <input type="text" class="form-control" id="lugar" name="lugar" required>
                </div>
                <div class="form-group">
                    <label for="precio_evento">Precio</label>
                    <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                </div>
                <button type="submit" name="update" class="btn btn-success">Actualizar Evento</button>
                <button type="button" class="btn btn-secondary" onclick="clearForm()">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Modal para crear evento -->
    <div class="modal fade" id="createEventModal" tabindex="-1" role="dialog" aria-labelledby="createEventModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEventModalLabel">Crear Evento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="nombre_evento">Nombre del Evento</label>
                            <input type="text" class="form-control" id="nombre_evento" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion_evento">Descripción</label>
                            <input type="text" class="form-control" id="descripcion_evento" name="descripcion" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha_hora_evento">Fecha y Hora</label>
                            <input type="datetime-local" class="form-control" id="fecha_hora_evento" name="fecha_hora" required>
                        </div>
                        <div class="form-group">
                            <label for="lugar_evento">Lugar</label>
                            <input type="text" class="form-control" id="lugar_evento" name="lugar" required>
                        </div>
                        <div class="form-group">
                            <label for="precio_evento">Precio</label>
                            <input type="number" step="0.01" class="form-control" id="precio_evento" name="precio_evento" required>
                        </div>
                        <button type="submit" name="create" class="btn btn-primary">Crear Evento</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir el archivo JavaScript -->
    <script src="../js/form-validation.js"></script>

    <script>
        function fillForm(id, nombre, descripcion, fechaHora, lugar, precio) {
            document.getElementById('event_id').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('descripcion').value = descripcion;
            document.getElementById('fecha_hora').value = fechaHora;
            document.getElementById('lugar').value = lugar;
            document.getElementById('precio').value = precio;
            document.getElementById('updateForm').style.display = 'block';
        }

        function clearForm() {
            document.getElementById('updateForm').style.display = 'none';
            document.getElementById('event_id').value = '';
            document.getElementById('nombre').value = '';
            document.getElementById('descripcion').value = '';
            document.getElementById('fecha_hora').value = '';
            document.getElementById('lugar').value = '';
            document.getElementById('precio').value = '';
        }
    </script>

    <!-- Scripts necesarios para Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
