<?php

namespace App\Notifications;

use \Illuminate\Notifications\Messages\MailMessage;

/**
 * Notification gửi email xác minh địa chỉ email.
 *
 * Kế thừa từ notification gốc của Laravel và override phương thức `toMail`
 * để sử dụng template email tùy chỉnh (`mail.verify-password`).
 * Template nhận URL xác minh và thông tin user để cá nhân hóa nội dung.
 *
 * Được gửi khi user đăng ký mới hoặc thay đổi địa chỉ email.
 *
 * @see \Illuminate\Auth\Notifications\VerifyEmail
 * @see \App\Actions\Fortify\UpdateUserProfileInformation
 */
class VerifyEmail extends \Illuminate\Auth\Notifications\VerifyEmail
{

    /**
     * Tạo nội dung email thông báo xác minh địa chỉ email.
     *
     * Sử dụng Markdown template `mail.verify-password` với hai biến:
     * - url: đường dẫn xác minh email có chứa chữ ký hợp lệ
     * - user: đối tượng notifiable (user nhận email) để cá nhân hóa nội dung
     *
     * @param  mixed  $notifiable  User nhận thông báo
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->markdown('mail.verify-password', [
                'url' => $this->verificationUrl($notifiable),
                'user' => $notifiable,
            ]);
    }

}
