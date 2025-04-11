<?php

namespace App\Domain\Model;

enum OrderStatus: string
{
    case CART = 'cart';
    case PAID = 'paid';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}