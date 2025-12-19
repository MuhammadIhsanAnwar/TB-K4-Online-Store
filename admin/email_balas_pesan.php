<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

function kirimEmailBalasan($email_tujuan, $nama_penerima, $subject, $pesan, $pesan_original = '')
{
    $mail = new PHPMailer(true);

    try {
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

        $mail->setFrom("helpdesk@urbanhype.neoverse.my.id", "URBANHYPE - Balasan Pesan");
        $mail->addAddress($email_tujuan, $nama_penerima);

        $mail->isHTML(true);
        $mail->Subject = "Balasan: " . $subject;

        $pesan_original_html = '';
        if (!empty($pesan_original)) {
            $pesan_original_html = "
            <div class='divider'></div>
            <div class='original-message-section'>
                <h3 style='color: #1E5DAC; margin-top: 30px; margin-bottom: 15px;'>
                    <i class='bi bi-envelope-paper'></i> Pesan Anda Sebelumnya:
                </h3>
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
            <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css' rel='stylesheet'>
            <link rel='stylesheet' href='css_admin/email_balas_pesan_style.css'>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1><i class='bi bi-bag-fill'></i> URBANHYPE</h1>
                    <p>Balasan atas Pertanyaan Anda</p>
                </div>

                <div class='content'>
                    <div class='greeting'>
                        Halo <strong>" . htmlspecialchars($nama_penerima) . "</strong>,
                    </div>

                    <p>Terima kasih telah menghubungi kami. Berikut adalah balasan atas pertanyaan/pesan Anda:</p>

                    <div class='message-box'>
                        <strong>
                            <i class='bi bi-chat-left-text-fill'></i> Balasan dari Tim Support:
                        </strong><br><br>
                        " . nl2br(htmlspecialchars($pesan)) . "
                    </div>

                    <p>Jika Anda memiliki pertanyaan lebih lanjut, jangan ragu untuk menghubungi kami kembali.</p>

                    " . $pesan_original_html . "

                    <div class='divider'></div>

                    <p style='color: #666; font-size: 14px;'>
                        <strong>Informasi Kontak:</strong><br>
                        <i class='bi bi-envelope-fill'></i> Email: helpdesk@urbanhype.neoverse.my.id<br>
                        <i class='bi bi-globe'></i> Website: urbanhype.neoverse.my.id<br>
                        <i class='bi bi-clock-fill'></i> Jam Kerja: Senin - Jumat, 09:00 - 17:00 WIB
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
