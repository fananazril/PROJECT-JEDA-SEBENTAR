<?php
function cariJurnalByJudul(mysqli $connection, string $keyword, int $userid, string $sortBy = 'dibuat', string $sortOrder = 'DESC', int $limit = 6, int $offset = 0): array {
    $hasilCari = [];
    $allowedCols = ['idjurnal', 'judul', 'tanggal', 'dibuat'];
    $allowedOrders = ['ASC', 'DESC'];
    $sortBy = in_array($sortBy, $allowedCols) ? $sortBy : 'dibuat';
    $sortOrder = in_array(strtoupper($sortOrder), $allowedOrders) ? strtoupper($sortOrder) : 'DESC';

    $searchTerm = "%{$keyword}%";
    
    $sql = "SELECT idjurnal, judul, isi, tanggal, dibuat, diupdate FROM jurnal WHERE id_user = ? AND judul LIKE ? ORDER BY `$sortBy` $sortOrder LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($connection, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isii", $userid, $searchTerm, $limit, $offset);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($result && mysqli_num_rows($result) > 0) {
                $hasilCari = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
            if ($result) mysqli_free_result($result);
        } else {
            error_log("Error execute cariJurnalByJudul statement: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Error prepare cariJurnalByJudul statement: " . mysqli_error($connection));
    }

    return $hasilCari;
}

function getTotalCariJurnal(mysqli $connection, string $keyword, int $userid): int {
    $total = 0;
    $searchTerm = "%{$keyword}%";
    $sql = "SELECT COUNT(*) as total FROM jurnal WHERE id_user = ? AND judul LIKE ?";
    $stmt = mysqli_prepare($connection, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "is", $userid, $searchTerm);
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