<?php

namespace App\Orchid\Layouts\Transaction;

use App\Orchid\Layouts\BaseListLayout;

/**
 * List layout for the Transaction management screen in the Orchid admin panel.
 *
 * Inherits all column discovery, filtering, sorting, and action buttons
 * from BaseListLayout. The target data key is automatically resolved to
 * "transactions" and the model class to App\Models\Transaction.
 *
 * Displayed on: Admin Panel → Management → Transactions (TransactionListScreen).
 * Required permission: platform.systems.transactions
 *
 * @see \App\Orchid\Layouts\BaseListLayout
 * @see \App\Orchid\Screens\Transaction\TransactionListScreen
 *
 * Satisfies: Requirements 10.3, 14.1
 */
class TransactionListLayout extends BaseListLayout {}
