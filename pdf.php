<?php
require __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

// -----------------------
// Example user data
// Replace this with your DB fetch (PDO or mysqli) and fill $user accordingly.
// If the photo is stored as binary in DB, uncomment the base64 block below.
// -----------------------
$user = [
    'full_name' => 'Jane Doe',
    'email' => 'jane.doe@example.com',
    'phone' => '+1 555 123 4567',
    'address' => "123 Main St\nCity, State 12345",
    // Option A: photo on filesystem (relative or absolute path)
    'photo_path' => __DIR__ . '/uploads/jane.jpg',
    // Option B: photo binary from DB (uncomment if using)
    // 'photo_blob' => $binaryFromDb,
    'notes' => 'Preferred contact time: mornings. Member since 2020.'
];

// -----------------------
// Assemble image src (works for file or DB blob)
// mPDF supports data URLs (base64) so both methods are covered.
// -----------------------
$imgSrc = '';
if (!empty($user['photo_blob'])) {
    // If you store binary in DB:
    $base64 = base64_encode($user['photo_blob']);
    // try detect type or use known type (image/jpeg)
    $imgSrc = 'data:image/jpeg;base64,' . $base64;
} elseif (!empty($user['photo_path']) && file_exists($user['photo_path'])) {
    // Use filesystem file path. mPDF allows absolute paths.
    $imgSrc = $user['photo_path'];
} else {
    // fallback placeholder (URL or local placeholder)
    $imgSrc = 'https://via.placeholder.com/150x200?text=No+Photo';
}

// -----------------------
// Create mPDF with A4 page. Units are mm internally.
// Format 'A4' guarantees 210 x 297 mm page size.
// You can adjust margins as needed.
// -----------------------
$mpdf = new Mpdf([
    'format' => 'A4',
    'orientation' => 'P',
    'margin_left' => 12,
    'margin_right' => 12,
    'margin_top' => 12,
    'margin_bottom' => 12,
]);

// Disable default header/footer if any
$mpdf->SetHeader('');
$mpdf->SetFooter('');

// To improve single-page fit you can tweak this:
$mpdf->SetAutoPageBreak(true, 12); // reserve 12mm bottom margin

// -----------------------
// HTML template (inline CSS). Keep content compact to ensure it's one page.
// Make sure images are sized to fit. Use max-width or explicit sizes.
// -----------------------
$html = '
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 12pt; color: #222; }
    .container { display: block; }
    .header { display: flex; align-items: center; margin-bottom: 10px; }
    .photo {
        width: 90px; /* control size so it fits on page */
        height: 120px;
        object-fit: cover;
        border: 1px solid #ccc;
        margin-left: 12px;
    }
    .title { font-size: 16pt; font-weight: bold; }
    .details { margin-top: 8px; }
    .label { font-weight: bold; width: 100px; display:inline-block; vertical-align:top; }
    .value { display:inline-block; max-width: 360px; }
    .notes { margin-top: 12px; font-size: 11pt; }
    /* prevent page breaks inside the main container to keep single-page layout */
    .no-break { page-break-inside: avoid; }
</style>
</head>
<body>
<div class="container no-break">
    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
        <div>
            <div class="title">' . htmlspecialchars($user['full_name']) . '</div>
            <div style="color:#666; margin-top:4px">User Profile</div>
        </div>
        <div>
            <img src="' . htmlspecialchars($imgSrc) . '" class="photo" alt="User photo" />
        </div>
    </div>

    <div class="details" style="margin-top:12px;">
        <div><span class="label">Email:</span> <span class="value">' . htmlspecialchars($user['email']) . '</span></div>
        <div><span class="label">Phone:</span> <span class="value">' . htmlspecialchars($user['phone']) . '</span></div>
        <div><span class="label">Address:</span> <span class="value">' . nl2br(htmlspecialchars($user['address'])) . '</span></div>
    </div>

    <div class="notes">
        <div class="label" style="font-weight:bold;">Notes:</div>
        <div style="margin-top:6px;">' . nl2br(htmlspecialchars($user['notes'])) . '</div>
    </div>
</div>
</body>
</html>
';

// -----------------------
// Write HTML to the PDF
// -----------------------
$mpdf->WriteHTML($html);

// -----------------------
// Output options:
// 1) Save to server file:
// $filename = __DIR__ . '/output/user_' . preg_replace('/[^a-z0-9]+/i','_', strtolower($user['full_name'])) . '.pdf';
// $mpdf->Output($filename, \Mpdf\Output\Destination::FILE);

// 2) Force download to browser:
// $mpdf->Output('user-profile.pdf', \Mpdf\Output\Destination::DOWNLOAD);

// 3) Inline open in browser (not a web preview — it opens the PDF file in the browser PDF viewer):
// $mpdf->Output('user-profile.pdf', \Mpdf\Output\Destination::INLINE);

// Example: save to file and also send as download. Uncomment one as needed:
$filename = __DIR__ . '/output/user_profile.pdf';
if (!is_dir(__DIR__ . '/output')) {
    mkdir(__DIR__ . '/output', 0755, true);
}
$mpdf->Output($filename, \Mpdf\Output\Destination::FILE);

// You can then send it to the client for download:
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="user_profile.pdf"');
readfile($filename);
exit;
?>