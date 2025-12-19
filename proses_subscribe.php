<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

include 'admin/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email tidak boleh kosong']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
    exit;
}

$email_escaped = mysqli_real_escape_string($koneksi, $email);
$check_email = mysqli_query($koneksi, "SELECT id FROM subscribers WHERE email='$email_escaped'");

if (mysqli_num_rows($check_email) > 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email ini sudah terdaftar di newsletter kami. Silakan gunakan email lain.'
    ]);
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'urbanhype.neoverse.my.id';
    $mail->SMTPAuth = true;
    $mail->Username = 'mailreset@urbanhype.neoverse.my.id';
    $mail->Password = 'administrator-online-store';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom('mailreset@urbanhype.neoverse.my.id', 'URBANHYPE Newsletter');
    $mail->addAddress($email);
    $mail->addReplyTo('mailreset@urbanhype.neoverse.my.id', 'URBANHYPE Support');

    $mail->isHTML(true);
    $mail->Subject = 'Selamat Datang di URBANHYPE Newsletter!';
    $mail->CharSet = 'UTF-8';

    $htmlBody = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            
            body {
                font-family: 'Arial', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                line-height: 1.6;
                color: #333;
                background-color: #f5f5f5;
            }
            
            .container {
                max-width: 600px;
                margin: 0 auto;
                background: white;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            }
            
            .header {
                background: linear-gradient(135deg, #1E5DAC 0%, #1a4d8f 100%);
                color: white;
                padding: 40px 30px;
                text-align: center;
            }
            
            .header h1 {
                font-size: 28px;
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            
            .header p {
                font-size: 14px;
                opacity: 0.9;
            }
            
            .content {
                padding: 40px 30px;
            }
            
            .content h2 {
                color: #1E5DAC;
                font-size: 20px;
                margin-bottom: 20px;
            }
            
            .content p {
                margin-bottom: 15px;
                line-height: 1.8;
            }
            
            .benefits {
                background: rgba(30, 93, 172, 0.08);
                padding: 25px;
                border-radius: 8px;
                margin: 25px 0;
                border-left: 4px solid #1E5DAC;
            }
            
            .benefits h3 {
                color: #1E5DAC;
                font-size: 16px;
                margin-bottom: 15px;
            }
            
            .benefits ul {
                list-style: none;
            }
            
            .benefits li {
                padding: 8px 0;
                padding-left: 25px;
                position: relative;
            }
            
            .benefits li svg {
                position: absolute;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                width: 16px;
                height: 16px;
                fill: #10b981;
            }
            
            .cta-button {
                display: inline-block;
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white;
                padding: 14px 40px;
                text-decoration: none;
                border-radius: 6px;
                font-weight: bold;
                margin: 25px 0;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
            }
            
            .divider {
                height: 1px;
                background: #eee;
                margin: 30px 0;
            }
            
            .footer {
                background: #f9f9f9;
                padding: 30px;
                text-align: center;
                border-top: 1px solid #eee;
            }
            
            .footer p {
                font-size: 13px;
                color: #666;
                margin-bottom: 10px;
            }
            
            .footer a {
                color: #1E5DAC;
                text-decoration: none;
            }
            
            .footer-note {
                font-size: 11px;
                color: #999;
                margin-top: 20px;
                border-top: 1px solid #eee;
                padding-top: 20px;
            }
            
            .icon-inline { vertical-align: -0.15em; margin-right: 4px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>
                    <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' viewBox='0 0 16 16' class='icon-inline'>
                        <path d='M13.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-2.135l.35 1.414a.5.5 0 0 1-.97.207L9 2.914V4.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5V2.914l-2.293 1.707a.5.5 0 0 1-.97-.207L3.635 2.5H1.5a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h12zm-1.5 3.5h-9l1.414 1.414a.5.5 0 0 1-.707.707L2 5.414V13.5a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5V5.414l-1.707 1.207a.5.5 0 0 1-.707-.707L12 4.5z'/>
                    </svg>
                    Selamat Datang!
                </h1>
                <p>Terima kasih telah mempercayai kami</p>
            </div>
            
            <div class='content'>
                <h2>Halo,</h2>
                
                <p>Kami dengan senang hati menerima subscription Anda ke newsletter <strong>URBANHYPE</strong>!</p>
                
                <p>Email <strong>" . htmlspecialchars($email) . "</strong> telah terdaftar dan aktif di database kami.</p>
                
                <div class='benefits'>
                    <h3>Keuntungan Sebagai Subscriber:</h3>
                    <ul>
                        <li><svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor' viewBox='0 0 16 16'><path d='M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z'/></svg> Diskon eksklusif hingga 50% untuk produk pilihan</li>
                        <li><svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor' viewBox='0 0 16 16'><path d='M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 14.5V7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038c-.038-.224-.038-.424-.038-.494V2.5zm1.068.5H7v-.5a1.5 1.5 0 1 0-3 0v.5h2.932zM4 4.5H2.038c-.038.224-.038.424-.038.494V7h1.999zm.508 1.226c.201-.028.423-.043.69-.044h6.086c.266.001.488.016.69.044H14v2H2v-2h1.508zm0 2.58h11v4.5h-11V8.306zM3 12a1 1 0 0 0-1 1v1.5a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5V13a1 1 0 0 0-1-1z'/></svg> Akses pertama ke koleksi terbaru dan limited edition</li>
                        <li><svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor' viewBox='0 0 16 16'><path d='M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 14.5V7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038c-.038-.224-.038-.424-.038-.494V2.5zm1.068.5H7v-.5a1.5 1.5 0 1 0-3 0v.5h2.932zM4 4.5H2.038c-.038.224-.038.424-.038.494V7h1.999zm.508 1.226c.201-.028.423-.043.69-.044h6.086c.266.001.488.016.69.044H14v2H2v-2h1.508zm0 2.58h11v4.5h-11V8.306zM3 12a1 1 0 0 0-1 1v1.5a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5V13a1 1 0 0 0-1-1z'/></svg> Poin reward untuk setiap pembelian</li>
                        <li><svg xmlns='http://www.w3.org/2000/svg' width='13' height='13' fill='currentColor' viewBox='0 0 16 16'><path d='M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z'/></svg> Notifikasi flash sale dan penawaran spesial</li>
                        <li><svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor' viewBox='0 0 16 16'><path d='M5.991 3.75a1.5 1.5 0 0 0-2.982 0l-.423 4.758H1.5a.5.5 0 0 0 0 1h1.072l-.423 4.758a1.5 1.5 0 1 0 2.982 0L5.5 9h5.019l.423 4.758a1.5 1.5 0 1 0 2.982 0L13.5 9h1.072a.5.5 0 0 0 0-1h-1.072l-.423-4.758a1.5 1.5 0 0 0-2.982 0L10.519 8H5.5l-.423-4.758z'/></svg> Member VIP dengan benefit tambahan</li>
                        <li><svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor' viewBox='0 0 16 16'><path d='M1.75 1.062a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-8zM9.5 3a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm-7-1a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2.5zm2.5.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z'/></svg> Event eksklusif dan undian berhadiah</li>
                    </ul>
                </div>
                
                <p>Mulai sekarang, Anda akan mendapatkan akses eksklusif ke penawaran terbaik kami. Jangan lewatkan kesempatan emas untuk mendapatkan fashion terbaik dengan harga istimewa!</p>
                
                <center>
                    <a href='https://urbanhype.neoverse.my.id/shop.php' class='cta-button'>
                        Mulai Belanja Sekarang
                        <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor' viewBox='0 0 16 16'>
                            <path fill-rule='evenodd' d='M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z'/>
                        </svg>
                    </a>
                </center>
                
                <div class='divider'></div>
                
                <p>Jika Anda memiliki pertanyaan atau butuh bantuan, tim customer service kami siap membantu Anda 24/7.</p>
                
                <p style='margin-top: 25px;'>
                    Salam hangat,<br>
                    <strong>Tim URBANHYPE</strong><br>
                    <em>Your Fashion, Your Style</em>
                </p>
            </div>
            
            <div class='footer'>
                <p>
                    <a href='https://urbanhype.neoverse.my.id'>Website</a> 
                </p>
                
                <p class='footer-note'>
                    © 2025 UrbanHype. Semua hak dilindungi.<br>
                    Anda menerima email ini karena telah subscribe ke newsletter kami.
                </p>
            </div>
        </div>
    </body>
    </html>
    ";

    $mail->Body = $htmlBody;
    $mail->AltBody = strip_tags($htmlBody);

    if ($mail->send()) {
        $subscribe_date = date('Y-m-d H:i:s');
        $query = "INSERT INTO subscribers (email, subscribed_at) VALUES ('$email_escaped', '$subscribe_date')";

        if (mysqli_query($koneksi, $query)) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Email konfirmasi berhasil dikirim! Cek inbox Anda.'
            ]);
        } else {
            error_log("Database Error: " . mysqli_error($koneksi));
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Email konfirmasi berhasil dikirim! Cek inbox Anda.'
            ]);
        }
    } else {
        throw new Exception('Email gagal dikirim: ' . $mail->ErrorInfo);
    }
} catch (Exception $e) {
    error_log("PHPMailer Error pada " . date('Y-m-d H:i:s') . ": " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}

exit;