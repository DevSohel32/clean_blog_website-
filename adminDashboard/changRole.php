<?php
include '../FontEnd/lib/connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['role_id'])) {
        $id = $_POST['id'];
        $role_id = $_POST['role_id'];

        try {
            $sql = "UPDATE users SET role_id = :role_id, updated_at = NOW() WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Success message + redirect back
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            echo "Error updating role: " . $e->getMessage();
        }
    } else {
        echo "Invalid request";
    }
} else {
    echo "Invalid request method";
}
?>