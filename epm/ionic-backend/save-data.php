<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    $response = ['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error];
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'testConnection') {
        testDatabaseConnection();
    } elseif ($_GET['action'] === 'getUsers') {
        getUsers();
    } elseif ($_GET['action'] === 'deleteUser' && isset($_GET['id'])) {
        deleteUser($_GET['id']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    if ($_GET['action'] === 'updateUser') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['id'], $data['data'])) {
            $userId = $data['id'];
            $newData = $data['data'];
            updateUser($userId, $newData);
        } else {
            $response = ['success' => false, 'message' => 'Datos incompletos para actualizar usuario'];
            echo json_encode($response);
        }
    }
}
function getUsers() {
    global $conn;

    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);

    if ($result === false) {
        $response = ['success' => false, 'message' => 'Error al obtener usuarios: ' . $conn->error];
        echo json_encode($response);
        exit;
    }

    if ($result->num_rows > 0) {
        $users = array();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $response = ['success' => true, 'users' => $users];
    } else {
        $response = ['success' => false, 'message' => 'No existen usuarios en la base de datos.'];
    }

    echo json_encode($response);
}

function deleteUser($id) {
    global $conn;


    $sqlCheckUser = "SELECT nombre FROM users WHERE id = ?";
    $stmtCheckUser = $conn->prepare($sqlCheckUser);

    if ($stmtCheckUser === false) {
        $response = ['success' => false, 'message' => 'Error en la preparación de la consulta (verificación): ' . $conn->error];
        echo json_encode($response);
        exit;
    }


    $stmtCheckUser->bind_param('i', $id);


    $stmtCheckUser->execute();
    $resultCheckUser = $stmtCheckUser->get_result();

    if ($resultCheckUser === false) {
        $response = ['success' => false, 'message' => 'Error al verificar el usuario: ' . $stmtCheckUser->error];
        echo json_encode($response);
        exit;
    }


    if ($resultCheckUser->num_rows == 0) {
        $response = ['success' => false, 'message' => 'Usuario no encontrado'];
        echo json_encode($response);
        exit;
    }

    $stmtCheckUser->close();


    $sqlDeleteUser = "DELETE FROM users WHERE id = ?";
    $stmtDeleteUser = $conn->prepare($sqlDeleteUser);

    if ($stmtDeleteUser === false) {
        $response = ['success' => false, 'message' => 'Error en la preparación de la consulta de eliminación: ' . $conn->error];
        echo json_encode($response);
        exit;
    }


    $stmtDeleteUser->bind_param('i', $id);


    if ($stmtDeleteUser->execute()) {
        $response = ['success' => true, 'message' => 'Usuario eliminado con éxito'];
        echo json_encode($response);
    } else {
        $response = ['success' => false, 'message' => 'Error al eliminar usuario: ' . $stmtDeleteUser->error];
        echo json_encode($response);
    }


    $stmtDeleteUser->close();
}

function updateUser($id, $newData) {
    global $conn;

    $sqlCheckUser = "SELECT nombre FROM users WHERE id = ?";
    $stmtCheckUser = $conn->prepare($sqlCheckUser);

    if ($stmtCheckUser === false) {
        $response = ['success' => false, 'message' => 'Error en la preparación de la consulta (verificación): ' . $conn->error];
        echo json_encode($response);
        exit;
    }

    $stmtCheckUser->bind_param('i', $id);

    $stmtCheckUser->execute();
    $resultCheckUser = $stmtCheckUser->get_result();

    if ($resultCheckUser === false) {
        $response = ['success' => false, 'message' => 'Error al verificar el usuario: ' . $stmtCheckUser->error];
        echo json_encode($response);
        exit;
    }

    if ($resultCheckUser->num_rows == 0) {
        $response = ['success' => false, 'message' => 'Usuario no encontrado'];
        echo json_encode($response);
        exit;
    }

    $stmtCheckUser->close();


    $sqlUpdateUser = "UPDATE users SET nombre=?, apellido=?, direccion=?, numTarjCred=?, banco=?, fechaVencimiento=?, codCvv=? WHERE id=?";
    $stmtUpdateUser = $conn->prepare($sqlUpdateUser);

    if ($stmtUpdateUser === false) {
        $response = ['success' => false, 'message' => 'Error en la preparación de la consulta de actualización: ' . $conn->error];
        echo json_encode($response);
        exit;
    }

    $stmtUpdateUser->bind_param('ssssssii', $newData['nombre'], $newData['apellido'], $newData['direccion'], $newData['numTarjCred'], $newData['banco'], $newData['fechaVencimiento'], $newData['codCvv'], $id);

    if ($stmtUpdateUser->execute()) {
        $response = ['success' => true, 'message' => 'Usuario actualizado con éxito'];
    } else {
        $response = ['success' => false, 'message' => 'Error al actualizar usuario: ' . $stmtUpdateUser->error];
    }

    echo json_encode($response);

    $stmtUpdateUser->close();
}




?>
