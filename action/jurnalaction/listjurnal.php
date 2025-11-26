<?php
function getAllJurnal(mysqli $connection, int $userid, string $sortBy = 'judul', string $sortOrder = 'ASC'): array {
    $allJurnal = [];
    $allowedCols = ['idjurnal', 'judul', 'tanggal']; 
    $allowedOrders = ['ASC', 'DESC'];
    $sortBy = in_array($sortBy, $allowedCols) ? $sortBy : 'judul';
    $sortOrder = in_array(strtoupper($sortOrder), $allowedOrders) ? strtoupper($sortOrder) : 'ASC';

    $sql = "SELECT idjurnal, judul, isi, tanggal, dibuat FROM jurnal WHERE id_user = ? ORDER BY `$sortBy` $sortOrder";
    $stmt = mysqli_prepare($connection, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userid);
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
?>