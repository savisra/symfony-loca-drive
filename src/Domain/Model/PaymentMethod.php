<?php

namespace App\Domain\Model;

enum PaymentMethod: string 
{
    case CARD = "card";
    case PAYPAL = "paypal";
    case STRIPE = "stripe";  
}