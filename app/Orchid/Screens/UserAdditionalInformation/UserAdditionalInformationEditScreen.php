<?php

namespace App\Orchid\Screens\UserAdditionalInformation;

use App\Orchid\Screens\BaseEditScreen;

/**
 * Màn hình tạo mới và chỉnh sửa UserAdditionalInformation trong Admin Panel.
 *
 * Kế thừa từ BaseEditScreen — tự động sinh form từ cấu trúc bảng `user_additional_informations`.
 * Yêu cầu permission `platform.systems.user_additional_informations`.
 *
 * @see \App\Orchid\Screens\BaseEditScreen
 * @satisfies Requirements 11.3, 11.4
 */
class UserAdditionalInformationEditScreen extends BaseEditScreen {}
