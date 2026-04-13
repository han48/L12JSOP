<?php

namespace App\Http\Controllers\Api;

/**
 * BaseController (API namespace) — Alias của `\App\Http\Controllers\BaseController`
 * trong namespace `App\Http\Controllers\Api`.
 *
 * Class này kế thừa toàn bộ logic từ controller gốc mà không override bất kỳ method nào.
 * Mục đích duy nhất là cho phép các API controllers (PostController, ProductController,
 * TransactionController, v.v.) trong namespace `Api` kế thừa từ đúng namespace của mình
 * thay vì phải tham chiếu đầy đủ đến `\App\Http\Controllers\BaseController`.
 *
 * Tất cả hành vi (index, show, recommendations, store, update, destroy) được định nghĩa
 * hoàn toàn trong class cha.
 *
 * @see \App\Http\Controllers\BaseController  Class cha chứa toàn bộ logic
 */
class BaseController extends \App\Http\Controllers\BaseController {}
