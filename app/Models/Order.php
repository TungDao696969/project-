<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'user_id', 'total_price', 'status', 'payment_id', 'coupon_id',
        'shipping_address', 'shipping_fee', 'tax'
    ];

    // Định nghĩa các trạng thái
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Các trạng thái có thể chuyển đổi
    public static $statusTransitions = [
        self::STATUS_PENDING => [self::STATUS_PROCESSING, self::STATUS_CANCELLED],
        self::STATUS_PROCESSING => [self::STATUS_SHIPPED, self::STATUS_CANCELLED],
        self::STATUS_SHIPPED => [self::STATUS_COMPLETED],
        self::STATUS_COMPLETED => [], // Không thể thay đổi
        self::STATUS_CANCELLED => [], // Không thể thay đổi
    ];
    // Quan hệ với bảng order_details
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Quan hệ với bảng users
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với bảng payments
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Quan hệ với bảng coupons
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

// Kiểm tra trạng thái có thể chuyển đổi hay không
public function canTransitionTo($newStatus)
{
    return in_array($newStatus, self::$statusTransitions[$this->status] ?? []);
}
}
