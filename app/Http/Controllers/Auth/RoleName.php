<?php

namespace App\Http\Controllers\Auth;

enum RoleName: string
{
	case ADMIN = 'Администратор';
	case EMPLOYER = 'Работодатель';
	case SCHOOL = 'Учебное заведение';
	case TRAINEE = 'Практикант';
}