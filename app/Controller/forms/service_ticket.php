<?php
ob_start();
session_start();
date_default_timezone_set("Asia/Manila");
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

require_once '../../../vendor/autoload.php';
require_once '../../../assets/vendor/tcpdf/tcpdf.php';

use app\Model\Ticket\Ticket;
use app\Model\DbConnection\DbConfig;

// Get ticket ID
$id = $_GET['ticket'] ?? null;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing ticket hash.']);
    exit;
}

// Database connection
$db = new DbConfig();
$conn = $db->db_connection();

// Extend TCPDF
class MYPDF extends TCPDF {
    public function Header() {
        $gmi_logo  = K_PATH_IMAGES . 'Glacier_Logos/gmi.png';
        $gili_logo = K_PATH_IMAGES . 'Glacier_Logos/gili.png';

        $this->Image($gmi_logo, 15, 13, 20.6, 20.6, 'PNG', '', 'T', false, 300);
        $this->Image($gili_logo, 168, 7, 35, 35, 'PNG', '', 'T', false, 300);

        $this->SetY(10);
        $this->SetX(10);
        $this->Cell(30, 25, '', 0, 0, 'C');

        $this->SetFont('helvetica', 'B', 20);
        $this->Cell(125, 25, 'SERVICE TICKET COPY', 0, 0, 'C');

        $this->SetX(10);
        $this->SetDrawColor(0, 102, 204);
        $this->SetLineWidth(0.6);
        $this->Line(0, $this->GetY() + 25, 210, $this->GetY() + 25);
    }

    public function Footer() {
        $this->SetY(-10);
        $this->SetTextColor(255, 0, 0);
        $this->SetFont('helvetica', 'I', 8);
        $dateNow = date("d-M-Y H:i");
        $this->Cell(200, 5, "Printed Date: {$dateNow}", 0, 0, 'L');
        $this->SetX(15);
        $this->Cell(200, 5, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
    }
}

// Hex to RGB converter
function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) == 3) {
        $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
        $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
        $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    return [$r, $g, $b];
}

// Status color mapping
function getStatusColor($status) {
    $status = strtolower(trim($status));
    return match ($status) {
        'assigned' => '#003366',
        'in progress' => '#00ffff',
        'onhold', 'on hold' => '#ff0000',
        'open' => '#0000ff',
        'closed' => '#006400',
        'resolved' => '#00ff99',
        default => '#17a2b8',
    };
}

// Field formatter
function ticketField($pdf, $label, $value = '-') {
    $leftMargin = 15;
    $rightMargin = 195;
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetXY($leftMargin, $pdf->GetY());
    $pdf->Cell(60, 8, $label . ':', 0, 0, 'L');

    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetXY($leftMargin + 60, $pdf->GetY());
    $pdf->Cell($rightMargin - ($leftMargin + 60), 8, $value, 0, 1, 'R');

    // Dotted line separator
    $y = $pdf->GetY();
    $pdf->SetLineStyle(['width' => 0.1, 'dash' => '1,1', 'color' => [180, 180, 180]]);
    $pdf->Line($leftMargin, $y, $rightMargin, $y);
    $pdf->Ln(2);
}

// Create PDF instance
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('Service Portal');
$pdf->SetAuthor('IT Service Desk');
$pdf->SetTitle('Service Ticket Copy');
$pdf->SetSubject('Service Ticket');
$pdf->SetKeywords('TCPDF, PDF, service, ticket');

// Font & Margin settings
$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT); // Increased top margin
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Language
if (file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// Add page
$pdf->AddPage('P', 'A4');
$pdf->SetY(40); // Push below header

// Fetch ticket data
$ticket = new Ticket(['id' => $id]);
$result = $ticket->ViewAllTicketDetails($id);
$data = $result['data'][0] ?? null;

if (!$data) {
    $pdf->Write(0, 'No data found for this ticket.');
    $pdf->Output('service_ticket.pdf', 'I');
    exit;
}

// Draw rounded status badge
$statusColorHex = getStatusColor($data['status_id'] ?? '');
list($r, $g, $b) = hexToRgb($statusColorHex);
$brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
$textColor = ($brightness > 128) ? [0, 0, 0] : [255, 255, 255];

$badgeWidth = 60;
$badgeHeight = 12;
$badgeX = ($pdf->getPageWidth() - $badgeWidth) / 2;
$badgeY = $pdf->GetY();

$pdf->SetFillColor($r, $g, $b);
$pdf->SetTextColor($textColor[0], $textColor[1], $textColor[2]);
$pdf->RoundedRect($badgeX, $badgeY, $badgeWidth, $badgeHeight, 3, '1111', 'F');
$pdf->SetXY($badgeX, $badgeY);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell($badgeWidth, $badgeHeight, strtoupper($data['status_id'] ?? 'STATUS'), 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(10);

$ticketType = $data['ticket_type'] ?? '-';
$ticketTypeFormatted = ucfirst(strtolower($ticketType));

// Output ticket fields
ticketField($pdf, 'Ticket Control Number', $data['ticket_control_number'] ?? '-');
ticketField($pdf, 'Ticket Type', $ticketTypeFormatted);
ticketField($pdf, 'User Full Name', $data['user_fullname'] ?? '-');
ticketField($pdf, 'Warehouse / SBU', $data['warehouse_name'] ?? '-');
ticketField($pdf, 'Category', $data['category'] ?? '-');
ticketField($pdf, 'User Email', $data['email'] ?? '-');
ticketField($pdf, 'User Contact No.', $data['contact_no'] ?? '-');
ticketField($pdf, 'Title / Subject', $data['title'] ?? '-');
ticketField($pdf, 'Description', $data['description'] ?? '-');
ticketField($pdf, 'Attachment', $data['attachments'] ?? '-');
ticketField($pdf, 'Remarks', $data['remarks'] == "" ? '-' : $data['remarks']);
ticketField($pdf, 'Technician', $data['technician'] == '' ? 'No assigned Technician' : $data['technician']);
ticketField($pdf, 'Resolution', $data['resolution'] == '' ? 'No resolution yet' : $data['resolution']);

// Output PDF
ob_end_clean();
$pdf->Output('service_ticket.pdf', 'I');
?>
