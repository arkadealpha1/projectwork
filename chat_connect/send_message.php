<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'projectwork';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the message data from the request
$data = json_decode(file_get_contents('php://input'), true);
$sender_id = $data['sender_id'];
$receiver_id = $data['receiver_id'];
$message = $data['message'];
$conversationId=null;

$stmt=$conn->prepare("SELECT connect_id from chat_connections where (user1_id=? AND user2_id=?) OR (user1_id=? AND user2_id=?)");
$stmt->bind_param("iiii",$sender_id,$receiver_id,$receiver_id,$sender_id);
// $stmt->execute( [$sender_id,$receiver_id,$receiver_id,$sender_id]);
// $conversation=$stmt->fetch();
$stmt->execute();
$conversation=$stmt->get_result()->fetch_assoc();
if($conversation){
    $conversationId=$conversation['connect_id'];
    $stmt=$conn->prepare("INSERT INTO chat_messages (connect_id,sender_id,receiver_id,message) VALUES (?,?,?,?)");
    $stmt->execute(params: [$conversationId,$sender_id,$receiver_id,$message]);
    $stmt=$conn->prepare("UPDATE chat_connections SET last_message_at=CURRENT_TIMESTAMP WHERE connect_id=?");
    $stmt->execute([$conversationId]);

}
else{
    $stmt=$conn->prepare("INSERT INTO chat_connections (user1_id,user2_id) VALUES (?,?)");
    $stmt->execute([$sender_id,$receiver_id]);
     $conversationId=$conn->insert_id;

    $stmt=$conn->prepare("INSERT INTO chat_messages (connect_id,sender_id,receiver_id,message) VALUES (?,?,?,?)");
    $stmt->execute([$conversationId,$sender_id,$receiver_id,$message]); 

}
// Insert the message into the database
// $sql = "INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
// $stmt = $conn->prepare($sql);
// $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
// $stmt->execute();

// // Update the chat_connections table
// $sql = "INSERT INTO chat_connections (user1_id, user2_id) VALUES (?, ?) 
//         ON DUPLICATE KEY UPDATE last_message_at = CURRENT_TIMESTAMP";
// $stmt = $conn->prepare($sql);
// $stmt->bind_param("ii", $sender_id, $receiver_id);
// $stmt->execute();

$stmt->close();
$conn->close();

echo json_encode(['status' => 'success']);
?>