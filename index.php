<?php 
require "vendor/autoload.php";
use Ramsey\Uuid\Uuid;

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once ("db.php");

$method = $_SERVER['REQUEST_METHOD'];
$notes = load_notes();

function clean_notes($title, $content){
    if ($title === "" || $content === "") {
        http_response_code(400);
        echo json_encode(["message" => "Title and Content cannot be empty"]);
        exit;
    }

    if (strlen($title) < 3 || strlen($content) < 3) {
        http_response_code(400);
        echo json_encode(["message" => "Title and Content must be at least 3 characters long"]);
        exit;
    }

    if (strlen($title) > 20 || strlen($content) > 500) {
        http_response_code(400);
        echo json_encode(["message" => "Title cannot exceed 20 characters and Content cannot exceed 500 characters"]);
        exit;
    }
}
if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

switch ($method) {
    case "GET":
        if (isset($_GET["route"]) && $_GET["route"] === "notes") {
            http_response_code(200);
            echo json_encode($notes);
            break;
        }
        if (isset($_GET["route"]) && $_GET["route"] === "stats"){
            if (empty($notes)){
                http_response_code(404);
                echo json_encode([
                    "message" => "No notes found",
                    "total_notes" => 0,
                    "oldest_note" => null,
                    "newest_note" => null,
                    "average_title_length" => 0,
                    "average_content_length" => 0
                ]);
                break;
            }
        }
        
        $total_notes = count($notes);

        $oldest_data = min(array_column($notes, 'created_at'));
        $newest_data = max(array_column($notes, 'created_at'));

        $average_title_length = round(
        array_sum(array_map('strlen', array_column($notes, 'title'))) / $total_notes,
        2
        );
        $average_content_length = round(
            array_sum(array_map('strlen', array_column($notes, 'content'))) / $total_notes,
            2
        );

        $oldest_note = null;
        $newest_note = null;

        foreach($notes as $note){
            if($note['created_at'] === $oldest_data){
                $oldest_note = $note;
            }
            if($note['created_at'] === $newest_data){
                $newest_note = $note;
            }
        }
        http_response_code(200);
        echo json_encode([
            "total_notes" => $total_notes,
            "oldest_note" => $oldest_note,
            "newest_note" => $newest_note,
            "average_title_length" => $average_title_length,
            "average_content_length" => $average_content_length
        ]);
        break;

        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            if (isset($notes[$id])) {
                http_response_code(200);
                echo json_encode($notes[$id]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Note not found"]);
            } 
        }
        else{
        header("Content-Type: text/html");
        echo file_get_contents("index.html");
        }
        break;
        
    case "POST":
        $uni_id = Uuid::uuid4()->toString();
        $data = json_decode(file_get_contents("php://input"), true);
        $content_type = $_SERVER['CONTENT_TYPE']??'';
        if (stripos($content_type, 'application/json') !== 0){
            http_response_code(415);
            echo json_encode(["message" => "Content-Type must be application/json"]);
            exit;
        }
        if ($data === null){
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON data"]);
            exit;
        }

        $title = trim($data["title"] ?? "");
        $content = trim($data["content"] ?? "");

        clean_notes($title, $content);
        
        $notes[$uni_id] = [
            "id" => $uni_id,
            "title" => $title,
            "content" => $content,
            "created_at" => date("Y-m-d H:i:s")
        ];

        $saved = save_notes($notes);
        if ($saved == true) {
            http_response_code(200);
            echo json_encode([
                "message" => "Note created",
                "note" => $notes[$uni_id]
            ]);
        }
        else{
            http_response_code(500);
            echo json_encode(["message" => "Failed to save note"]);
        }
        break;

    case "PUT":
        $data = json_decode(file_get_contents("php://input"), true);
        $content_type = $_SERVER['CONTENT_TYPE']??'';
        if (stripos($content_type, 'application/json') !== 0){
            http_response_code(415);
            echo json_encode(["message" => "Content-Type must be application/json"]);
            exit;
        }
        if (!isset($_GET["id"])) {
            echo json_encode(["message" => "Note ID is required"]);
            break;
        }
        $id = $_GET["id"];
        if (!isset($notes[$id])) {
            http_response_code(404);
            echo json_encode(["message" => "Note not found"]);
            break;
        }

        $title = trim($data["title"] ??"");
        $content = trim($data["content"] ??"");

        clean_notes($title, $content);

        $notes[$id]["title"] = $title;
        $notes[$id]["content"] = $content;
        $notes[$id]["updated_at"] = date("Y-m-d H:i:s");
        
        $saved = save_notes($notes);
        if ($saved == true) {
            http_response_code(200);
            echo json_encode(["message" => "Note updated"]);   
        }
        else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to update note"]);
        }
        break;

    case "DELETE":
        if (!isset($_GET["id"])) {
            http_response_code(404);
            echo json_encode(["message" => "Note ID is required"]);
            break;
        }
        $id = $_GET["id"];
        if (!isset($notes[$id])) {
            http_response_code(404);
            echo json_encode(["message" => "Note not found"]);
            break;
        }
        unset($notes[$id]);
        $saved = save_notes($notes);
        if ($saved == true) {
            http_response_code(200);
            echo json_encode(["message" => "Note deleted"]);
        }
        else{
            http_response_code(500);
            echo json_encode(["message" => "Failed to delete note"]);
        }
        break;
}
?>