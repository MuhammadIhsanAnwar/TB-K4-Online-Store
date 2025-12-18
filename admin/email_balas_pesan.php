<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

/**
 * Fungsi untuk mengirim email balasan pesan
 * @param string $email_tujuan - Email penerima
 * @param string $nama_penerima - Nama penerima
 * @param string $subject - Subject email
 * @param string $pesan - Isi pesan balasan
 * @param string $pesan_original - Pesan original dari user (opsional)
 * @return array - Array dengan status dan message
 */
function kirimEmailBalasan($email_tujuan, $nama_penerima, $subject, $pesan, $pesan_original = '')
{
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi SMTP (sama dengan proses_kirim_reset.php)
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = "urbanhype.neoverse.my.id";
        $mail->SMTPAuth   = true;
        $mail->Username   = "helpdesk@urbanhype.neoverse.my.id";
        $mail->Password   = "administrator-online-store";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Set From dan To
        $mail->setFrom("helpdesk@urbanhype.neoverse.my.id", "URBANHYPE - Balasan Pesan");
        $mail->addAddress($email_tujuan, $nama_penerima);

        // Atur format HTML
        $mail->isHTML(true);
        $mail->Subject = "Balasan: " . $subject;

        // Template HTML Email dengan pesan original
        $pesan_original_html = '';
        if (!empty($pesan_original)) {
            $pesan_original_html = "
            <div class='divider'></div>
            
            <div class='original-message-section'>
                <h3 style='color: #1E5DAC; margin-top: 30px; margin-bottom: 15px;'>📨 Pesan Anda Sebelumnya:</h3>
                <div class='original-message-box'>
                    " . nl2br(htmlspecialchars($pesan_original)) . "
                </div>
            </div>
            ";
        }

        $mail->Body = "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background-color: #f5f5f5;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 20px auto;
                    background-color: white;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    overflow: hidden;
                }
                .header {
                    background: linear-gradient(135deg, #1E5DAC 0%, #1a4d8f 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 700;
                }
                .header p {
                    margin: 10px 0 0 0;
                    font-size: 14px;
                    opacity: 0.9;
                }
                .content {
                    padding: 30px;
                }
                .greeting {
                    color: #333;
                    font-size: 16px;
                    margin-bottom: 20px;
                }
                .message-box {
                    background-color: #f8f9fa;
                    border-left: 4px solid #1E5DAC;
                    padding: 20px;
                    margin: 20px 0;
                    border-radius: 5px;
                    line-height: 1.6;
                    color: #333;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                }
                .original-message-box {
                    background-color: #f0f7ff;
                    border-left: 4px solid #B7C5DA;
                    padding: 20px;
                    margin: 15px 0;
                    border-radius: 5px;
                    line-height: 1.6;
                    color: #555;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                    font-size: 14px;
                }
                .original-message-section h3 {
                    font-size: 14px;
                    color: #1E5DAC;
                }
                .footer {
                    background-color: #f5f5f5;
                    padding: 20px;
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                    border-top: 1px solid #ddd;
                }
                .footer a {
                    color: #1E5DAC;
                    text-decoration: none;
                }
                .divider {
                    height: 1px;
                    background-color: #ddd;
                    margin: 30px 0;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🛍️ URBANHYPE</h1>
                    <p>Balasan atas Pertanyaan Anda</p>
                </div>
                
                <div class='content'>
                    <div class='greeting'>
                        Halo <strong>" . htmlspecialchars($nama_penerima) . "</strong>,
                    </div>
                    
                    <p>Terima kasih telah menghubungi kami. Berikut adalah balasan atas pertanyaan/pesan Anda:</p>
                    
                    <div class='message-box'>
                        <strong>📝 Balasan dari Tim Support:</strong><br><br>
                        " . nl2br(htmlspecialchars($pesan)) . "
                    </div>
                    
                    <p>Jika Anda memiliki pertanyaan lebih lanjut, jangan ragu untuk menghubungi kami kembali.</p>
                    
                    " . $pesan_original_html . "
                    
                    <div class='divider'></div>
                    
                    <p style='color: #666; font-size: 14px;'>
                        <strong>Informasi Kontak:</strong><br>
                        Email: helpdesk@urbanhype.neoverse.my.id<br>
                        Website: urbanhype.neoverse.my.id<br>
                        Jam Kerja: Senin - Jumat, 09:00 - 17:00 WIB
                    </p>
                </div>
                
                <div class='footer'>
                    <p>&copy; 2025 UrbanHype. All Right Reserved.</p>
                    <p>Email ini dikirim otomatis oleh sistem kami.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        // Kirim email
        $mail->send();
        return [
            'status' => true,
            'message' => 'Email berhasil dikirim'
        ];
    } catch (Exception $e) {
        return [
            'status' => false,
            'message' => 'Error: ' . $mail->ErrorInfo
        ];
    }
}
