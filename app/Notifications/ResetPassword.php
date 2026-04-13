<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notification gửi email đặt lại mật khẩu.
 *
 * Kế thừa từ notification gốc của Laravel và override phương thức `toMail`
 * để sử dụng template email tùy chỉnh (`mail.reset-password`).
 * Template nhận URL reset password và thông tin user để cá nhân hóa nội dung.
 *
 * @see \Illuminate\Auth\Notifications\ResetPassword
 * @see \App\Actions\Fortify\ResetUserPassword
 */
class ResetPassword extends \Illuminate\Auth\Notifications\ResetPassword
{

    /**
     * Tạo nội dung email thông báo đặt lại mật khẩu.
     *
     * Sử dụng Markdown template `mail.reset-password` với hai biến:
     * - url: đường dẫn reset password chứa token hợp lệ
     * - user: đối tượng notifiable (user nhận email) để cá nhân hóa nội dung
     *
     * @param  mixed  $notifiable  User nhận thông báo
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->markdown('mail.reset-password', [
                'url' => $this->resetUrl($notifiable),
                'user' => $notifiable,
            ]);
    }

}
