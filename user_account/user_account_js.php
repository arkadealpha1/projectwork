<?php
session_start();
include '../connect.php'; // Ensure this file connects to the database

$user_id = $_SESSION['user_id']; // Ensure the user is logged in

$sql = "SELECT post_id, title, media FROM post WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(["posts" => $posts]);
?>
