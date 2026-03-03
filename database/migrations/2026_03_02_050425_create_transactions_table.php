<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // Core Fields
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_identifier', 255)->nullable()->comment('User identifier cho guest checkout (email/phone)');
            $table->unsignedBigInteger('amount')->comment('Số tiền giao dịch (chưa trừ phí)');
            $table->unsignedBigInteger('fee')->default(0)->comment('Phí giao dịch (từ payment gateway)');
            $table->unsignedBigInteger('net_amount')->nullable()->comment('Số tiền thực nhận sau khi trừ phí (amount - fee)');
            $table->string('currency', 3)->default('VND')->comment('Loại tiền tệ');

            // Transaction Info
            $table->string('transaction_no', 100)->unique()->comment('Mã giao dịch nội bộ');
            $table->string('gateway_transaction_id', 255)->nullable()->comment('Transaction ID từ payment gateway');
            $table->string('bank_code', 50)->nullable()->comment('Mã ngân hàng (cho ATM/Credit Card)');
            $table->string('status', 30)->default('PENDING')->comment('Trạng thái giao dịch');
            $table->string('payment_method', 30)->comment('Phương thức thanh toán');
            $table->string('response_code', 20)->nullable()->comment('Response code từ gateway');
            $table->text('order_info')->nullable()->comment('Thông tin đơn hàng / mô tả giao dịch');
            $table->timestamp('pay_date')->nullable()->comment('Thời gian thanh toán');
            $table->string('account_number', 50)->nullable()->comment('Số tài khoản (cho bank transfer)');

            // Failure & Refund Info
            $table->text('failure_reason')->nullable()->comment('Lý do thất bại');
            $table->unsignedBigInteger('refunded_amount')->default(0)->comment('Số tiền đã hoàn (cho refund)');
            $table->timestamp('refunded_at')->nullable()->comment('Thời gian hoàn tiền');
            $table->text('refund_reason')->nullable()->comment('Lý do hoàn tiền');

            // Expiration
            $table->timestamp('expires_at')->nullable()->comment('Thời gian hết hạn giao dịch');

            // Metadata
            $table->json('metadata')->nullable()->comment('Custom metadata');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
