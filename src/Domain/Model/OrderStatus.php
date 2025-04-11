<?php

namespace App\Domain\Model;

enum OrderStatus: string
{
    case CART = 'cart';
    case PAYMENT_METHOD_SET = 'payment_method_set';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
}