// app/Enums/Role.php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case DOCTOR = 'doctor';
    case PATIENT = 'patient';
    case CUSTOMER = 'customer';
}