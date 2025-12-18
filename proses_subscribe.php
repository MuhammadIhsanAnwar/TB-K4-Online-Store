<?php
header('Content-Type: application/json');

// ============================================
// IMPORT PHPMAILER
// ============================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Validasi request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// Ambil email dari request
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Validasi email tidak boleh kosong
if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email tidak boleh kosong']);
    exit;
}

// Validasi format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
    exit;
}

// ============================================
// KONFIGURASI PHPMAILER
// ============================================

$mail = new PHPMailer(true);

try {
    // ============ SERVER SETTINGS ============
    $mail->isSMTP();
    $mail->Host = 'urbanhype.neoverse.my.id';
    $mail->SMTPAuth = true;
    $mail->Username = 'newslater@urbanhype.neoverse.my.id';
    $mail->Password = 'administrator-online-store';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 587;
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'error_log';

    // ============ RECIPIENTS ============
    $mail->setFrom('newslater@urbanhype.neoverse.my.id', 'URBANHYPE');     
    $mail->addAddress($email);                                 
    $mail->addReplyTo('newslater@urbanhype.neoverse.my.id', 'URBANHYPE Support');  

    // ============ EMAIL CONTENT ============
    $mail->isHTML(true);
    $mail->Subject = 'Terima Kasih Telah Subscribe URBANHYPE! 🎉';
    $mail->CharSet = 'UTF-8';

    // HTML Body Email
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
            
            .benefits li:before {
                content: '✓';
                position: absolute;
                left: 0;
                color: #10b981;
                font-weight: bold;
                font-size: 18px;
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
                transition: all 0.3s ease;
            }
            
            .cta-button:hover {
                background: linear-gradient(135deg, #059669 0%, #047857 100%);
                transform: translateY(-2px);
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
            
            .social-links {
                margin-top: 15px;
            }
            
            .social-links a {
                display: inline-block;
                margin: 0 10px;
                width: 35px;
                height: 35px;
                background: #1E5DAC;
                color: white;
                border-radius: 50%;
                text-decoration: none;
                text-align: center;
                line-height: 35px;
                font-size: 18px;
            }
            
            .footer-note {
                font-size: 11px !important;
                color: #999 !important;
                margin-top: 20px;
                border-top: 1px solid #eee;
                padding-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <!-- HEADER -->
            <div class='header'>
                <h1>🎉 Selamat Datang!</h1>
                <p>Terima kasih telah mempercayai kami</p>
            </div>
            
            <!-- CONTENT -->
            <div class='content'>
                <h2>Halo,</h2>
                
                <p>Kami dengan senang hati menerima subscription Anda ke newsletter <strong>URBANHYPE</strong>! Email <strong>" . htmlspecialchars($email) . "</strong> telah terdaftar dan aktif.</p>
                
                <p>Mulai sekarang, Anda akan mendapatkan akses eksklusif ke penawaran terbaik kami.</p>
                
                <div class='benefits'>
                    <h3>📋 Keuntungan Sebagai Subscriber:</h3>
                    <ul>
                        <li>✨ Diskon eksklusif hingga 50% untuk produk pilihan</li>
                        <li>🆕 Akses pertama ke koleksi terbaru dan limited edition</li>
                        <li>🎁 Poin reward untuk setiap pembelian</li>
                        <li>🔔 Notifikasi flash sale dan penawaran spesial</li>
                        <li>👑 Member VIP dengan benefit tambahan</li>
                        <li>📢 Event eksklusif dan undian berhadiah</li>
                    </ul>
                </div>
                
                <p>Jangan lewatkan kesempatan emas untuk mendapatkan fashion terbaik dengan harga istimewa. Koleksi terbaru kami sudah menunggu!</p>
                
                <center>
                    <a href='https://urbanhype.com/shop.php' class='cta-button'>Mulai Belanja Sekarang →</a>
                </center>
                
                <div class='divider'></div>
                
                <p>Jika Anda memiliki pertanyaan atau butuh bantuan, tim customer service kami siap membantu. Hubungi kami melalui:</p>
                <p>
                    📧 Email: <a href='mailto:info@urbanhype.com'>info@urbanhype.com</a><br>
                    📞 WhatsApp: <a href='https://wa.me/6281234567890'>+62 812 3456 7890</a><br>
                    🕒 Jam Operasional: Senin - Jumat (09:00 - 17:00)
                </p>
                
                <p style='margin-top: 25px;'>
                    Salam hangat,<br>
                    <strong>Tim URBANHYPE</strong><br>
                    <em>Your Fashion, Your Style</em>
                </p>
            </div>
            
            <!-- FOOTER -->
            <div class='footer'>
                <div class='social-links'>
                    <a href='https://facebook.com/urbanhype' title='Facebook'>f</a>
                    <a href='https://instagram.com/urbanhype' title='Instagram'>📷</a>
                    <a href='https://twitter.com/urbanhype' title='Twitter'>𝕏</a>
                    <a href='https://tiktok.com/@urbanhype' title='TikTok'>♪</a>
                </div>
                
                <p style='margin-top: 20px;'>
                    <a href='https://urbanhype.com'>Website</a> | 
                    <a href='#'>FAQ</a> | 
                    <a href='#'>Kebijakan Privasi</a> | 
                    <a href='#'>Syarat &amp; Ketentuan</a>
                </p>
                
                <p class='footer-note'>
                    © 2025 URBANHYPE. Semua hak dilindungi.<br>
                    Anda menerima email ini karena telah subscribe ke newsletter kami.<br>
                    <a href='#' style='color: #1E5DAC;'>Unsubscribe dari mailing list</a>
                </p>
            </div>
        </div>
    </body>
    </html>
    ";

    $mail->Body = $htmlBody;

    // Plain text alternative (untuk email clients yang tidak support HTML)
    $mail->AltBody = strip_tags($htmlBody);

    // ============ SEND EMAIL ============
    if ($mail->send()) {
        // Email berhasil dikirim

        // OPTIONAL: Simpan ke database
        include 'admin/koneksi.php';
        $email_db = mysqli_real_escape_string($koneksi, $email);
        $subscribe_date = date('Y-m-d H:i:s');
        $query = "INSERT INTO subscribers (email, subscribed_at) VALUES ('$email_db', '$subscribe_date')
                  ON DUPLICATE KEY UPDATE subscribed_at='$subscribe_date'";
        mysqli_query($koneksi, $query);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Email konfirmasi berhasil dikirim! Cek inbox Anda.'
        ]);
    } else {
        throw new Exception('Email gagal dikirim: ' . $mail->ErrorInfo);
    }
} catch (Exception $e) {
    // Log error
    error_log("PHPMailer Error pada " . date('Y-m-d H:i:s') . ": " . $e->getMessage());

    // Response error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memproses subscribe. Coba lagi nanti.'
    ]);
}

exit;
