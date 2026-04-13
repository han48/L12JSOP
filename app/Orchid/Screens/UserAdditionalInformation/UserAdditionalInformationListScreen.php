<?php

namespace App\Orchid\Screens\UserAdditionalInformation;

use App\Orchid\Screens\BaseListScreen;

/**
 * Màn hình danh sách UserAdditionalInformation trong Admin Panel.
 *
 * Kế thừa từ BaseListScreen — hiển thị danh sách phân trang UserAdditionalInformation.
 * Yêu cầu permission `platform.systems.user_additional_informations`.
 *
 * @see \App\Orchid\Screens\BaseListScreen
 * @satisfies Requirements 11.3, 11.4
 */
class UserAdditionalInformationListScreen extends BaseListScreen {}
