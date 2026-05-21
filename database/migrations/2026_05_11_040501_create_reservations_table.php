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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->string('reservation_code')->unique()->nullable();
            $table->string('customer_ktp_card')->nullable();
            $table->date('start_date');
            $table->integer('duration_month');
            $table->decimal('room_price', 12, 2);
            $table->decimal('admin_fee', 12, 2)->default(0);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            $table->enum('status', [
                        'pending',
                        'waiting_payment',
                        'payment_uploaded',
                        'approved',
                        'rejected',
                        'cancelled',
                        'expired'
                    ])->default('pending');
            $table->timestamp('payment_due_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
