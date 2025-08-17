<?php
require_once('../tcpdf/tcpdf.php');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'votesystem');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Get election title
$title = 'Election Result';
if (file_exists('config.ini')) {
    $parse = @parse_ini_file('config.ini', false, INI_SCANNER_RAW);
    if ($parse && isset($parse['election_title'])) {
        $title = $parse['election_title'];
    }
}

// Build results table
$content = '<h2 align="center">' . htmlspecialchars($title) . '</h2>';
$content .= '<h4 align="center">Tally Result</h4>';
$content .= '<table border="1" cellspacing="0" cellpadding="3">';

$sql = "SELECT * FROM positions ORDER BY priority ASC";
$query = $conn->query($sql);
while ($row = $query->fetch_assoc()) {
    $content .= '<tr><td colspan="2" align="center" style="font-size:15px;"><b>' . htmlspecialchars($row['description']) . '</b></td></tr>';
    $content .= '<tr><td width="80%"><b>Candidates</b></td><td width="20%"><b>Votes</b></td></tr>';
    $csql = "SELECT * FROM candidates WHERE position_id = '" . $row['id'] . "' ORDER BY lastname ASC";
    $cquery = $conn->query($csql);
    while ($crow = $cquery->fetch_assoc()) {
        $vsql = "SELECT COUNT(*) AS votes FROM votes WHERE candidate_id = '" . $crow['id'] . "'";
        $vquery = $conn->query($vsql);
        $votes = $vquery->fetch_assoc()['votes'];
        $content .= '<tr><td>' . htmlspecialchars($crow['lastname']) . ', ' . htmlspecialchars($crow['firstname']) . '</td><td>' . $votes . '</td></tr>';
    }
}
$content .= '</table>';

// Generate PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 11);
$pdf->writeHTML($content);
$pdf->Output('election_result.pdf', 'I');
?>