<?php
function getAllJurnal(mysqli $connection, int $userid, string $sortBy = 'dibuat', string $sortOrder = 'DESC', int $limit = 6, int $offset = 0): array {
    $allJurnal = [];
    $allowedCols = ['idjurnal', 'judul', 'tanggal', 'dibuat']; 
    $allowedOrders = ['ASC', 'DESC'];
    $sortBy = in_array($sortBy, $allowedCols) ? $sortBy : 'dibuat';
    $sortOrder = in_array(strtoupper($sortOrder), $allowedOrders) ? strtoupper($sortOrder) : 'DESC';

    $sql = "SELECT idjurnal, judul, isi, tanggal, dibuat, diupdate FROM jurnal WHERE id_user = ? ORDER BY `$sortBy` $sortOrder LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($connection, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iii", $userid, $limit, $offset);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($result && mysqli_num_rows($result) > 0) {
                $allJurnal = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
            if ($result) mysqli_free_result($result);
        } else {
            error_log("Error execute getAllJurnal statement: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Error prepare getAllJurnal statement: " . mysqli_error($connection));
    }

    return $allJurnal;
}

function getTotalJurnal(mysqli $connection, int $userid): int {
    $total = 0;
    $sql = "SELECT COUNT(*) as total FROM jurnal WHERE id_user = ?";
    $stmt = mysqli_prepare($connection, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userid);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($result && $row = mysqli_fetch_assoc($result)) {
                $total = (int)$row['total'];
            }
            if ($result) mysqli_free_result($result);
        }
        mysqli_stmt_close($stmt);
    }
    
    return $total;
}
?>