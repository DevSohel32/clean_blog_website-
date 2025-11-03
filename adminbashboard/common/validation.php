<?php
function Validation($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data; // ✅ Return the cleaned value
}
?>
